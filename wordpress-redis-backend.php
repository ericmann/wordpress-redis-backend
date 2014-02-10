<?php
/**
 * WordPress Redis Backend
 *
 * A WordPress object cache that uses Redis for storage.
 *
 * @package   wordpress_redis_backend
 * @author    Eric Mann <eric@eamann.com>
 * @license   GPL-2.0+
 * @link      http://eamann.com
 * @copyright 2014 Eric Mann
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Redis Backend
 * Plugin URI:        https://github.com/ericmann/wordpress-redis-backend
 * Description:       A WordPress object cache that uses Redis for storage.
 * Version:           1.0.0
 * Author:            Eric Mann
 * Author URI:        http://eamann.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/ericmann/wordpress-redis-backend
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once 'vendor/autoload.php';

register_activation_hook( __FILE__, array( 'WordPress_Redis_Backend', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WordPress_Redis_Backend', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WordPress_Redis_Backend', 'get_instance' ) );

class WordPress_Redis_Backend {

	const VERSION = '1.0.0';
	protected $plugin_slug = 'wordpress-redis-backend';
	protected static $instance = null;
	public static $filename = '/object-cache.php';
	public static $file_moved = false;

	private function __construct(){
		add_action( 'admin_init', array( $this, 'validations' ) );
	}

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate( $network_wide ) {
		self::move_file();
	}

	public static function move_file( $force = false ){
		if( ! self::cache_file_exists() || $force ){
			$from = self::plugin_object_cache();
			$to = self::content_object_cache();
			if ( copy( $from, $to ) ) {
				// Let's store a hash of the file so we can detect later if it's been
				// changed or not.
				update_site_option( 'wrb_file_hash', self::hash_cache_file() );
				self::$file_moved = true;
			}
		}
	}

	public static function plugin_object_cache(){
		return dirname( __FILE__ ) . self::$filename;
	}

	public static function content_object_cache(){
		return WP_CONTENT_DIR . self::$filename;
	}

	public static function cache_file_exists(){
		return file_exists( self::content_object_cache() );
	}

	public static function hash_cache_file(){
		return hash_file( 'md5', self::content_object_cache() );
	}

	public static function hash_plugin_cache_file(){
		return hash_file( 'md5', self::plugin_object_cache() );
	}

	public static function cache_file_not_modified(){
		return ( get_site_option( 'wrb_file_hash', false ) == @self::hash_cache_file() );
	}

	public static function hashes_match(){
		return ( self::hash_cache_file() === self::hash_plugin_cache_file() );
	}

	public static function current_uri(){
		$scheme = ( is_ssl() ) ? 'https' : 'http';
		return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
	}

	public static function deactivate( $network_wide ) {

		// If object-cache.php has changed at all, let's not change it
		// in case another plugin or something has overwritten the file.
		if( self::cache_file_not_modified() ){
			unlink( self::content_object_cache() );
		}
		delete_site_option( 'wrb_file_hash' );

	}

	public function validations(){

		if( self::isset_true( $_REQUEST['wrb-move-file'] )){
			$force = self::force_move();
			if ( ! self::cache_file_exists() || $force ){
				self::move_file( $force );
			}
		}

		if( ! self::cache_file_exists() ){
			$url = self::current_uri() . '?wrb-move-file=true';
			$message = '<strong>WordPress Redis Backend:</strong> File object-cache.php doesn\'t exist in the content directory. ';
			$message .= '<a href="' . $url . '">Move the file</a>';
			self::admin_notice( $message, 'error');
		}

		if( self::cache_file_exists() && ! self::hashes_match() ){
			$url = self::current_uri() . '?wrb-move-file=true&wrb-force=true';
			$message = '<strong>WordPress Redis Backend:</strong> An object-cache.php file already exists in the content directory. ';
			$message .= 'Would you like to overwrite? ';
			$message .= '<a href="' . $url . '">Overwrite</a>';
			self::admin_notice( $message, 'error');
		}

		if( self::$file_moved ){
			self::admin_notice( '<strong>WordPress Redis Backend:</strong> File moved sucessfully' );
		}

	}

	public static function force_move(){
		return self::isset_true( $_REQUEST['wrb-force'] );
	}

	public static function isset_true( $var ){
		return isset( $var ) && true == $var;
	}

	public static function admin_notice( $message, $class = 'updated' ){
		if(
			( ! is_multisite() && current_user_can( 'manage_options' ) )
			|| ( is_multisite() && current_user_can( 'manage_network_options' ) )
		){
			new WP_Admin_Notice( $message, $class );
		}
	}
}
