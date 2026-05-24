<?php

namespace App\Helpers;

use App\Models\MenuItem;
use App\Models\CmsContent;

class MenuHelper
{
    /**
     * Get active menu items
     * Prioritizes MenuItem model over CmsContent for flexibility
     */
    public static function getMenuItems()
    {
        // Check if we have dynamic menus in database
        $dynamicMenus = MenuItem::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        if ($dynamicMenus->isNotEmpty()) {
            // Use dynamic menus from MenuItem model
            return $dynamicMenus;
        }

        // Fallback to CMS static content
        return self::getCmsMenuItems();
    }

    /**
     * Get menu items from CMS content (fallback/legacy)
     */
    private static function getCmsMenuItems()
    {
        $lang = app()->getLocale();
        $langField = 'value_' . $lang;

        $headerContents = CmsContent::where('section', 'header')->get()->keyBy('key');

        $staticMenus = [];

        // Convert CMS content to MenuItem-like structure
        $menuMappings = [
            'menu_home' => ['route' => 'home', 'icon' => 'fas fa-home'],
            'menu_about' => ['route' => 'about', 'icon' => 'fas fa-info-circle'],
            'menu_programs' => ['route' => 'programs', 'icon' => 'fas fa-graduation-cap'],
            'menu_teachers' => ['route' => 'teachers', 'icon' => 'fas fa-chalkboard-teacher'],
            'menu_statistics' => ['route' => 'statistics', 'icon' => 'fas fa-chart-bar'],
            'menu_blog' => ['route' => 'blog', 'icon' => 'fas fa-blog'],
            'menu_contact' => ['route' => 'contact', 'icon' => 'fas fa-envelope'],
        ];

        $order = 0;
        foreach ($menuMappings as $key => $config) {
            $content = $headerContents->get($key);
            if ($content) {
                $staticMenus[] = (object)[
                    'id' => null,
                    'label_uz' => $content->value_uz ?? $content->$langField ?? '',
                    'label_ru' => $content->value_ru ?? '',
                    'label_en' => $content->value_en ?? '',
                    'url' => route($config['route']),
                    'icon' => $config['icon'] ?? null,
                    'is_active' => true,
                    'open_in_new_tab' => false,
                    'order' => $order++,
                    'children' => collect([]),
                    'is_cms_fallback' => true, // Flag to identify fallback menus
                ];
            }
        }

        return collect($staticMenus);
    }

    /**
     * Render menu items as HTML
     */
    public static function renderMenuItems($menus = null)
    {
        if ($menus === null) {
            $menus = self::getMenuItems();
        }

        $lang = app()->getLocale();
        $langField = 'label_' . $lang;
        $html = '';

        foreach ($menus as $menu) {
            $label = $menu->$langField ?? $menu->label_uz;
            $url = $menu->url;
            $icon = $menu->icon ? '<i class="' . $menu->icon . ' me-1"></i>' : '';
            $target = $menu->open_in_new_tab ? ' target="_blank"' : '';
            $activeClass = request()->url() == $url ? 'active' : '';

            if ($menu->children && $menu->children->isNotEmpty()) {
                // Dropdown menu
                $html .= '<li class="nav-item dropdown">';
                $html .= '<a class="nav-link dropdown-toggle ' . $activeClass . '" href="#" role="button" data-bs-toggle="dropdown">';
                $html .= $icon . $label . '</a>';
                $html .= '<ul class="dropdown-menu">';

                foreach ($menu->children as $child) {
                    $childLabel = $child->$langField ?? $child->label_uz;
                    $childIcon = $child->icon ? '<i class="' . $child->icon . ' me-1"></i>' : '';
                    $childTarget = $child->open_in_new_tab ? ' target="_blank"' : '';

                    $html .= '<li><a class="dropdown-item" href="' . $child->url . '"' . $childTarget . '>';
                    $html .= $childIcon . $childLabel . '</a></li>';
                }

                $html .= '</ul></li>';
            } else {
                // Regular menu item
                $html .= '<li class="nav-item">';
                $html .= '<a class="nav-link ' . $activeClass . '" href="' . $url . '"' . $target . '>';
                $html .= $icon . $label . '</a>';
                $html .= '</li>';
            }
        }

        return $html;
    }

    /**
     * Check if dynamic menus are enabled
     */
    public static function isDynamicMenuEnabled()
    {
        return MenuItem::where('is_active', true)->exists();
    }
}
