<?php
class NF_Views_Upgrade_to_Pro_Page {

	function __construct(){
		add_action('admin_menu', array( $this,'add_page') );
	}

	function add_page(){
		add_submenu_page(
			'edit.php?post_type=cf7-views',
			__( 'Get Pro', 'textdomain' ),
			__( '<strong style="color: #FCB214;">Get Pro</strong>', 'textdomain' ),
			'manage_options',
			'cf7-views-get-pro',
			array( $this, 'upgrade_to_pro_page')
		);
	}

	function upgrade_to_pro_page(){
		?>
		<style>
			#cf7-views-upgrade-section{
				margin: 32px;
				font-size: 1rem;
			}
			#cf7-views-upgrade-section h2{
				font-size: 1.88em;
				line-height: 2.5rem;
				margin-bottom: 1.2rem;
			}
			.cf7-views-heading-highlight {
				color: #cd631d;
				font-weight: 600;
			}
			.cf7-views-pro-benefits li {
				list-style: none!important;
				position: relative;
				padding-left: 1.2533333333rem;
				height: 30px;
			}
			.cf7-views-pro-benefits span{
				line-height: 30px;
			}
			.cf7-views-pro-benefits .dashicons-yes{
				color:green;
				font-size:32px;
			}
			.cf7-views-pro-benefits__title {
				font-weight: 600;
				padding-left: 10px;
			}
			.cf7-views-pro-benefits__description:before {
				content: "– ";
			}
			.cf7-views-upsell{
				display: inline-flex;
				align-items: center;
				justify-content: center;
				box-sizing: border-box;
				min-height: 48px;
				padding: 8px 1em;
				font-size: 16px;
				line-height: 1.5;
				font-family: Arial,sans-serif;
				color: #ffffff;
				border-radius: 4px;
				box-shadow: inset 0 -4px 0 rgba(0,0,0,.2);
				filter: drop-shadow(0 2px 4px rgba(0,0,0,.2));
				text-decoration: none;
				background-color: #FF9800 ;
			}
		</style>

		<div id="cf7-views-upgrade-section">
			<h2><span class="cf7-views-heading-highlight">CF7 Views Pro</span>, take your Views to next level!</h2>
			<ul class="cf7-views-pro-benefits ">

					<li class="cf7-views-pro-benefits__item">
						<span class="dashicons dashicons-yes"></span>
						<span class="cf7-views-pro-benefits__title">Filter & Sorting</span>
						<span class="cf7-views-pro-benefits__description">filter & sort view by form field values.</span>
					</li>
					<li class="cf7-views-pro-benefits__item">
						<span class="dashicons dashicons-yes"></span>
						<span class="cf7-views-pro-benefits__title">Approved Submissions</span>
						<span class="cf7-views-pro-benefits__description">display only those submissions which are approved by admin.</span>
					</li>
					<li class="cf7-views-pro-benefits__item">
						<span class="dashicons dashicons-yes"></span>
						<span class="cf7-views-pro-benefits__title">List & DataTable View</span>
						<span class="cf7-views-pro-benefits__description">display entries in List View or DataTable View.</span>
					</li>
					<li class="cf7-views-pro-benefits__item">
						<span class="dashicons dashicons-yes"></span>
						<span class="cf7-views-pro-benefits__title">Search</span>
						<span class="cf7-views-pro-benefits__description">allow users to search data in view.</span>
					</li>
					<li class="cf7-views-pro-benefits__item">
						<span class="dashicons dashicons-yes"></span>
						<span class="cf7-views-pro-benefits__title">Single Page View</span>
						<span class="cf7-views-pro-benefits__description">display entry details on single page.</span>
					</li>
					<li class="cf7-views-pro-benefits__item">
						<span class="dashicons dashicons-yes"></span>
						<span class="cf7-views-pro-benefits__title">Premium Support</span>
						<span class="cf7-views-pro-benefits__description">access to premium support.</span>
					</li>
				</ul>
				<a class="cf7-views-upsell" target="_blank" href="https://cf7views.com/pricing/?utm_source=wordpress-plugin-dashboard&utm_medium=cf7-views-upgrade-page&utm_campaign=cf7-views-lite-version"> Buy CF7 Views Pro</a>
		</div>

		<?php
	}


}
new NF_Views_Upgrade_to_Pro_Page();