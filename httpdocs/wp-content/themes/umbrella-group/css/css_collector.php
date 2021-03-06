<?php

class umbrella_customization
{
    private $per_pages = [
        [
            "url" => "/price/",
            "css" => "price.css",
            "js" => "price.js"
        ],
        [
            "url" => "/faq/",
            "js" => "faq.js"
        ],
        [
            "id" => "2",
            "css" => "main.css"
        ]

    ];
    private $css_files = [
        "main.css", "styles-intelsib.css", "tablets-and-down.css"
    ];
    private $js_files = [
        "chat.js", "main.js"
    ];

    public function get_umbrella_custom_css()
    {
        $styles = '<style>';
        foreach ($this->css_files as $css_file) {
            $styles .= file_get_contents(get_theme_file_path() . '/assets/css/' . $css_file);
        }
        foreach ($this->per_pages as $page) {
            if (isset($page['url']) && str_contains(get_permalink(), $page['url'])) {
                $styles .= umbrella_filter_ab_tests(file_get_contents(get_theme_file_path() . '/assets/css/pages/' . $page['css']));
            }
            if (array_key_exists('id', $page) && get_the_ID() == $page['id']) {
                $styles .= umbrella_filter_ab_tests(file_get_contents(get_theme_file_path() . '/assets/css/pages/' . $page['css']));
            }
        }
        $styles .= $this->get_styles_from_cache_files();
        $styles .= $this->get_umbrella_styles_from_cache();
        $styles .= '</style>';
//        $styles = flatsome_minify_css($styles);
        echo $styles;
    }

    public function get_umbrella_custom_scripts()
    {
        $scripts = '<script>';
        foreach ($this->js_files as $js_file) {
            if ($js_file != 'chat.js' || !str_contains(get_permalink(), 'blog')) {
                $scripts .= file_get_contents(get_theme_file_path() . '/assets/js/' . $js_file);
            }
        }
        foreach ($this->per_pages as $page) {
            if (isset($page['url']) && str_contains(get_permalink(), $page['url'])) {
                if (strlen($page['js'] > 0)) {
                    $scripts .= file_get_contents(get_theme_file_path() . '/assets/js/pages/' . $page['js']);
                }
            }
        }
        $scripts .= $this->get_scripts_from_cache_files();
        $scripts .= '</script>';
        echo $scripts;
    }
    private function get_umbrella_styles_from_cache()
    {
        $value = wp_cache_get('umbrella_custom_css');
        $content = '';
        if (!empty($value) && $value != false) {
            return $value;
        }
        return '';
    }

    private function get_styles_from_cache_files()
    {
        $value = wp_cache_get('umbrella_custom_css_files');
        $content = '';
        if (is_array($value)) {
            foreach ($value as $file) {
                $content .= file_get_contents(get_theme_file_path() . $file);
            }
        }
        return $content;
    }
    private function get_scripts_from_cache_files()
    {
        $value = wp_cache_get('umbrella_custom_js_files');
        $content = '';
        if (is_array($value)) {
            foreach ($value as $file) {
                $content .= file_get_contents(get_theme_file_path() . $file);
            }
        }
        return $content;
    }
}

function umbrella_add_custom_css(string $css)
{
    $value = wp_cache_get('umbrella_custom_css');
    if (false === $value) {
        wp_cache_set('umbrella_custom_css', $css);
    } else if (is_array($value)) {
        $value .= $css;
        wp_cache_set('umbrella_custom_css', $value);
        echo($css);
    }
}
function umbrella_add_custom_js_files(array $files)
{
    $value = wp_cache_get('umbrella_custom_js_files');
    if (false === $value) {
        wp_cache_set('umbrella_custom_js_files', $files);
    } else if (is_array($value)) {
        foreach ($files as $file){
            if (!in_array($file,$value)) {
                array_push($value,$file);
                wp_cache_set('umbrella_custom_js_files', $value);
            }
        }
    }
}

function umbrella_add_custom_css_files(array $files)
{
    $value = wp_cache_get('umbrella_custom_css_files');
    if (false === $value) {
        wp_cache_set('umbrella_custom_css_files', $files);
    } else if (is_array($value)) {
        foreach ($files as $file){
            if (!in_array($file,$value)) {
                array_push($value,$file);
                wp_cache_set('umbrella_custom_css_files', $value);
            }
        }
    }
}
