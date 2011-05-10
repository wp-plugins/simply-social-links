<?php
/*
Plugin Name: Simply Social Links
Plugin URI: http://maathe.us/blog/simply-social-widgets
Description: A simple way to add social links (like: facebook, twitter, tumblr, last.fm, flickr, plurk, etc) to your Links (Bookmarks?!). <br> Uma forma ~simples~ de adicionar informações aos links (bookmarks?!), como: twitter, tumblr, last.fm, facebook, flickr, orkut, etc. Ainda testando.
Version: 0.5
Author: Matheus Eduardo (@matheuseduardo)
Author URI: http://maathe.us/blog
Donate URI: http://maathe.us/blog/pague-me-um-cafe
Last change: 04/05/2011
Licence: GPL
*/

/*  Copyright 2010  Matheus Eduardo  (email : matheuseduardo.com@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


class simplySocialLinks {
	
	private static $wpdb;
	private static $info;
	private static $PLUGIN_PATH;
	
	public static function adicionarMenu() {
		
	}
	
	public static function adicionarWidgets() {
		
	}
	
	public static function teste() {
		//update_link_meta(8, 'ssl_network[twitter]', 'http://twitter.com/gustavobarbosa');
		//update_link_meta(8, 'ssl_network[flickr]', 'http://flickr.com/gustavobarbosa');
	}
	
	public static function admin_inicializar() {
		
		error_reporting(E_ALL ^ E_NOTICE);
		
		if ( !defined('WP_CONTENT_URL') )
			define('WP_CONTENT_URL', site_url() . '/wp-content');
		if ( !defined('WP_PLUGIN_URL') )
			define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
		
		global $wpdb;
		// echo '<pre>'; print_r($wpdb); exit();
		
		
		$wpdb->linkmeta = $wpdb->prefix . 'linkmeta';
		$wpdb->tables = array_merge($wpdb->tables, array('linkmeta'));
		simplySocialLinks::$PLUGIN_PATH = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		
		define('SSL_BASENAME', plugin_basename(__FILE__));
		define('SSL_BASEDIR', dirname( plugin_basename(__FILE__)));
		define('SSL_BASE', rtrim (dirname (__FILE__), '/'));
		define('SSL_TEXTDOMAIN', 'simply-social-links' );
		
		
		add_action('admin_head', array('simplySocialLinks','social_links_javascript'));
		add_action('wp_ajax_add_social_network_link', array('simplySocialLinks','add_social_network_link'));
		add_action('wp_ajax_delete_social_network_link', array('simplySocialLinks','delete_social_network_link'));
		add_action('add_meta_boxes_link', array('simplySocialLinks','social_links_metabox'));
		add_filter( 'plugin_action_links_' . SSL_BASENAME, array('simplySocialLinks', 'add_settings_link' ));
		
		// 
		add_action('deleted_link', array('simplySocialLinks', 'social_links_clear'));
		
		add_action('admin_print_scripts-link-add.php', array('simplySocialLinks', 'social_links_admin_scripts'));
		add_action('admin_print_styles-link-add.php', array('simplySocialLinks', 'social_links_admin_styles'));
		
		add_action('admin_print_scripts-link.php', array('simplySocialLinks', 'social_links_admin_scripts'));
		add_action('admin_print_styles-link.php', array('simplySocialLinks', 'social_links_admin_styles'));
		
		add_action('admin_print_scripts-link-manager.php', array('simplySocialLinks', 'social_links_admin_scripts'));
		add_action('admin_print_styles-link-manager.php', array('simplySocialLinks', 'social_links_admin_styles'));
		
		
		error_reporting(0);
	}
	
	
	public static function inicializar() {
		
		error_reporting(E_ALL ^ E_NOTICE);
		
		global $wpdb;
		$wpdb->linkmeta = $wpdb->prefix . 'linkmeta';
		$wpdb->tables = array_merge($wpdb->tables, array('linkmeta'));
		
		// upgrade jquery version (plugin will be updated until WP load at least 1.5.x version)
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js');
		wp_enqueue_script('jquery');
		
		load_plugin_textdomain( 'simply-social-links', false, dirname( plugin_basename( __FILE__ )));
		
		// defining functions to work like wordpress native functions
		// (ex.: get_post_meta, update_post_meta, etc)
		
		if (!function_exists('get_link_meta')) {
			function get_link_meta($link_id, $key, $single = false) {
				return get_metadata('link', $link_id, $key, $single);
			}
		}
		
		if (!function_exists('update_link_meta')) {
			function update_link_meta($link_id, $meta_key, $meta_value, $prev_value = '') {
				return update_metadata('link', $link_id, $meta_key, $meta_value, $prev_value);
			}
		}
		
		if (!function_exists('delete_link_meta')) {
			function delete_link_meta($link_id, $meta_key, $meta_value = '') {
				return delete_metadata('link', $link_id, $meta_key, $meta_value);
			}
		}
		
		error_reporting(0);
	}
	
	
	public static function social_links_metabox() {
		add_meta_box('social_links_metabox', __('Social Networks Links','simply-social-links'),array('simplySocialLinks','metabox_social_links_setup'), 'link', 'normal', 'high');
		
	}
	
	public static function social_links_admin_scripts() {
		wp_enqueue_script('social_links_js', simplySocialLinks::$PLUGIN_PATH . 'social-links.js', array('jquery'), '0.1', false);
		
	}
	public static function social_links_admin_styles() {
		wp_enqueue_style('social_links_css', simplySocialLinks::$PLUGIN_PATH . 'social-links.css');
	}
	
	public static function add_settings_link( $links, $file ) {
		array_unshift(
			$links,
			sprintf( '<a id="simply-social-links" href="javascript:void(0)" title="Configure this plugin">%s</a>', __('Settings') )
		);
		
		return $links;
	}
	
	public static function metabox_social_links_setup($link) {
		error_reporting(E_ALL ^ E_NOTICE);
		
		global $wpdb;
		
		include('social-links.php');
		
		error_reporting(0);
	}
	
	
	
	
	
	
	
	public static function social_links_javascript() {
		
	}
	
	public static function add_social_network_link() {
		
		global $wpdb;
		
		sleep(1);
		
		$lid = $_GET['link_id'];
		$site = $_GET['site'];
		$url = $_GET['url'];
		
		$pre_query = "INSERT INTO $wpdb->linkmeta (`meta_id`, `link_id`, `meta_key`, `meta_value`) VALUES (null, %d, '_ssl_network[$site]', %s) ";
		
		$q = $wpdb->prepare("INSERT INTO $wpdb->linkmeta (`meta_id`, `link_id`, `meta_key`, `meta_value`) VALUES (null, %d, '_ssl_network[$site]', %s) ", $lid, $url);
		//print_r($q);exit;
		$wpdb->query($q);
		
		echo('{ "sucesso": "S", "mensagem" : "' . __('Link successfully added.','simply-social-links') . '" }');
		
		die();
	}
	
	public static function delete_social_network_link() {
		error_reporting(E_ALL ^ E_NOTICE);
		
		global $wpdb;
		
		sleep(1);
		
		$mid = $_GET['meta_id'];
		$lid = $_GET['link_id'];
		
		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->linkmeta WHERE link_id = %d and meta_id = %d ", $lid, $mid));
		
		echo('{ "sucesso": "S", "mensagem" : "' . __('Link successfully deleted.','simply-social-links') . '" }');
		
		error_reporting(0);
		die();
	}
	
	
	public static function social_links_clear($link_id) {
		global $wpdb;
		
		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->linkmeta WHERE link_id = %d", $link_id));
		
	}
	
	
	public static function instalar() {
		global $wpdb;
		$charset_collate = "";
		
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
		
		$sql = sprintf('CREATE TABLE IF NOT EXISTS `%slinkmeta` (
		  `meta_id` bigint(20) UNSIGNED NOT NULL auto_increment,
		  `link_id` bigint(20) UNSIGNED NOT NULL,
		  `meta_key` varchar(255),
		  `meta_value` longtext,
		  PRIMARY KEY (`meta_id`)
	   ) %s ',$wpdb->prefix,$charset_collate);
		
		//echo "<pre> $sql"; exit();
		$wpdb->query($sql);
	}
	
	public static function desinstalar() {
		return new WP_Error('OK','Desinstalado com sucesso!');
	}
	
	public static function apagar() {
		
	}
	
}

register_activation_hook( __FILE__, array('simplySocialLinks', 'instalar'));
register_deactivation_hook( __FILE__, array('simplySocialLinks', 'desinstalar'));
register_uninstall_hook( __FILE__, array('simplySocialLinks', 'apagar'));

add_action('init', array('simplySocialLinks','inicializar'));
add_action('admin_init', array('simplySocialLinks','admin_inicializar'));











?>