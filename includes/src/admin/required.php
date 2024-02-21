<?php

namespace Brikk\Includes\Src\Admin;

class Required {

    use \Brikk\Includes\Src\Traits\Singleton;

    function __construct() {

        $this->include_tgm();
        $this->register();

    }

    protected function include_tgm() {

        include BK_PATH . 'includes/lib/tgm-plugin-activation/class-tgm-plugin-activation.php';

    }

    protected function register() {

        add_action( 'tgmpa_register', [ $this, 'register_required_plugins' ] );

    }

    public function register_required_plugins() {

    	/*
    	 * Array of plugin arrays. Required keys are name and slug.
    	 * If the source is NOT from the .org repo, then source is also required.
    	 */
    	$plugins = [

    		[
                'name' => 'Routiz',
    			'slug' => 'routiz',
    			'source' => BK_PATH . 'includes/plugins/routiz.zip',
    			'required' => true,
    			'version' => '1.5.0.7',
    			'force_activation' => false,
    			'force_deactivation' => false,
    			'external_url' => '',
    			'is_callable' => '',
            ],

    		[
                'name' => 'Brikk Utilities',
    			'slug' => 'brikk-utilities',
    			'source' => BK_PATH . 'includes/plugins/brikk-utilities.zip',
    			'required' => true,
    			'version' => '1.4.0.6',
    			'force_activation' => false,
    			'force_deactivation' => false,
    			'external_url' => '',
    			'is_callable' => '',
            ],
            [
                'name' => 'Elementor',
    			'slug' => 'elementor',
    			'required' => true,
            ],
    		[
                'name' => 'WooCommerce',
    			'slug' => 'woocommerce',
    			'required' => false,
            ],
    		[
                'name' => 'Contact Form 7',
    			'slug' => 'contact-form-7',
    			'required' => false,
            ]

    	];

    	/*
    	 * array of configuration settings
         *
    	 */
    	$config = [
    		'id' => 'brikk',
    		'default_path' => '',
    		'menu' => 'tgmpa-install-plugins',
    		'has_notices' => true,
    		'dismissable' => true,
    		'dismiss_msg' => '',
    		'is_automatic' => false,
    		'message' => '',
    	];

    	tgmpa( $plugins, $config );

    }

}
