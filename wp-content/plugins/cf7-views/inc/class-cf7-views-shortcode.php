<?php
class CF7_Views_Shortcode {
	public $view_id;
	public $submissions_count;
	public $table_heading_added = false;
	private $seq_no =1;
	function __construct() {

		add_shortcode( 'cf7-views', array( $this, 'shortcode' ), 10 );

	}

	public function shortcode( $atts ) {
		$this->seq_no = 1;
		$atts = shortcode_atts(
			array(
				'id' => '',
			), $atts );

		if ( empty( $atts['id'] ) ) {
			return;
		}
		$view_id = $atts['id'];
		$this->view_id = $view_id;
		$view_settings_json = get_post_meta( $view_id, 'view_settings', true );
		if ( empty( $view_settings_json ) ) {
			return;
		}

		$view_settings =  json_decode( $view_settings_json );
		$view_type = $view_settings->viewType;
		$method_name = 'get_view';
		$view  = $this->$method_name( $view_settings );
		return $view;

	}

	function get_view( $view_settings ) {
		global $wpdb;
		$view_type = $view_settings->viewType;
		$before_loop_rows = $view_settings->sections->beforeloop->rows;
		$loop_rows = $view_settings->sections->loop->rows;
		$after_loop_rows = $view_settings->sections->afterloop->rows;

		$this->submissions_count = 0;

		$per_page = $view_settings->viewSettings->multipleentries->perPage;
		$sort_order = isset($view_settings->viewSettings->sort->direction) ? $view_settings->viewSettings->sort->direction : 'ASC';
		$args = array(
			'channel' => $view_settings->formId,
			'posts_per_page' =>$per_page,
			'order' => $sort_order
		);

		if ( ! empty( $_GET['pagenum'] ) ) {
			$page_no = sanitize_text_field( $_GET['pagenum'] );
			$offset = $per_page * ( $page_no-1 );
			$args['offset'] = $offset;
			$this->seq_no = $offset+1;
		}

		$submissions_data = cf7_views_get_submissions_data( $args );
		$submissions = $submissions_data['submissions'];
		$this->submissions_count = $submissions_data['count'];
		if ( empty( $submissions ) ) {
			return;
		}
		$view_content = '';
		if ( ! empty ( $before_loop_rows ) ) {
			$view_content .= $this->get_sections_content( 'beforeloop', $view_settings, $submissions );
		}

		if ( ! empty ( $loop_rows ) ) {
			if ( $view_type == 'table' ) {
				$view_content .= $this->get_table_content( 'loop', $view_settings, $submissions );
			} else {
				$view_content .= $this->get_sections_content( 'loop', $view_settings, $submissions );
			}

		}

		if ( ! empty ( $after_loop_rows ) ) {
			$view_content .= $this->get_sections_content( 'afterloop', $view_settings, $submissions );
		}
		return $view_content;

	}


	function get_sections_content( $section_type, $view_settings, $submissions ) {
		$content = '';
		$section_rows = $view_settings->sections->{$section_type}->rows;
		if ( $section_type == 'loop' ) {
			foreach ( $submissions as $sub ) {
				foreach ( $section_rows as $row_id ) {
					//$content .= $this->get_table_content( $row_id, $view_settings, $sub );
					$content .= $this->get_grid_row_html( $row_id, $view_settings, $sub );
					$this->seq_no++;
				}
			}
		} else {
			foreach ( $section_rows as $row_id ) {
				$content .= $this->get_grid_row_html( $row_id, $view_settings );
			}
		}
		return $content;
	}



	function get_table_content( $section_type, $view_settings, $submissions ) {
		$content = '';
		$section_rows = $view_settings->sections->{$section_type}->rows;
		$content = ' <div class="cf7-views-cont cf7-views-'.$this->view_id.'-cont"> <table class="cf7-views-table cf7-view-' . $this->view_id . '-table pure-table pure-table-bordered">';
		$content .= '<thead>';
		foreach ( $submissions as $sub ) {
			$content .= '<tr>';
			foreach ( $section_rows as $row_id ) {
				$content .= $this->get_table_row_html( $row_id, $view_settings, $sub );
				$this->seq_no++;
			}
			$content .= '</tr>';
		}
		$content .= '</tbody></table></div>';

		return $content;
	}

	function get_table_row_html( $row_id, $view_settings, $sub = false ) {
		$row_content = '';
		$row_columns = $view_settings->rows->{$row_id}->cols;
		foreach ( $row_columns as $column_id ) {
			$row_content .= $this->get_table_column_html( $column_id, $view_settings, $sub );
		}
		//$row_content .= '</table>'; // row ends
		return $row_content;
	}

	function get_table_column_html( $column_id, $view_settings, $sub ) {
		$column_size = $view_settings->columns->{$column_id}->size;
		$column_fields = $view_settings->columns->{$column_id}->fields;

		$column_content = '';

		if ( ! ( $this->table_heading_added ) ) {

			foreach ( $column_fields as $field_id ) {
				$column_content .= $this->get_table_headers( $field_id, $view_settings, $sub );
			}
			$this->table_heading_added = true;
			$column_content .= '</tr></thead><tbody><tr>';
		}
		foreach ( $column_fields as $field_id ) {

			$column_content .= $this->get_field_html( $field_id, $view_settings, $sub );
		}

		return $column_content;
	}



	function get_grid_row_html( $row_id, $view_settings, $sub = false ) {
		$row_columns = $view_settings->rows->{$row_id}->cols;

		$row_content = '<div class="pure-g">';
		foreach ( $row_columns as $column_id ) {
			$row_content .= $this->get_grid_column_html( $column_id, $view_settings, $sub );
		}
		$row_content .= '</div>'; // row ends
		return $row_content;
	}

	function get_grid_column_html( $column_id, $view_settings, $sub ) {
		$column_size = $view_settings->columns->{$column_id}->size;
		$column_fields = $view_settings->columns->{$column_id}->fields;

		$column_content = '<div class="pure-u-1 pure-u-md-' . $column_size . '">';

		foreach ( $column_fields as $field_id ) {

			$column_content .= $this->get_field_html( $field_id, $view_settings, $sub );

		}
		$column_content .= '</div>'; // column ends
		return $column_content;
	}

	function get_field_html( $field_id, $view_settings, $sub ) {
		$field = $view_settings->fields->{$field_id};
		$form_field_id = $field->formFieldId;
		$fieldSettings = $field->fieldSettings;
		$label = $fieldSettings->useCustomLabel ? $fieldSettings->label : $field->label;
		$class = $fieldSettings->customClass;
		$view_type = $view_settings->viewType;
		$field_html = '';
		if ( $view_type == 'table' ) {
			$field_html .= '<td>';
		}

		$field_html .= '<div class="field-' . $form_field_id . ' ' . $class . '">';

		// check if it's a form field
		if ( ! empty( $sub ) && is_object( $sub ) && ($form_field_id !== 'entryId' && $form_field_id !== 'sequenceNumber' )) {
			//  if view type is table then don't send label
			if ( ! empty( $label &&  $view_type != 'table' ) ) {
				$field_html .= '<div class="field-label">' . $label . '</div>';
			}
			$field_value = $this->get_field_value( $form_field_id, $sub );

			if ( is_array( $field_value ) ) {
				$field_value = implode( ',', $field_value );
			}
			// var_dump($sub); die;
			$field_value = apply_filters('cf7views-field-value', $field_value, $field, $view_settings, $sub);
			$field_html .= $field_value;
		} else {

			switch ( $form_field_id ) {
			case 'pagination':
				$field_html .= $this->get_pagination_links( $view_settings, $sub );
				break;
			case 'paginationInfo':
				$field_html .= $this->get_pagination_info( $view_settings, $sub );
				break;
			case 'entryId':
				$field_html .= '<div class="cf7-view-field-value cf7-view-field-type-entryId-value">';
				$field_html .= $sub->id() ;
				$field_html .='</div>';
				break;
			case 'sequenceNumber':
				$field_html .= '<div class="cf7-view-field-value cf7-view-field-type-sequenceNumber-value">';
				$field_html .= $this->seq_no;
				$field_html .= '</div>';
				break;
			}
		}

		$field_html .= '</div>';
		if ( $view_type == 'table' ) {
			$field_html .= '</td>';
		}


		return $field_html;
	}

	function get_table_headers( $field_id, $view_settings, $sub ) {
		$field = $view_settings->fields->{$field_id};
		$fieldSettings = $field->fieldSettings;
		$label = $fieldSettings->useCustomLabel ? $fieldSettings->label : $field->label;
		return '<th>' . $label . '</th>';
	}


	function get_pagination_links( $view_settings, $sub ) {
		global $wp;
		$entries_count = $this->submissions_count;
		$per_page = $view_settings->viewSettings->multipleentries->perPage;
		$pages = new CF7_View_Paginator( $per_page, 'pagenum' );
		$pages->set_total( $entries_count ); //or a number of records
		$current_url = site_url(remove_query_arg(array('pagenum', 'view_id')));
		$current_url = add_query_arg( 'view_id', $this->view_id, $current_url );

		return $pages->page_links( $current_url. '&');
	}

	function get_pagination_info( $view_settings, $sub ) {
		$page_no = empty(  $_GET['pagenum'] ) ? 1 : sanitize_text_field( $_GET['pagenum'] );
		$submissions_count = $this->submissions_count;
		$per_page = $view_settings->viewSettings->multipleentries->perPage;
		$from = ( $page_no-1 ) * $per_page;
		if ( $submissions_count > $per_page ) {
			$of = $per_page * $page_no;
		} else {
			$of = $submissions_count;
		}


		return sprintf(
			__( 'Displaying %1$s - %2$s of %3$s', 'cf7-views' ),
			$from,
			$of,
			$submissions_count
		);

	}

	function get_field_value( $key, $sub ) {
		$value = '';
		$fields = $sub->fields;
		if ( array_key_exists( $key, $fields ) ) {
			$value = $fields[$key];
		}

		return $value;
	}

}
new CF7_Views_Shortcode();
