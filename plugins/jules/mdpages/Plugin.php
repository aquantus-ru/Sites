<?php namespace Jules\MdPages;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'MdPages',
            'description' => 'Renders Markdown pages from a directory.',
            'author'      => 'Jules',
            'icon'        => 'icon-file-text-o'
        ];
    }

    public function registerComponents()
    {
        return [
            'Jules\MdPages\Components\MdPage' => 'mdPage',
            'Jules\MdPages\Components\MdMenu' => 'mdMenu',
        ];
    }

    public function registerNavigation()
    {
        return [
            'mdpages' => [
                'label'       => 'MD Pages',
                'url'         => \Backend::url('jules/mdpages/manager'),
                'icon'        => 'icon-file-text',
                'permissions' => ['jules.mdpages.*'],
                'order'       => 500,
            ],
        ];
    }
}
