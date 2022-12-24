<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CF7ViewsReview{

	const PREFIX = 'cf7_views';
	const PLUGIN_NAME = 'Views for Contact Form 7';
	const PLUGIN_REVIEW_LINK = 'https://wordpress.org/support/plugin/cf7-views/reviews/?filter=5';
	const FILE_PATH = CF7_VIEWS_URL.'/inc/admin/review';

	public static function init() {
	//	echo 'here'; die;
		add_action( 'init', array( __CLASS__, 'hooks' ) );
		add_action( 'wp_ajax_'.self::PREFIX.'_review_action', array( __CLASS__, 'ajax_handler' ) );
	}

	/**
	 * Hook into relevant WP actions.
	 */
	public static function hooks() {
		if ( is_admin() && current_user_can( 'edit_posts' ) ) {
			self::installed_on();
			add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
			add_action( 'network_admin_notices', array( __CLASS__, 'admin_notices' ) );
			add_action( 'user_admin_notices', array( __CLASS__, 'admin_notices' ) );
		}
	}


	/**
	 * Get the install date for comparisons. Sets the date to now if none is found.
	 *
	 * @return false|string
	 */
	public static function installed_on() {
		//delete_optionalled_on', $installed_on );( 'gf_spreadsheet_reviews_inst
		$installed_on = get_option( self::PREFIX.'_reviews_installed_on', false );
		//update_option( self::PREFIX.'_reviews_installed_on', date('Y-m-d', strtotime('-90 days')) );
		if ( ! $installed_on ) {
			$installed_on = current_time( 'mysql' );
			update_option( self::PREFIX.'_reviews_installed_on', $installed_on );
		}
		//var_dump(get_option( self::PREFIX.'_reviews_installed_on', false ));
		return $installed_on;
	}

	/**
	 *
	 */
	public static function ajax_handler() {
		$args = wp_parse_args( $_REQUEST, array(
			'group'  => self::get_trigger_group(),
			'code'   => self::get_trigger_code(),
			'pri'    => self::get_current_trigger( 'pri' ),
			'reason' => 'maybe_later',
		) );

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::PREFIX.'_review_action' ) ) {
			wp_send_json_error();
		}

		try {
			$user_id = get_current_user_id();

			$dismissed_triggers                   = self::dismissed_triggers();
			$dismissed_triggers[ $args['group'] ] = $args['pri'];
			update_user_meta( $user_id, '_'.self::PREFIX.'_reviews_dismissed_triggers', $dismissed_triggers );
			update_user_meta( $user_id, '_'.self::PREFIX.'_reviews_last_dismissed', current_time( 'mysql' ) );

			switch ( $args['reason'] ) {
				case 'maybe_later':
					update_user_meta( $user_id, '_'.self::PREFIX.'_reviews_last_dismissed', current_time( 'mysql' ) );
					break;
				case 'am_now':
				case 'already_did':
					self::already_did( true );
					break;
			}

			wp_send_json_success();

		} catch ( Exception $e ) {
			wp_send_json_error( $e );
		}
	}

	/**
	 * @return int|string
	 */
	public static function get_trigger_group() {
		static $selected;

		if ( ! isset( $selected ) ) {

			$dismissed_triggers = self::dismissed_triggers();

			$triggers = self::triggers();

			foreach ( $triggers as $g => $group ) {
				foreach ( $group['triggers'] as $t => $trigger ) {
					if ( ! in_array( false, $trigger['conditions'] ) && ( empty( $dismissed_triggers[ $g ] ) || $dismissed_triggers[ $g ] < $trigger['pri'] ) ) {
						$selected = $g;
						break;
					}
				}

				if ( isset( $selected ) ) {
					break;
				}
			}
		}

		return $selected;
	}

	/**
	 * @return int|string
	 */
	public static function get_trigger_code() {
		static $selected;

		if ( ! isset( $selected ) ) {

			$dismissed_triggers = self::dismissed_triggers();
			//echo '<pre>';print_r($dismissed_triggers);die;
			foreach ( self::triggers() as $g => $group ) {
				foreach ( $group['triggers'] as $t => $trigger ) {
					if ( ! in_array( false, $trigger['conditions'] ) && ( empty( $dismissed_triggers[ $g ] ) || $dismissed_triggers[ $g ] < $trigger['pri'] ) ) {
						$selected = $t;
						break;
					}
				}

				if ( isset( $selected ) ) {
					break;
				}
			}
		}
		//echo '<pre>';print_r( self::triggers());die;
		return $selected;
	}
/**
	 * @param null $key
	 *
	 * @return bool|mixed|void
	 */
	public static function get_current_trigger( $key = null ) {
		$group = self::get_trigger_group();
		$code  = self::get_trigger_code();

		if ( ! $group || ! $code ) {
			return false;
		}

		$trigger = self::triggers( $group, $code );

		return empty( $key ) ? $trigger : ( isset( $trigger[ $key ] ) ? $trigger[ $key ] : false );
	}

	/**
	 * Returns an array of dismissed trigger groups.
	 *
	 * Array contains the group key and highest priority trigger that has been shown previously for each group.
	 *
	 * $return = array(
	 *   'group1' => 20
	 * );
	 *
	 * @return array|mixed
	 */
	public static function dismissed_triggers() {
		$user_id = get_current_user_id();

		$dismissed_triggers = get_user_meta( $user_id, '_'.self::PREFIX.'_reviews_dismissed_triggers', true );

		if ( ! $dismissed_triggers ) {
			$dismissed_triggers = array();
		}

		return $dismissed_triggers;
	}

	/**
	 * Returns true if the user has opted to never see this again. Or sets the option.
	 *
	 * @param bool $set If set this will mark the user as having opted to never see this again.
	 *
	 * @return bool
	 */
	public static function already_did( $set = false ) {
		$user_id = get_current_user_id();
		//var_dump(delete_user_meta( $user_id, '_'.self::PREFIX.'_reviews_dismissed_triggers' ));
		  //delete_user_meta( $user_id, '_'.self::PREFIX.'_reviews_last_dismissed' );
		 // delete_user_meta( $user_id, '_'.self::PREFIX.'_reviews_already_did' );
		if ( $set ) {
			update_user_meta( $user_id, '_'.self::PREFIX.'_reviews_already_did', true );

			return true;
		}
		return (bool) get_user_meta( $user_id, '_'.self::PREFIX.'_reviews_already_did', true );
	}

	/**
	 * Gets a list of triggers.
	 *
	 * @param null $group
	 * @param null $code
	 *
	 * @return bool|mixed|void
	 */
	public static function triggers( $group = null, $code = null ) {
		static $triggers;

		if ( ! isset( $triggers ) ) {
			$current_user = wp_get_current_user();
			$time_message = __( '<p>Hi %s! </p>
			<p>You\'ve been using '.self::PLUGIN_NAME.' on your site for %s </p><p> We hope it\'s been helpful. If you\'re enjoying this plugin, please consider leaving a positive review on wordpress.org. It really helps.</p>', 'gf-spreadsheet' );
			$triggers = array(
				'time_installed' => array(
					'triggers' => array(
						'one_week'     => array(
							'message'    => sprintf( $time_message, $current_user->user_login,  __( '1 week', 'gf-spreadsheet' ) ),
							'conditions' => array(
								strtotime( self::installed_on() . ' +1 week' ) < time(),
							),
							'link'       => self::PLUGIN_REVIEW_LINK,
							'pri'        => 10,
						),
						'one_month'    => array(
							'message'    => sprintf( $time_message, $current_user->user_login, __( '1 month', 'gf-spreadsheet' ) ),
							'conditions' => array(
								strtotime( self::installed_on() . ' +1 month' ) < time(),
							),
							'link'       => self::PLUGIN_REVIEW_LINK,
							'pri'        => 20,
						),
						'three_months' => array(
							'message'    => sprintf( $time_message, $current_user->user_login, __( '3 months', 'gf-spreadsheet' ) ),
							'conditions' => array(
								strtotime( self::installed_on() . ' +3 months' ) < time(),
							),
							'link'       => self::PLUGIN_REVIEW_LINK,
							'pri'        => 30,
						),

					),
					'pri'      => 10,
				),

			);


			$triggers = apply_filters( self::PREFIX.'_reviews_triggers', $triggers );

			// Sort Groups
			uasort( $triggers, array( __CLASS__, 'rsort_by_priority' ) );

			// Sort each groups triggers.
			foreach ( $triggers as $k => $v ) {
				uasort( $triggers[ $k ]['triggers'], array( __CLASS__, 'rsort_by_priority' ) );
			}
		}

		if ( isset( $group ) ) {
			if ( ! isset( $triggers[ $group ] ) ) {
				return false;
			}

			return ! isset( $code ) ? $triggers[ $group ] : ( isset( $triggers[ $group ]['triggers'][ $code ] ) ? $triggers[ $group ]['triggers'][ $code ] : false);
		}

		return $triggers;
	}

	/**
	 * Render admin notices if available.
	 */
	public static function admin_notices() {
		//var_dump( self::hide_notices());
		if ( self::hide_notices() ) {
			return;
		}

		$group  = self::get_trigger_group();
		$code   = self::get_trigger_code();
		$pri    = self::get_current_trigger( 'pri' );
		$trigger = self::get_current_trigger();

		// Used to anonymously distinguish unique site+user combinations in terms of effectiveness of each trigger.
		$uuid = wp_hash( home_url() . '-' . get_current_user_id() );
		//var_dump($group, $code,$pri, $trigger ); die;
		?>

		<script type="text/javascript">
			(function ($) {
				var trigger = {
					group: '<?php echo $group; ?>',
					code: '<?php echo $code; ?>',
					pri: '<?php echo $pri; ?>'
				};

				function dismiss(reason) {
					$.ajax({
						method: "POST",
						dataType: "json",
						url: ajaxurl,
						data: {
							action: "<?php echo self::PREFIX.'_review_action'; ?>",
							nonce: "<?php echo wp_create_nonce( self::PREFIX.'_review_action' ); ?>",
							group: trigger.group,
							code: trigger.code,
							pri: trigger.pri,
							reason: reason
						}
					});

					<?php if ( ! empty( self::$api_url ) ) : ?>
					$.ajax({
						method: "POST",
						dataType: "json",
						url: '<?php echo self::$api_url; ?>',
						data: {
							trigger_group: trigger.group,
							trigger_code: trigger.code,
							reason: reason,
							uuid: '<?php echo $uuid; ?>'
						}
					});
					<?php endif; ?>
				}

				$(document)
					.on('click', "<?php echo '.'.self::PREFIX.'-notice '.'.'.self::PREFIX.'-dismiss'; ?>", function (event) {

						var $this = $(this),
							reason = $this.data('reason'),
							notice = $this.parents("<?php echo '.'.self::PREFIX.'-notice'; ?>");

						notice.fadeTo(100, 0, function () {
							notice.slideUp(100, function () {
								notice.remove();
							});
						});

						dismiss(reason);
					})
					.ready(function () {
						setTimeout(function () {
							$("<?php echo '.'.self::PREFIX.'-notice button.notice-dismiss'; ?>").click(function (event) {
								dismiss('maybe_later');
							});
						}, 1000);
					});
			}(jQuery));
		</script>

		<style>
			<?php echo '.'.self::PREFIX.'-notice ';?> p  {
				margin-bottom: 0;
				margin-top: 0px;

			}

			<?php echo '.'.self::PREFIX.'-notice ';?> img.logo {
				float: left;
				margin-left: 0px;
				width:  64px;
				padding: 0.25em;
				border: 1px solid #ccc;
				margin-right: 30px;
				margin-top: 16px;
			}
			<?php echo '.'.self::PREFIX.'-notice ';?> ul{
				margin-top: 9px;
   				 margin-bottom: 20px;
			}
			<?php echo '.'.self::PREFIX.'-notice ';?> ul li{
				float: left;
   				 margin-right: 20px;
			}

		</style>

		<div class="notice notice-success is-dismissible <?php echo self::PREFIX.'-notice ';?>">

			<div>
				<img style="margin-right:10px" class="logo" src="<?php echo self::FILE_PATH; ?>/icon-128x128.jpg" />
				<div style="display:inline-block;">
				<strong>
					<?php echo $trigger['message']; ?>
					-WebHolics
				</strong>
				</div>
			</div>
			<ul>
				<li>
					<a class="<?php echo self::PREFIX.'-dismiss ';?>" target="_blank" href="<?php echo $trigger['link']; ?>" data-reason="am_now">
						<strong><?php _e( 'Rate us 5 stars on wordpress.org', 'gf-spreadsheet' ); ?></strong>
					</a>
				</li>
				<li>
					<a href="#" class="<?php echo self::PREFIX.'-dismiss ';?>" data-reason="maybe_later">
						<?php _e( 'Nope, maybe later', 'gf-spreadsheet' ); ?>
					</a>
				</li>
				<li>
					<a href="#" class="<?php echo self::PREFIX.'-dismiss ';?>" data-reason="already_did">
						<?php _e( 'I already did', 'gf-spreadsheet' ); ?>
					</a>
				</li>
			</ul>
			<div style="clear:both"></div>
		</div>

		<?php
	}

	/**
	 * Checks if notices should be shown.
	 *
	 * @return bool
	 */
	public static function hide_notices() {
		$trigger_code = self::get_trigger_code();
		//var_dump($trigger_code); die;
		$conditions = array(
			self::already_did(),
			self::last_dismissed() && strtotime( self::last_dismissed() . ' +2 weeks' ) > time(),
			empty( $trigger_code ),
		);

		return in_array( true, $conditions );
	}

	/**
	 * Gets the last dismissed date.
	 *
	 * @return false|string
	 */
	public static function last_dismissed() {
		$user_id = get_current_user_id();
		//var_dump(get_user_meta( $user_id, '_'.self::PREFIX.'_reviews_last_dismissed', true )); die;
		return get_user_meta( $user_id, '_'.self::PREFIX.'_reviews_last_dismissed', true );
	}

	/**
	 * Sort array by priority value
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public static function sort_by_priority( $a, $b ) {
		if ( ! isset( $a['pri'] ) || ! isset( $b['pri'] ) || $a['pri'] === $b['pri'] ) {
			return 0;
		}

		return ( $a['pri'] < $b['pri'] ) ? - 1 : 1;
	}

	/**
	 * Sort array in reverse by priority value
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public static function rsort_by_priority( $a, $b ) {
		if ( ! isset( $a['pri'] ) || ! isset( $b['pri'] ) || $a['pri'] === $b['pri'] ) {
			return 0;
		}

		return ( $a['pri'] < $b['pri'] ) ? 1 : - 1;
	}

}

CF7ViewsReview::init();