<?php

/*
 * call the framework
 *
 */
require get_template_directory() . '/includes/autoload.php';

add_action('init', function () {
    if( function_exists('acf_add_options_page') ) {
        $parent = acf_add_options_page(array(
            'page_title' 	=> 'Configuration',
            'menu_title' 	=> 'Configuration',
            'redirect' 		=> true
        ));

        acf_add_options_sub_page(array(
            'page_title' 	=> 'Limites',
            'menu_title' 	=> 'Limites',
            'parent_slug' 	=> $parent['menu_slug'],
        ));

        acf_add_options_sub_page(array(
            'page_title' 	=> 'Mails',
            'menu_title' 	=> 'Mails',
            'parent_slug' 	=> $parent['menu_slug'],
        ));

        acf_add_options_sub_page(array(
            'page_title' 	=> 'Expiration',
            'menu_title' 	=> 'Expiration',
            'parent_slug' 	=> $parent['menu_slug'],
        ));

        acf_add_options_sub_page(array(
            'page_title' 	=> 'Publicité géante',
            'menu_title' 	=> 'Publicité géante',
            'parent_slug' 	=> $parent['menu_slug'],
        ));
    }
});
