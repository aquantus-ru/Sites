<?php namespace Jules\MdPages\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use File;
use Input;
use Flash;
use Backend;
use Request;
use ApplicationException;

class Manager extends Controller
{
    public $requiredPermissions = ['jules.mdpages.*'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Jules.MdPages', 'mdpages', 'manager');
    }

    public function index()
    {
        $this->pageTitle = 'Manage Pages';
        $this->vars['files'] = $this->getFileList();
    }

    public function update()
    {
        $path = Input::get('path');

        if (!$path) {
             return Backend::redirect('jules/mdpages/manager');
        }

        $fullPath = base_path('pages/' . $path);

        // Security check
        if (strpos($path, '..') !== false || !File::exists($fullPath)) {
            Flash::error('File not found or invalid path');
            return Backend::redirect('jules/mdpages/manager');
        }

        $this->vars['path'] = $path;
        $this->vars['content'] = File::get($fullPath);
        $this->pageTitle = 'Edit Page: ' . $path;
    }

    public function onSave()
    {
        $path = Input::get('path');
        $content = Input::get('content');

        $fullPath = base_path('pages/' . $path);

        if (strpos($path, '..') !== false) {
             throw new ApplicationException('Invalid path');
        }

        // Allow creating new files if we were to implement create, but for update:
        // We'll just overwrite.

        // Create dir if not exists (unlikely for existing file but good practice)
        $dir = dirname($fullPath);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        File::put($fullPath, $content);
        Flash::success('Saved successfully.');
    }

    protected function getFileList()
    {
        // Recursive scan
        $files = [];
        $dir = base_path('pages');
        if (!File::exists($dir)) return [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            if ($file->getExtension() !== 'md') continue;

            // Normalize path separators
            $relativePath = str_replace(base_path('pages') . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);

            $files[] = $relativePath;
        }
        sort($files);
        return $files;
    }
}
