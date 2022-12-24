<?php
/*
 * Plugin Name: CF7 Views
 * Plugin URI: https://cf7views.com
 * Description: Display Contact Form 7 Submissions in frontend.
 * Version: 2.1
 * Author: WebHolics
 * Author URI: https://webholics.org
 * Text Domain: cf7-views
 *
 * Copyright 2020.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( "CF7_VIEWS_URL", plugins_url() . "/" . basename( dirname( __FILE__ ) ) );
define( "CF7_VIEWS_DIR_URL", WP_PLUGIN_DIR . "/" . basename( dirname( __FILE__ ) ) );

function cf7_views_load_plugin_textdomain() {
    load_plugin_textdomain( 'cf7-views', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'cf7_views_load_plugin_textdomain' );


require_once CF7_VIEWS_DIR_URL . '/inc/helpers.php';

//Backend
require_once CF7_VIEWS_DIR_URL . '/inc/admin/class-cf7-views-posttype.php';
require_once CF7_VIEWS_DIR_URL . '/inc/admin/class-cf7-views-metabox.php';
require_once CF7_VIEWS_DIR_URL . '/inc/admin/class-cf7-views-ajax.php';
require_once CF7_VIEWS_DIR_URL . '/inc/admin/review/class-cf7-views-review.php';
require_once CF7_VIEWS_DIR_URL . '/inc/admin/class-cf7-views-upgrade-to-pro-page.php';
//Frontend
require_once CF7_VIEWS_DIR_URL . '/inc/pagination.php';
require_once CF7_VIEWS_DIR_URL . '/inc/class-cf7-views-shortcode.php';

add_action( 'admin_enqueue_scripts', 'cf7_views_admin_scripts' );

add_action( 'wp_enqueue_scripts', 'cf7_views_frontend_scripts' );

function cf7_views_admin_scripts( $hook ) {
	global $post;
	if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
		if ( 'cf7-views' === $post->post_type ) {

			wp_enqueue_style( 'fontawesome', CF7_VIEWS_URL .'/assets/css/font-awesome.css' );
			wp_enqueue_style( 'pure-css', CF7_VIEWS_URL .'/assets/css/pure-min.css' );
			wp_enqueue_style( 'pure-grid-css', CF7_VIEWS_URL .'/assets/css/grids-responsive-min.css' );
			wp_enqueue_style( 'cf7-views-admin', CF7_VIEWS_URL . '/assets/css/cf7-views-admin.css' );


			$js_dir    = CF7_VIEWS_DIR_URL . '/build/static/js';
			$js_files = array_diff( scandir( $js_dir ), array( '..', '.' ) );
			$count = 0;
			foreach ( $js_files as $js_file ) {
				if ( strpos( $js_file , '.js.map'  )  === false  ) {
					$js_file_name = $js_file;
					wp_enqueue_script( 'cf7_views_script' . $count, CF7_VIEWS_URL . '/build/static/js/' . $js_file_name, array( 'jquery' ), '', true );
					$count++;
					// wp_localize_script( 'react_grid_script'.$count, 'formData' , $form_data );
				}
			}

			$css_dir    = CF7_VIEWS_DIR_URL . '/build/static/css';
			$css_files = array_diff( scandir( $css_dir ), array( '..', '.' ) );

			foreach ( $css_files as $css_file ) {
				if ( strpos( $css_file , '.css.map'  ) === false ) {
					$css_file_name = $css_file;
				}
			}
			// $grid_options = get_option( 'gf_stla_form_id_grid_layout_4');
			wp_enqueue_style( 'cf7_views_style', CF7_VIEWS_URL . '/build/static/css/' . $css_file_name );
		}
	}
}

function cf7_views_frontend_scripts() {
	wp_enqueue_style( 'pure-css', CF7_VIEWS_URL .'/assets/css/pure-min.css' );
	wp_enqueue_style( 'pure-grid-css', CF7_VIEWS_URL .'/assets/css/grids-responsive-min.css' );
	wp_enqueue_style( 'cf7-views-front', CF7_VIEWS_URL . '/assets/css/cf7-views-display.css' );
}
