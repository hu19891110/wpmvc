<?php

namespace WPMVC\Common;

// Import namespaces
use WPMVC\Models\View;

/**
 * Core WPMVC Bootstrap
 */
class WPMVC
{

    /**
     * Initializes the WordPress theme
     *
     * @access public
     * @param string $rootDir (default: "")
     * @return void
     */
    public function __construct($rootDir = "")
    {
        // Define default constants
        define('WP_HOME_URL', get_home_url());
        define('WP_SITENAME', get_bloginfo('name'));
        define('WP_AJAX_URL', admin_url('admin-ajax.php'));
        define('TEMPLATEDIR', str_replace(WP_CONTENT_DIR, '', WP_CONTENT_URL . TEMPLATEPATH));
        define('STYLESHEET_URL', get_stylesheet_directory_uri());
        define('TEMPLATE_URL', get_template_directory_uri());
        define('RSS2_URL', get_bloginfo('rss2_url'));

        // Composer autoload
        if (file_exists(STYLESHEETPATH . '/vendor/autoload.php')) {
            require_once(STYLESHEETPATH . '/vendor/autoload.php');
        }

        // Auto-load hook files
        $hooks = glob(TEMPLATEPATH . '/app/hooks/*');
        if ($hooks && count($hooks)) {
            foreach ($hooks as $hook) {
                if (file_exists($hook) && is_file($hook)) { require_once($hook); }
            }
        }
    }

    /** 
     * Begin the routing
     *
     * @access public
     * @return void
     */
    public function init()
    {
        // Create a new view and set the default path as the current path
        $theHeader = new View(TEMPLATEPATH . '/core/views/');
        $theBody = new View(TEMPLATEPATH . '/app/views/');
        $theFooter = new View(TEMPLATEPATH . '/core/views/');

        // Set the header view
        $theHeader->setFile('header');
        // Set the footer view
        $theFooter->setFile('footer');

        // If request for homepage
        if (is_home()) {
            $theBody->setFile('home');
        } else {
            // Retrieve the current requested post type (applies to pages, and post single and archive views)
            $postType = get_post_type();
            if (is_404()) {
                // 404 view
                $theBody->setFile('404');
            } elseif (is_search()) {
                // Search index
                $theBody->setFile('search/index');
            } elseif (is_tax()) {
                // Taxonomy archive
                $taxonomy = get_query_var('taxonomy');
                $theBody->setFile(sprintf('taxonomy/%s/index', $taxonomy));
            } elseif (is_tag()) {
                // Tag archive
                $theBody->setFile('views/tag/index');
            } elseif (is_post_type_archive()) {
                // Post type archive
                $theBody->setFile(sprintf('%s/index', $postType));
            } elseif (is_single()) {
                // Post permalink
                $theBody->setFile(sprintf('%s/single', $postType));
            } elseif (is_page()) {
                global $pagename;
                // Page permalink
                $theBody->setFile($pagename);
            }
        }

        echo $theHeader->output();
        echo $theBody->output();
        echo $theFooter->output();
    }

}