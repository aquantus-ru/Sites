<?php namespace Jules\MdPages\Components;

use Cms\Classes\ComponentBase;
use Request;

class MdMenu extends ComponentBase
{
    public $menuTree;

    public function componentDetails()
    {
        return [
            'name'        => 'MD Menu',
            'description' => 'Displays a menu of MD pages.'
        ];
    }

    public function onRun()
    {
        $this->menuTree = $this->buildMenuTree(base_path('pages'));

        // Add root index if it exists
        if (file_exists(base_path('pages/index.md'))) {
            array_unshift($this->menuTree, [
                'title' => 'Home',
                'url' => '/docs',
                'isActive' => Request::is('docs') || Request::path() == 'docs'
            ]);
        }
    }

    protected function buildMenuTree($dir, $prefix = '')
    {
        if (!is_dir($dir)) {
            return [];
        }

        $items = [];
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;

            $path = $dir . '/' . $file;
            // Build the relative URL slug
            $slugPart = $file;
            if (str_ends_with($file, '.md')) {
                $slugPart = substr($file, 0, -3);
            }

            $relativeUrl = $prefix . ($prefix ? '/' : '') . $slugPart;

            if (is_dir($path)) {
                // Check if index.md exists in the folder
                $hasIndex = file_exists($path . '/index.md');
                $url = $hasIndex ? '/docs/' . $prefix . ($prefix ? '/' : '') . $file : '#';

                $children = $this->buildMenuTree($path, $prefix . ($prefix ? '/' : '') . $file);

                // Only show folder if it has content or children
                if (!empty($children) || $hasIndex) {
                    $items[] = [
                        'title' => $this->formatTitle($file),
                        'url' => $url,
                        'children' => $children,
                        'isFolder' => true,
                        // Active if current path starts with this folder's path
                        'isActive' => Request::is('docs/' . $prefix . ($prefix ? '/' : '') . $file) || Request::is('docs/' . $prefix . ($prefix ? '/' : '') . $file . '/*')
                    ];
                }
            } elseif (str_ends_with($file, '.md')) {
                // Skip index.md as it is associated with the parent folder
                if ($file == 'index.md') continue;

                $items[] = [
                    'title' => $this->formatTitle($slugPart),
                    'url' => '/docs/' . $relativeUrl,
                    'isActive' => Request::is('docs/' . $relativeUrl)
                ];
            }
        }

        // Sort items alphabetically
        usort($items, function($a, $b) {
            return strcasecmp($a['title'], $b['title']);
        });

        return $items;
    }

    protected function formatTitle($name)
    {
        return ucwords(str_replace(['-', '_'], ' ', $name));
    }
}
