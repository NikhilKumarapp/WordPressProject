<?php

class CF7_Views_Metabox {


	function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );

	}
	/**
	 * Register meta box(es).
	 */
	function register_meta_boxes() {
		add_meta_box( 'cf7-views-metabox', __( 'View Settings', 'cf7-views' ),  array( $this, 'views_metabox' ), 'cf7-views', 'normal', 'high' );
		add_meta_box( 'cf7-views-shortcode-metabox', __( 'Shortcode', 'cf7-views' ),  array( $this, 'shortcode_metabox' ), 'cf7-views', 'side', 'high' );
	}



	function views_metabox( $post ) {
		// $form_id = $this->get_setting( 'ninja_form_id' );
		if ( class_exists( 'WPCF7_ContactForm' ) && class_exists('Flamingo_Inbound_Message') ) {
			$forms = WPCF7_ContactForm::find();

			$view_forms = array( array( 'id' => '', 'label' => 'Select' ) );
			foreach ( $forms as $form ) {
				$view_forms[] = array( 'id' => $form->name(), 'label' => $form->title() );
			}
			// echo '<pre>';
			// print_r($forms); die;
			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'cf7_views_metabox', 'cf7_views_nonce' );
			// delete_post_meta($post->ID, 'view_settings');
			$view_saved_settings = get_post_meta( $post->ID, 'view_settings', true );
			if ( empty( $view_saved_settings ) ) {
				$view_saved_settings = '{}';
				$form_id = '';
				if ( ! empty( $view_forms[1]['id'] ) ) {
					$form_id = $view_forms[1]['id'];
				}
			}else {
				$view_settings = json_decode( html_entity_decode( $view_saved_settings ) );
				$form_id = $view_settings->formId;
			}
			$form_fields = cf7_views_get_form_fields( $form_id );

			$cf7_view_config = apply_filters( 'cf7_view_config', array('prefix'=>'cf7'));
?>
	<script>
		var view_forms = '<?php echo  addslashes(json_encode( $view_forms )); ?>';
				var _view_saved_settings = '<?php echo addslashes( $view_saved_settings ); ?>';
				var _view_form_fields =  '<?php echo  addslashes($form_fields); ?>';
				var _view_config =  '<?php echo  addslashes(json_encode( $cf7_view_config )); ?>';
	</script>
		   <div id="views-container"></div>

		<?php
		} else {
			echo 'Please install <a target="_blank" href="https://wordpress.org/plugins/contact-form-7/"> Contact Form 7 </a> & <a target="_blank" href="https://wordpress.org/plugins/flamingo/"> Flamingo</a> to use CF7 Views';

		}
	}

	/**
	 * Save meta box content.
	 *
	 * @param int     $post_id Post ID
	 */
	function save_meta_box( $post_id ) {

		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['cf7_views_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['cf7_views_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'cf7_views_metabox' ) ) {
			return $post_id;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$finale_view_settings = $_POST['final_view_settings'];

		update_post_meta( $post_id, 'view_settings', $finale_view_settings );

	}
	public function shortcode_metabox( $post ) {
		echo '<code>[cf7-views id=' . $post->ID . ']</code>';
		echo '<p style="margin-top:10px" class="description">Use this shortcode to display View anywhere on your site.</p>';
	}


	function get_setting( $setting_name ) {
		$settings = $this->settings;
		return isset( $settings[$setting_name] )?$settings[$setting_name]: '';
	}

}

new CF7_Views_Metabox();
