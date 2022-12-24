<?php

function cf7_views_get_form_fields( $slug ) {
	if ( empty( $slug ) ) {
		return '{}';
	}
	$form_fields_obj = new stdClass();
	$post = cf7_views_get_post_by_slug( $slug );

	if ( empty( $post ) || ! is_object($post) ) {
		return '{}';
	}
	$form_id = $post->ID;
	$contact_form = WPCF7_ContactForm::get_instance( $form_id );

	if ( $contact_form ) {
		$tags = $contact_form->scan_form_tags();
		$form_fields_obj = new stdClass();
		foreach ( $tags as $tag ) {
			if ( $tag->type != 'submit' ) {
				$form_fields_obj->{$tag->name} = (object)array( 'id' => $tag->name, 'label' => $tag->name,'fieldType' => $tag->basetype );
			}
		}
		return json_encode( $form_fields_obj );

	}

}


/**
 * Get submissions based on specific critera.
 *
 * @since 2.7
 * @param array   $args
 * @return array $sub_ids
 */
function cf7_views_get_submissions_data( $args ) {
    if (class_exists('Flamingo_Inbound_Message')) {
		$submissions = Flamingo_Inbound_Message::find($args);
		return array(
			'submissions' => $submissions,
			'count' => Flamingo_Inbound_Message::count($args)
		);
    }else{
		return array(
			'submissions' => '',
			'count' => 0
		);
	}


}

function cf7_views_get_post_by_slug( $slug ) {
	$posts = get_posts( array(
			'name' => $slug,
			'posts_per_page' => 1,
			'post_type' => 'wpcf7_contact_form',
			'post_status' => 'publish'
		) );


	if ( ! $posts ) {
		return '';
	}

	return $posts[0];
}
