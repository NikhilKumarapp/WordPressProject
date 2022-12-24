<?php

class CF7_Views_Ajax{

	function __construct() {
		add_action( 'wp_ajax_cf7_views_get_form_fields', array($this, 'get_form_fields'));

	}

	public function get_form_fields(){
		//var_dump($_POST['form_id']); die;
		if( empty($_POST['form_id']) ) return ;

		echo cf7_views_get_form_fields($_POST['form_id']);
		wp_die();
	}

}
new CF7_Views_Ajax();