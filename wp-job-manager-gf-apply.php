<?php
/**
 * Plugin Name: WP Job Manager - Apply With Gravity Forms
 * Plugin URI:  https://github.com/astoundify/wp-job-manager-companies
 * Description: Apply to jobs that have added an email address via Gravity Forms
 * Author:      Astoundify
 * Author URI:  http://astoundify.com
 * Version:     1.0
 * Text Domain: ajmgfa
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Astoundify_Job_Manager_Apply {

	/**
	 * @var $instance
	 */
	private static $instance;

	private $form_id;

	/**
	 * Make sure only one instance is only running.
	 */
	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start things up.
	 *
	 * @since WP Job Manager - Apply with Gravity Forms 1.0
	 */
	public function __construct() {
		$this->form_id = get_option( 'job_manager_gravity_form' );

		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Set some smart defaults to class variables. Allow some of them to be
	 * filtered to allow for early overriding.
	 *
	 * @since WP Job Manager - Apply with Gravity Forms 1.0
	 *
	 * @return void
	 */
	private function setup_globals() {
		$this->file         = __FILE__;
		
		$this->basename     = apply_filters( 'ajmgfa_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_dir   = apply_filters( 'ajmgfa_plugin_dir_path',  plugin_dir_path( $this->file ) );
		$this->plugin_url   = apply_filters( 'ajmgfa_plugin_dir_url',   plugin_dir_url ( $this->file ) );

		$this->lang_dir     = apply_filters( 'ajmgfa_lang_dir',     trailingslashit( $this->plugin_dir . 'languages' ) );
		$this->domain       = 'ajmgfa';
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since WP Job Manager - Apply with Gravity Forms 1.0
	 *
	 * @return void
	 */
	private function setup_actions() {
		add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
		add_filter( 'gform_field_value_job_email', array( $this, 'field_value_job_email' ) );
		add_action( 'gform_enqueue_scripts_' . $this->form_id, array( $this, 'javascript' ) );
	}

	public function job_manager_settings( $settings ) {
		$settings[ 'job_listings' ][1][] = array(
			'name'       => 'job_manager_gravity_form',
			'std'        => null,
			'label'      => __( 'Gravity Form ID', 'ajmgfa' ),
			'desc'       => __( 'The ID of the Gravity Form you created for applications.', 'ajmgfa' ),
			'attributes' => array()
		);

		return $settings;
	}

	public function field_value_job_email() {
		global $post;
		
		return $post->_application;
	}

	public function javascript() {
		echo '<script type="text/javascript">';
		echo 'jQuery(function($) { $( ".gfield.disabled input" ).attr( "readonly", "readonly" ); });';
		echo '</script>';
	}
}
add_action( 'init', array( 'Astoundify_Job_Manager_Apply', 'instance' ) );