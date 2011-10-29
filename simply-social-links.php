<?php
/*
Plugin Name: Simply Social Links
Plugin URI: http://maathe.us/blog/simply-social-links
Description: A simple way to add social links (like: facebook, twitter, tumblr, last.fm, flickr, plurk, etc) to your Links (Bookmarks?!).
Version: 0.6.1
Author: Matheus Eduardo (@matheuseduardo)
Author URI: http://maathe.us/blog
Donate URI: http://maathe.us/blog/pague-me-um-cafe
Last change: 05/18/2011
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
		
		simplySocialLinks::$PLUGIN_PATH = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		
		global $wpdb;
		$wpdb->linkmeta = $wpdb->prefix . 'linkmeta';
		$wpdb->tables = array_merge($wpdb->tables, array('linkmeta'));
		
		// upgrade jquery version (plugin will be updated until WP load at least 1.5.x version)
		// update: 05/Jun/2011 - wordpress 3.2 (still beta) do this!
		if (get_bloginfo('version') < "3.2") {;
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js');
			wp_enqueue_script('jquery');
		}
		
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
		
		
		//add_filter('link_url',array('simplySocialLinks', 'link_after_filter'));
		add_filter('link_after', array('simplySocialLinks', 'link_after_filter'), 10, 2);
		
		//global $wp_filter;
		//$x = has_filter('wp_list_bookmarks');
		//echo '<pre>';print_r($wp_filter); exit;
		
		error_reporting(0);
	}
	
	
	public static function social_links_metabox() {
		add_meta_box('social_links_metabox', __('Social Networks Links','simply-social-links'),array('simplySocialLinks','metabox_social_links_setup'), 'link', 'normal', 'high');
		
	}
	
	public static function social_links_admin_scripts() {
		wp_enqueue_script('social_links_js', simplySocialLinks::$PLUGIN_PATH . 'social-links.js', array('jquery'), '0.1', false);
		wp_localize_script('social_links_js', 'SocialLinks', simplySocialLinks::js_localize_vars());
		
	}
	public static function social_links_admin_styles() {
		wp_enqueue_style('social_links_css', simplySocialLinks::$PLUGIN_PATH . 'social-links.css');
	}
	
	public static function add_settings_link( $links, $file ) {
		array_unshift(
			$links,
			sprintf( '<a id="simply-social-links" href="javascript:alert(\'Not working *yet*\');void(0)" title="Configure this plugin">%s</a>', __('Settings') )
		);
		
		return $links;
	}
	
	public static function metabox_social_links_setup($link) {
		error_reporting(E_ALL ^ E_NOTICE);
		
		global $wpdb;
		
		include('social-links.php');
		
		error_reporting(0);
	}
	
	
	public static function link_after_filter($output, $bookmark) {
		error_reporting(E_ALL);
		//echo "\r\n--output--\r\n";  print_r($output); echo "\r\n--bookmark--\r\n"; print_r($bookmark);echo "\r\n--fim--";
		
		$options = get_option('simplysociallink-options');
		
		if (!isset($options['theme'])) {
			$themefolder = simplySocialLinks::$PLUGIN_PATH . "themes/default/";
		}
		else {
			$themefolder = simplySocialLinks::$PLUGIN_PATH . "themes/" . $options['theme'] . "/";
		}
		
		$links = '<span class="ss-links">';
		foreach (simplySocialLinks::get_social_links($bookmark->link_id) as $sociallink):
			$links .= ' <a href="' . $sociallink['url'] . '" class="ssl_'. $sociallink['network'] .'" target="_blank"><img src="' . $themefolder. $sociallink['network'] . '.gif" class="" /></a>';
		endforeach;
		$links .= '</span>';
		
		return $output . $links;
	}
	
	
	
	public static function get_social_links($link_id) {
		global $wpdb;
		$retorno = array();
		
		$resultado = $wpdb->get_results($wpdb->prepare("select * from $wpdb->linkmeta where link_id = %d and meta_key like '_ssl_network%%' ", $link_id), ARRAY_A);
		
		if (count($resultado)>0) :
		
			foreach($resultado as $linha):
				
				$padrao = '/^_ssl_network\[(.*)\]$/i';
				preg_match($padrao, $linha['meta_key'], $rede);
				$rede = $rede[1];
				$url = $linha['meta_value'];
				$mid = $linha['meta_id'];
				
				$retorno[] = array('network' => $rede, 'url' => $url, 'meta_id' => $mid);
				
			endforeach;
			
		endif;
		
		return $retorno;
	}
	
	public static function add_social_links_to_array() {
	}
	
	
	public static function js_localize_vars() {
		return array(
			'SiteUrl' => get_bloginfo('url'),
			'successfully_added' => __('Link successfully added.','simply-social-links'),
			'successfully_deleted' => __('Link successfully deleted.','simply-social-links'),
			'list' => __('List','simply-social-links'),
			'add' => __('Add','simply-social-links'),
			'delete' => __('Delete','simply-social-links'),
			'visit' => __('Visit','simply-social-links'),
			'error' => __('Error','simply-social-links')
		);
	}
	
	public static function add_social_network_link() {
		
		global $wpdb;
		
		sleep(1);
		
		$lid = $_GET['link_id'];
		$site = $_GET['site'];
		$url = $_GET['url'];
		
		// $q = $wpdb->prepare("INSERT INTO $wpdb->linkmeta (`meta_id`, `link_id`, `meta_key`, `meta_value`) VALUES (null, %d, '_ssl_network[$site]', %s) ", $lid, $url);
		// print_r($q);exit;
		// $wpdb->query($q);
		
		$mid = $wpdb->insert($wpdb->linkmeta, array('link_id' => $lid, 'meta_key' => "_ssl_network[$site]", 'meta_value'=> $url ), array( '%d', '%s', '%s' ));
		
		if ($mid === false):
			echo('{ "sucesso": "N", "mensagem" : "' . __('Failed to add link.','simply-social-links') . '" }');
		else:
			echo('{ "sucesso": "S", "mensagem" : "' . __('Link successfully added.','simply-social-links') . '", "link": { "mid": "' . $mid . '"} }');
		endif;
		
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
		update_option('simplysociallink-options', array('theme' => 'default'));
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

require_once('bookmark-template-ssl.php');









?>