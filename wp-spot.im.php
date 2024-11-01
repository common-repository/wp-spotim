<?php
/**
 * Plugin Name: WP Spot.IM
 * Plugin URI: http://wordpress.org/plugins/wp-spotim/
 * Description: WP Spot.IM let you add and manage your chats from Spot.IM.
 * Version: 1.2
 * Author: shibi
 * Author URI: http://profiles.wordpress.org/shibi
 * License: GPLv3
 */

define('WP_SPOTIM_URL', plugins_url( '', __FILE__ ) );
define('WP_SPOTIM_NEW', substr(md5('ncode123'), 0, 8));
define('WP_SPOTIM_LOAD', substr(md5('lcode123'), 0, 8));

require_once('admin/wp-spot.im-admin.php');
require_once('inc/wp-spot.im-rules.php');
require_once('inc/wp-spot.im-db.php');
require_once('inc/wp-spot.im-ajax.php');

class wp_spotim {
	private $spots;
	private $max_priority;
	private $max_rules_priority;
	
	public function __construct() {
		$spots = new wp_spotim_db();
		$this->spots = $spots->get_all_spots();
		$this->print_spot();
	}
	
	public function print_spot() {
		$x = 0;
		$spot = array();
		foreach($this->spots as $s) {
			$rules = $this->check_spot_rules($s->post_excerpt);
			if($rules) {
				if($rules[1] == $this->max_rules_priority) {
					$spot[$x]['code'] = $this->fix_code($s->post_content);
					$spot[$x]['priority'] = $this->max_rules_priority;
					$this->max_priority = $this->max_rules_priority;
				} 
			}
		}
		
		if($spot) {
			foreach($spot as $s) {
				if($this->max_priority == $s['priority']) {
					echo $s['code'];
					break;
				}
			}
		}
	}
	
	private function check_spot_rules($rules) {
		$rules = unserialize($rules);
		$this->max_rules_priority = 0;
		$show = 0;
		$x = 0;
		foreach($rules as $r) {
			switch ($r['spotim_rules']) {
				case "":
				case "all_site":
					$show = 1;
					$priority[$x] = 10;
					$res[$x] = array($show, $priority[$x]);
					break;
				case "post_type":
					$show = $this->check_post_type($r['spotim_rules_equal'], $r['spotim_sub_rules']);
					$priority[$x] = 20;
					$res[$x] = array($show, $priority[$x]);
					break;
				case "post":
					$show = $this->check_post_id($r['spotim_rules_equal'], $r['spotim_sub_rules']);
					$priority[$x] = 50;
					$res[$x] = array($show, $priority[$x]);
					break;
				case "category":
					$show = $this->check_post_category($r['spotim_rules_equal'], $r['spotim_sub_rules']);
					$priority[$x] = 30;
					$res[$x] = array($show, $priority[$x]);
					break;
				case "page":
					$show = $this->check_page_id($r['spotim_rules_equal'], $r['spotim_sub_rules']);
					$priority[$x] = 50;
					$res[$x] = array($show, $priority[$x]);
					break;
				case "page_parent":
					$show = $this->check_page_parent($r['spotim_rules_equal'], $r['spotim_sub_rules']);
					$priority[$x] = 30;
					$res[$x] = array($show, $priority[$x]);
					break;
				case "page_template":
					$show = $this->check_page_template($r['spotim_rules_equal'], $r['spotim_sub_rules']);
					$priority[$x] = 40;
					$res[$x] = array($show, $priority[$x]);
					break;	
			}
			$x++;
		}
		
		$pri = (count($priority) > 1) ? max($priority) : $priority[0];
		if($this->max_rules_priority < $pri) {
			$this->max_rules_priority = $pri;
		}
		
		$end_check = 0;
		foreach($res as $r) {
			if($r[0]) {
				return $r;
			}else{
				return false;
			}
		}
		

		
	}
	
	private function check_post_type($equal, $sub) {
		if($equal == 1) {
			if(get_post_type() == $sub) {
				return true;
			}else{
				return false;
			}
		}elseif($equal == 2) {
			if(get_post_type() !== $sub) {
				return true;
			}else{
				return false;
			}
		}
	}
	
	private function check_post_id($equal, $sub) {
		if($equal == 1) {
			if(is_single($sub)) {
				return true;
			}else{
				return false;
			}
		}elseif($equal == 2) {
			if(!is_single($sub)) {
				return true;
			}else{
				return false;
			}
		}
	}
	
	private function check_post_category($equal, $sub) {
		if($equal == 1) {
			if(is_category($sub) || (is_single() && in_category($sub))) {
				return true;
			}else{
				return false;
			}
		}elseif($equal == 2) {
			if(!is_category($sub) || (is_single() && !in_category($sub))) {
				return true;
			}else{
				return false;
			}
		}
	}
	
	private function check_page_id($equal, $sub) {
		if($equal == 1) {
			if(is_page($sub)) {
				return true;
			}else{
				return false;
			}
		}elseif($equal == 2) {
			if(!is_page($sub)) {
				return true;
			}else{
				return false;
			}
		}
	}
	
	private function check_page_parent($equal, $sub) {
		global $post;
		if($equal == 1) {
			if($post->post_parent == $sub) {
				return true;
			}else{
				return false;
			}
		}elseif($equal == 2) {
			if($post->post_parent != $sub) {
				return true;
			}else{
				return false;
			}
		}
	}
	
	private function check_page_template($equal, $sub) {
		if($equal == 1) {
			if(is_page_template($sub)) {
				return true;
			}else{
				return false;
			}
		}elseif($equal == 2) {
			if(!is_page_template($sub)) {
				return true;
			}else{
				return false;
			}
		}
	}
	
	private function fix_code($code) {
		return stripslashes($code);
	}
}

if(is_admin()) {
	new wp_spotim_admin();
}else{
	function load_spot_to_footer() {
		new wp_spotim();
	}
    add_action('wp_footer', 'load_spot_to_footer');
}
?>