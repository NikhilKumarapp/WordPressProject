<?php

class CF7_Views_Posttype {
	private $forms = array();
	function __construct() {
		add_action( 'init', array( $this, 'create_posttype' ) );
		add_filter( 'manage_cf7-views_posts_columns' , array( $this,'add_extra_columns') );
		add_action( 'manage_cf7-views_posts_custom_column' , array( $this, 'extra_column_detail' ), 10, 2 );
	}

	function create_posttype() {

		$labels = array(
			'name'               => _x( 'CF7 Views', 'post type general name', 'cf7-views' ),
			'singular_name'      => _x( 'CF7 View', 'post type singular name', 'cf7-views' ),
			'menu_name'          => _x( 'CF7 Views', 'admin menu', 'cf7-views' ),
			'name_admin_bar'     => _x( 'CF7 Views', 'add new on admin bar', 'cf7-views' ),
			'add_new'            => _x( 'Add View', 'book', 'cf7-views' ),
			'add_new_item'       => __( 'Add New View', 'cf7-views' ),
			'new_item'           => __( 'New View', 'cf7-views' ),
			'edit_item'          => __( 'Edit View', 'cf7-views' ),
			'view_item'          => __( 'View CF7 ', 'cf7-views' ),
			'all_items'          => __( 'All CF7 Views', 'cf7-views' ),
			'search_items'       => __( 'Search Views', 'cf7-views' ),
			'parent_item_colon'  => __( 'Parent Views:', 'cf7-views' ),
			'not_found'          => __( 'No view found.', 'cf7-views' ),
			'not_found_in_trash' => __( 'No view found in Trash.', 'cf7-views' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'cf7-views' ),
			'public'             => false,
			'exclude_from_search'=> true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'cf7-views' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'menu_icon'		 => 'dashicons-format-gallery',
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'false' )
		);

		register_post_type( 'cf7-views', $args );
	}


	function add_extra_columns( $columns ) {
		$columns = array_slice( $columns, 0, 2, true ) + array( "shortcode" =>__( 'Shortcode', 'cf7-views' ) ) + array_slice( $columns, 2, count( $columns )-2, true );
		$columns = array_slice( $columns, 0, 2, true ) + array( "view_format" =>__( 'View Format', 'cf7-views' ) ) + array_slice( $columns, 2, count( $columns )-2, true );
		$columns = array_slice( $columns, 0, 2, true ) + array( "view_source" =>__( 'View Source', 'cf7-views' ) ) + array_slice( $columns, 2, count( $columns )-2, true );
		return $columns;
	}

	function extra_column_detail( $column, $post_id ) {
		switch ( $column ) {
		case 'shortcode' :
			echo '<code>[cf7-views id=' . $post_id . ']</code>';
			break;
		case 'view_format' :
			$view_settings_json = get_post_meta( $post_id, 'view_settings', true );
			if ( ! empty( $view_settings_json ) ) {
				$view_settings =  json_decode( $view_settings_json );
				$view_type = $view_settings->viewType;
				echo '<span>' . ucfirst( $view_type ) . '</span>';
			}
			break;
		case 'view_source' :
			if ( empty( $this->forms ) && class_exists( 'WPCF7_ContactForm' ) ) {
				$this->forms = WPCF7_ContactForm::find();
			}
			$view_settings_json = get_post_meta( $post_id, 'view_settings', true );
			if ( ! empty( $view_settings_json ) ) {
				$view_settings =  json_decode( $view_settings_json );
				$form_id = $view_settings->formId;
				if ( ! empty( $this->forms ) ) {
					foreach ( $this->forms as $form ) {
						if( $form->name() == $form_id){
							printf('<a href="%s">' . $form->title() . '</a>',
							admin_url('admin.php?page=wpcf7&post='.$form->id().'&action=edit')
							);
						}
					}
				}

			}
			break;

		}
	}
}

new CF7_Views_Posttype();
