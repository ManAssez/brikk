<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

/*
 * human readable dump
 *
 */
if( ! function_exists('dd') ) {
    function dd( $what = '' ) {
        print '<pre class="dump">';
        print_r( $what );
        print '</pre>';
    }
}

/*
 * shim for wp_body_open,
 * ensuring backward compatibility with versions of WordPress older than 5.2.
 *
 */
if( ! function_exists( 'wp_body_open' ) ) {
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

/*
 * define contstants
 *
 */
define( 'BK_VERSION', '1.6.0.7' );
define( 'BK_PATH', wp_normalize_path( get_template_directory() . DIRECTORY_SEPARATOR ) );
define( 'BK_URI', get_template_directory_uri() . '/' );

/*
 * autoload
 *
 */
spl_autoload_register( function( $class_name ) {
    if ( strpos( $class_name, 'Brikk' ) === false ) { return; } // check namespace

    $file_parts = explode( '\\', $class_name ); // Split the class name into an array to read the namespace and class.

    $namespace = ''; // Do a reverse loop through $file_parts to build the path to the file.
    for( $i = count( $file_parts ) - 1; $i > 0; $i-- ) {

        $current = strtolower( $file_parts[ $i ] ); // Read the current component of the file part.
        $current = str_ireplace( '_', '-', $current );

        if( count( $file_parts ) - 1 === $i ) { // If we're at the first entry, then we're at the filename.
            $file_name = "{$current}.php";
        }else{
            $namespace = '/' . $current . $namespace;
        }
    }

    $filepath  = trailingslashit( dirname( dirname( __FILE__ ) ) . $namespace ); // Now build a path to the file using mapping to the file location.
    $filepath .= $file_name;

    if( file_exists( $filepath ) ) { // If the file exists in the specified path, then include it.
        include_once( $filepath );
    }else{
        wp_die( esc_html("The file attempting to be loaded at {$filepath} does not exist.") );
    }

});

include BK_PATH . 'includes/utils/utils.php';

Brikk\Includes\Init::instance();
