<?php
/*
Plugin Name: InterVarsity
Plugin URI: https://github.com/caleb531/intervarsity-plugin
Description: The InterVarsity plugin is a WordPress plugin intended for InterVarsity Christian Fellowship/USA chapters. It primarily allows you to create and manage small groups for any number of campuses. The plugin provides several fields for you to describe your small group, including time, location, leaders, and contact information. Other features of the plugin include a Facebook Like Button shortcode and integration with the Cyclone Slider 2 plugin for setting page sliders. Ultimately, the InterVarsity plugin provides an powerful yet intuitive backend for creating your InterVarsity chapter website.
Author: Caleb Evans
Author URI: http://calebevans.me/
Version: 2.2.0
License: GNU General Public License v2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Define constant to indicate that the plugin is installed and active
define( 'INTERVARSITY_PLUGIN', true );
// Define constant for storing path to plugin directory
define( 'IV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'IV_PLUGIN_DIR_URI', plugins_url( '/intervarsity' ) );

// Class for managing all plugin functions
class InterVarsity_Plugin {

	function __construct() {

		register_activation_hook( __FILE__, 'flush_rewrite_rules' );
		register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

		add_action( 'init', array( $this, 'init' ), 0 );

		$this->add_shortcodes();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 10 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 10 );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_items' ), 0 );
		add_filter( 'dashboard_glance_items', array( $this, 'add_post_types_to_dashboard' ), 10 );

		add_filter( 'posts_search', array( $this, 'extend_sg_search' ), 500, 2 );

	}

	// Code to run on WP initialization
	public function init() {

		// If Awesome CPT is loaded
		if ( defined( 'AWESOME_CPT' ) ) {
			// Add custom post types, taxonomies, and meta boxes
			$this->add_post_types();
			$this->add_taxonomies();
			$this->add_meta_boxes();
		} else {
			// Otherwise, notify user of its absence
			add_action( 'admin_notices', array( $this, 'display_dependency_notice' ), 10 );
		}

	}

	// Displays notice indicating that a plugin depenency is missing
	public function display_dependency_notice() {

		?>
		<div class="error">
			<p>This plugin requires the <a href="https://github.com/caleb531/awesome-cpt" target="_blank">Awesome CPT plugin</a> to function. Please install and activate it.</p>
		</div>
		<?php

	}

	// Retrieves the HTML for the sidebar shown in all plugin help menus
	public function get_help_sidebar() {

		return '<p><strong>For more information:</strong></p>' .
		'</p><a href="https://github.com/caleb531/intervarsity-plugin/issues" target="_blank">Plugin Support</a></p>';

	}

	// Column populate functions

	public function populate_sg_time( $post_id ) {
		echo get_post_meta( $post_id, '_sg_time', true );
	}
	public function populate_sg_location( $post_id ) {
		echo get_post_meta( $post_id, '_sg_location', true );
	}
	public function populate_sg_leaders( $post_id ) {
		echo get_post_meta( $post_id, '_sg_leaders', true );
	}
	public function populate_sg_thumbnail( $post_id ) {
		echo get_the_post_thumbnail( $post_id, 'thumbnail' );
	}

	// Adds and configure custom post types
	public function add_post_types() {

		// Define post type for InterVarsity small groups
		$iv_small_group = new Awesome_Post_Type( array(
			'id'                      => 'iv_small_group',
			// Labels and post update messages are automatically generated from
			// these names
			'name'                    => array(
				'singular'            => 'small group',
				'plural'              => 'small groups'
			),
			'args'                    => array(
				'public'              => true,
				// Place menu item below Pages in the admin sidebar
				'menu_position'       => 20,
				'hierarchical'        => false,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions' ),
				'has_archive'         => 'small-groups/archive',
				'menu_icon'           => 'dashicons-groups',
				'rewrite'             => array(
					'slug'            => 'small-group',
					'with_front'      => false
				)
			),
			'help_menus'              => array(
				array(
					'screen'          => 'edit-iv_small_group',
					'tabs'            => array(
						array(
							'id'      => 'sg_overview',
							'title'   => 'Overview',
							'content' => '<p>Small groups are similar to pages in that they have a title, body text, and featured image. However, unlike pages, each small group also has a time, location, list of leaders, and contact information. Small groups can also be organized by college campus or by category.</p>'
						),
						array(
							'id'      => 'sg_managing',
							'title'   => 'Managing Small Groups',
							'content' => '<p>Managing small groups is very similar to managing pages, and the screens can be customized in the same way.</p>' .
							'<p>You can also perform the same types of actions, including narrowing the list by using the filters, acting on a small groups using the action links that appear when you hover over a row, or using the Bulk Actions menu to edit the metadata for multiple small groups at once.</p>'
						)
					),
					'sidebar'         => $this->get_help_sidebar()
				),
				array(
					'screen'          => 'iv_small_group',
					'tabs'            => array(
						array(
							'id'      => 'sg_creating',
							'title'   => 'Creating Small Groups',
							'content' => '<p>Creating a small group is very similar to creating a page, and the screens can be customized in the same way using drag and drop, the Screen Options tab, and expanding/collapsing boxes as you choose. The small group editor mostly works the same as the page editor, but there are several small group-specific boxes as well (including Details, Campus, and Category).</p>'
						)
					),
					'sidebar'         => $this->get_help_sidebar()
				)
			)
		) );

		// Add small group-specific columns to small group post list
		$iv_small_group->add_columns( array(
			array(
				'id'       => 'sg_time',
				'title'    => 'Time',
				'populate' => array( $this, 'populate_sg_time' )
			),
			array(
				'id'       => 'sg_location',
				'title'    => 'Location',
				'populate' => array( $this, 'populate_sg_location' ),
			),
			array(
				'id'       => 'sg_leaders',
				'title'    => 'Leaders',
				'populate' => array( $this, 'populate_sg_leaders' )
			),
			array(
				'id'       => 'sg_thumbnail',
				'title'    => 'Featured Image',
				'populate' => array( $this, 'populate_sg_thumbnail' )
			)
		) );

		// Remove Date column from small group post list, as date is irrelevant
		// for small groups
		$iv_small_group->remove_columns( array( 'date' ) );

	}

	// Adds and configures custom taxonomies
	public function add_taxonomies() {

		// Taxonomy for small group campuses
		$sg_campus = new Awesome_Taxonomy( array(
			'id'                      => 'sg_campus',
			'name'                    => array(
				'singular'            => 'campus',
				'plural'              => 'campuses'
			),
			'post_types'              => array( 'iv_small_group' ),
			// Add campus filter dropdown to small group edit screen
			'filterable'              => true,
			'args'                    => array(
				'hierarchical'        => true,
				'show_admin_column'   => true,
				'rewrite'             => array(
					'slug'            => 'small-groups/campus',
					'hierarchical'    => true,
					'with_front'      => false
				)
			),
			'help_menus'              => array(
				array(
					'screen'          => 'edit-sg_campus',
					'tabs'            => array(
						array(
							'id'      => 'sg_campus_overview',
							'title'   => 'Overview',
							'content' => '<p>You can use campuses to define sections of your site and group together small groups that belong to the same college campus. Campuses can also be nested to allow for even greater organization and structure.</p>' .
							'<p>Note that when you delete a campus, you do not delete the small groups which were assigned to that campus.</p>'
						)
					),
					'sidebar'         => $this->get_help_sidebar()
				)
			)
		) );

		// Taxonomy for small group categories
		$sg_category = new Awesome_Taxonomy( array(
			'id'                      => 'sg_category',
			'name'                    => array(
				'singular'            => 'category',
				'plural'              => 'categories'
			),
			'post_types'              => array( 'iv_small_group' ),
			'filterable'              => true,
			'args'                    => array(
				'hierarchical'        => true,
				'show_admin_column'   => true,
				'rewrite'             => array(
					'slug'            => 'small-groups/category',
					'hierarchical'    => true,
					'with_front'      => false
				)
			),
			'help_menus'              => array(
				array(
					'screen'          => 'edit-sg_category',
					'tabs'            => array(
						array(
							'id'      => 'sg_category_overview',
							'title'   => 'Overview',
							'content' => '<p>You can use categories to define sections of your site and group together related small groups. Categories can also be nested to allow for even greater organization and structure.</p>' .
							'<p>Note that when you delete a category, you do not delete the small groups which were assigned to that category.</p>'
						)
					),
					'sidebar'         => $this->get_help_sidebar()
				)
			)
		) );

	}

	// Indicates if Cyclone Slider 2 plugin is installed and active
	public function cyclone_slider_is_active() {

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		return is_plugin_active( 'cyclone-slider-2/cyclone-slider.php' );

	}

	// Adds and configures custom meta boxes
	public function add_meta_boxes() {

		// Meta box for providing small group details
		$sg_details = new Awesome_Meta_Box( array(
			'id'                  => 'sg_details',
			'title'               => 'Details',
			'post_types'          => array( 'iv_small_group' ),
			// The part of the edit screen where the meta box should show
			'context'             => 'normal',
			// The priority of the meta box within its context
			'priority'            => 'high',
			'fields'              => array(
				array(
					'id'          => 'sg_time',
					'name'        => '_sg_time',
					'type'        => 'text',
					'label'       => 'Time',
					'placeholder' => 'Enter the days and time of the small group'
				),
				array(
					'id'          => 'sg_location',
					'name'        => '_sg_location',
					'type'        => 'text',
					'label'       => 'Location',
					'placeholder' => 'Enter the location of the small group'
				),
				array(
					'id'          => 'sg_leaders',
					'name'        => '_sg_leaders',
					'type'        => 'text',
					'label'       => 'Leaders',
					'placeholder' => 'Enter the names of the small group leaders'
				)
			)
		) );

		// Meta box for providing small group contact info
		$sg_contact = new Awesome_Meta_Box( array(
			'id'                  => 'sg_contact',
			'title'               => 'Contact',
			'post_types'          => array( 'iv_small_group' ),
			'context'             => 'normal',
			'priority'            => 'high',
			'fields'              => array(
				array(
					'id'          => 'sg_contact_name',
					'name'        => '_sg_contact_name',
					'type'        => 'text',
					'label'       => 'Name',
					'placeholder' => 'Enter the name of the person to contact'
				),
				array(
					'id'          => 'sg_contact_phone',
					'name'        => '_sg_contact_phone',
					'type'        => 'text',
					'label'       => 'Phone',
					'placeholder' => 'Enter the phone number to contact'
				),
				array(
					'id'          => 'sg_contact_email',
					'name'        => '_sg_contact_email',
					'type'        => 'text',
					'label'       => 'Email',
					'placeholder' => 'Enter the email address to contact'
				)
			)
		) );

		if ( $this->cyclone_slider_is_active() ) {

			// Meta box for adding slider above page content
			$iv_slider = new Awesome_Meta_Box( array(
				'id'              => 'iv_slider',
				'title'           => 'Featured Slider',
				'post_types'      => array( 'page' ),
				'context'         => 'side',
				'priority'        => 'low',
				'fields'          => array(
					array(
						'id'      => 'iv_slider_id',
						'name'    => '_iv_slider_id',
						'type'    => 'select',
						'options' => array( $this, 'get_iv_slider_options' )
					)
				)
			) );

		}

	}

	// Populates slider dropdown in IV slider meta box
	public function get_iv_slider_options( $meta_value, $field, $post ) {

		// Create options array for dropdown menu
		$options = array();
		// Add option to indicate that no slider has been set
		$options[] = array(
			'value'    => '',
			'content'  => '(no slider)',
			// Select this option if no slider is set
			'selected' => empty( $meta_value )
		);

		if ( $this->cyclone_slider_is_active() ) {

			// Attempt to retrieve existing meta sliders
			$sliders = get_posts( array(
				'post_type'      => 'cycloneslider',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC'
			) );
			// Output name of each meta slider as menu item
			foreach ( $sliders as $slider ) {
				$slider_id = strval( $slider->ID );
				$options[] = array(
					// The slider shortcode is saved to the DB
					'value'    => $slider_id,
					// Identify slider using slider title
					'content'  => $slider->post_title,
					// Select the slider with the saved shortcode
					'selected' => ( $slider_id === $meta_value )
				);
			}

		}
		return $options;

	}

	// Adds edit links to admin bar for convenience
	public function add_admin_bar_items( $wp_admin_bar ) {

		if ( is_page() && $this->cyclone_slider_is_active() ) {

			$page = get_queried_object();
			// If page has slider assigned to it
			$slider_id = get_post_meta( $page->ID, '_iv_slider_id', true );
			if ( $slider_id ) {
				// Add edit link for page slider in admin bar
				$wp_admin_bar->add_node( array(
					'id'     => 'edit-slider',
					'title'  => 'Edit Slider',
					'parent' => 'edit',
					'href'   => home_url( "/wp-admin/post.php?post={$slider_id}&action=edit" )
				) );
			}

		} else if ( is_tax( 'sg_campus' ) || is_tax( 'sg_category' ) ) {

			$term = get_queried_object();
			// Add link for managing all small groups to admin bar
			$wp_admin_bar->add_node( array(
				'id'     => 'edit-small-groups',
				'title'  => 'Edit Small Groups',
				'parent' => 'edit',
				'href'   => home_url( "/wp-admin/edit.php?post_type=iv_small_group&{$term->taxonomy}={$term->slug}" )
			) );

		}

	}

	// Facebook Like/Share button shortcode
	public function iv_facebook_like_button_shortcode( $atts ) {

		// Merge default attribute values with those given
		$atts = shortcode_atts( array(
			'href'       => '',
			'width'      => 300,
			'layout'     => 'standard',
			'action'     => 'like',
			'show-faces' => 'false',
			'share'      => 'false'
		), $atts );

		ob_start();
		?>
		<div class="fb-like"
			data-href="<?php echo $atts['href']; ?>"
			data-width="<?php echo $atts['width']; ?>"
			data-layout="<?php echo $atts['layout']; ?>"
			data-action="<?php echo $atts['action']; ?>"
			data-show-faces="<?php echo $atts['show-faces'] ?>"
			data-share="<?php echo $atts['share']; ?>"></div>
		<?php
		return trim( ob_get_clean() );

	}

	// Adds InterVarsity-related shortcodes
	public function add_shortcodes() {

		add_shortcode( 'sg-time', 'get_the_sg_time' );
		add_shortcode( 'sg-location', 'get_the_sg_location' );
		add_shortcode( 'sg-leaders', 'get_the_sg_leaders' );
		add_shortcode( 'sg-contact-name', 'get_the_sg_contact_name' );
		add_shortcode( 'sg-contact-phone', 'get_the_sg_contact_phone' );
		add_shortcode( 'sg-contact-email', 'get_the_sg_contact_email' );
		add_shortcode( 'iv-facebook-like-button', array( $this, 'iv_facebook_like_button_shortcode' ) );

	}

	// Enqueues necessary frontend stylesheets and scripts
	public function enqueue_frontend_scripts() {

		// Enqueue Facebook scripts which enable Facebook Like Button
		wp_enqueue_script(
			'iv-facebook-scripts',
			IV_PLUGIN_DIR_URI . '/scripts/facebook.min.js',
			// jQuery is required for DOM manipulation
			array( 'jquery' ),
			// Script has no version associated with it
			false,
			// Place script at the end of the page <body>
			true
		);

	}

	// Enqueues necessary backend (admin) stylehseets and scripts
	public function enqueue_admin_styles() {

		// Enqueue stylesheet for admin interface and meta boxes
		wp_enqueue_style(
			'iv-admin',
			IV_PLUGIN_DIR_URI . '/styles/css/admin.css'
		);

	}

	// Extends small group searches to recognize time, location, etc.
	public function extend_sg_search( $search, &$wp_query ) {
		global $wpdb;

		// Stop if the given SQL clause is empty
		if ( empty( $search ) ) {
			return $search;
		}

		// Stop if user is not searching for small groups
		if ( 'iv_small_group' !== $wp_query->get( 'post_type' ) ) {
			return $search;
		}

		// Get search query from WP_Query object
		$search_query = $wp_query->query_vars['s'];
		// Convert query to lowercase and remove irrelevant characters
		$search_query = strtolower( $search_query );
		$search_query = preg_replace( '/[^a-z\- ]/', '', $search_query );
		// Search query must be passed to preg_quote() twice
		// so that the regex characters are properly escaped
		// for use by MySQL
		$search_query = preg_quote( preg_quote( $search_query ) );
		// Escape quotes within search query
		$search_query = esc_sql( $search_query );
		// Parse search terms into array
		$terms = explode( ' ', $search_query );
		// If terms cannot be parsed or term list is empty
		if ( false === $terms || 0 === count( $terms ) ) {
			// Use entire search query as the only term
			$terms = array( $search_query );
		}

		// Reset SQL clause
		$search = '';
		// Loop through search terms
		foreach ( $terms as $term ) {
			// Do not search for term if empty
			if ( empty( $term ) ) {
				continue;
			}
			// Make terms like "women" and "womens" equivalent
			if ( substr( $term, -1 ) !== 's' ) {
				$term .= 's';
			}
			$term .= '?';
			// Store term as regex for reuse throughout query
			// Surround term with word boundaries to match only whole words
			$term_regex = "\[\[:<:\]\]($term)\[\[:>:\]\]";
			// Extend SQL clause to search across
			// all small group data for each term
			$search .= " AND (
				`$wpdb->posts`.`post_title` REGEXP '$term_regex'
				OR `$wpdb->posts`.`post_name` REGEXP '$term_regex'
				OR EXISTS (
					SELECT * FROM `$wpdb->postmeta`
					WHERE `$wpdb->postmeta`.`post_id` = `$wpdb->posts`.`ID`
					AND (
						`$wpdb->postmeta`.`meta_key` = '_sg_time'
						OR `$wpdb->postmeta`.`meta_key` = '_sg_location'
						OR `$wpdb->postmeta`.`meta_key` = '_sg_leaders'
					)
					AND `$wpdb->postmeta`.`meta_value` REGEXP '$term_regex'
				)
				OR EXISTS (
					SELECT * FROM `$wpdb->terms`
						INNER JOIN `$wpdb->term_taxonomy`
						ON `$wpdb->term_taxonomy`.`term_id` = `$wpdb->terms`.`term_id`
						INNER JOIN `$wpdb->term_relationships`
						ON `$wpdb->term_relationships`.`term_taxonomy_id` = `$wpdb->term_taxonomy`.`term_taxonomy_id`
						WHERE (
							`taxonomy` = 'sg_campus'
							OR `taxonomy` = 'sg_category'
						)
						AND `object_id` = `$wpdb->posts`.`ID`
						AND `$wpdb->terms`.`name` REGEXP '$term_regex'
				)
			)";
		}
		return $search;
	}

	// Adds post type to "At a Glance" dashboard widget
	public function add_post_type_to_dashboard( $post_type, $singular_name, $plural_name ) {

		// Get object containing counts for post type
		$post_counts = wp_count_posts( $post_type );
		// Get singular/plural noun to accompany count
		$pluralized = _n( "1 $singular_name", number_format_i18n( $post_counts->publish ) . " $plural_name", $post_counts->publish );
		?>
		<li class="<?php echo $post_type; ?>-count">
			<?php if ( current_user_can( 'edit_posts' ) ): ?>
				<a href="edit.php?post_type=<?php echo $post_type; ?>"><?php echo $pluralized; ?></a>
			<?php else: ?>
				<span><?php echo $pluralized; ?></span>
			<?php endif; ?>
		</li>
		<?php

	}

	// Adds post types to the "At a Glance" box in Dashboard
	public function add_post_types_to_dashboard( $items ) {

		$this->add_post_type_to_dashboard( 'iv_small_group', 'Small Group', 'Small Groups' );
		if ( $this->cyclone_slider_is_active() ) {
			$this->add_post_type_to_dashboard( 'cycloneslider', 'Slider', 'Sliders' );
		}
		return $items;

	}

}

// Template functions for retrieving small group data (used in The Loop)

function get_the_sg_time() {
	global $post;
	return trim( get_post_meta( $post->ID, '_sg_time', true ) );
}
function get_the_sg_location() {
	global $post;
	return trim( get_post_meta( $post->ID, '_sg_location', true ) );
}
function get_the_sg_leaders() {
	global $post;
	return trim( get_post_meta( $post->ID, '_sg_leaders', true ) );
}
function get_the_sg_contact_name() {
	global $post;
	return trim( get_post_meta( $post->ID, '_sg_contact_name', true ) );
}
function get_the_sg_contact_phone() {
	global $post;
	return trim( get_post_meta( $post->ID, '_sg_contact_phone', true ) );
}
function get_the_sg_contact_email() {
	global $post;
	return trim( get_post_meta( $post->ID, '_sg_contact_email', true ) );
}

// Template functions for outputting small group data (also used in The Loop)

function the_sg_time() {
	global $post;
	echo get_the_sg_time( $post->ID );
}
function the_sg_location() {
	global $post;
	echo get_the_sg_location( $post->ID );
}
function the_sg_leaders() {
	global $post;
	echo get_the_sg_leaders( $post->ID );
}
function the_sg_contact_name() {
	global $post;
	echo get_the_sg_contact_name( $post->ID );
}
function the_sg_contact_phone() {
	global $post;
	echo get_the_sg_contact_phone( $post->ID );
}
function the_sg_contact_email() {
	global $post;
	echo get_the_sg_contact_email( $post->ID );
}
$iv_plugin = new InterVarsity_Plugin();
