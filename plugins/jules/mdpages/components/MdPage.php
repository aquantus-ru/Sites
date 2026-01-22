<?php namespace Jules\MdPages\Components;

use Cms\Classes\ComponentBase;
use Winter\Storm\Support\Facades\Markdown;
use Winter\Storm\Parse\Yaml;
use File;

class MdPage extends ComponentBase
{
    public $content;
    public $title;

    public function componentDetails()
    {
        return [
            'name'        => 'MD Page',
            'description' => 'Displays a Markdown page.'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'Slug',
                'description' => 'URL Slug',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $slug = $this->property('slug');

        // Basic directory traversal protection
        if (strpos($slug, '..') !== false) {
            $this->controller->setResponseCode(404);
            return $this->controller->run('404');
        }

        $baseDir = base_path('pages');

        if (!$slug || $slug == '/') {
            $filePath = $baseDir . '/index.md';
        } else {
             $filePath = $baseDir . '/' . $slug;
             // If directory, look for index.md
             if (is_dir($filePath)) {
                 $filePath = rtrim($filePath, '/') . '/index.md';
             } elseif (!str_ends_with($filePath, '.md')) {
                 $filePath .= '.md';
             }
        }

        if (!File::exists($filePath)) {
            $this->controller->setResponseCode(404);
            return $this->controller->run('404');
        }

        $fileContent = File::get($filePath);
        $this->parseContent($fileContent);
    }

    protected function parseContent($raw)
    {
        $config = [];
        $content = $raw;

        // Check for Front Matter (YAML)
        if (substr($raw, 0, 3) === '---') {
            $pos = strpos($raw, '---', 3);
            if ($pos !== false) {
                $yaml = substr($raw, 3, $pos - 3);
                $content = substr($raw, $pos + 3);
                try {
                    $config = (new Yaml)->parse($yaml);
                } catch (\Exception $e) {
                    // Ignore yaml parse errors
                }
            }
        }

        // Apply config to page and viewBag
        if ($config) {
            foreach ($config as $key => $value) {
                // Set on page object (for layout access)
                $this->page[$key] = $value;

                // Set on viewBag (specifically for plugins checking viewBag)
                // Note: In Winter, this.page IS often the viewBag context or merges with it.
                // But setting viewBag explicitly helps.
                $viewBag = $this->page->viewBag ?? [];
                if (!is_array($viewBag)) {
                    $viewBag = (array) $viewBag;
                }
                $viewBag[$key] = $value;
                $this->page->viewBag = $viewBag;
            }

            if (isset($config['title'])) {
                $this->title = $config['title'];
            }
        }

        $this->content = Markdown::parse(trim($content));
    }
}
