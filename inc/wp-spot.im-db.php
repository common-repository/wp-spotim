<?php 
class wp_spotim_db {
	private $wpdb;
	
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}
	
	public function insert_spot() {
		$this->wpdb->insert(
			$this->wpdb->posts,
			array(
				'post_type' => 'spotim',
				'post_status' => 'publish',
				'post_date' => date("Y-m-d H:i:s"),
				'post_modified' => date("Y-m-d H:i:s"),
				'ping_status' => 'closed',
				'comment_status' => 'closed',
				'post_title' => 'New Spot.IM'
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			)
		);
		echo $this->wpdb->insert_id;
	}
	
	public function edit_spot($spot_id, $spot_title, $spot_content, $rules) {
		$rules = serialize($rules);
		$update = $this->wpdb->update( 
			$this->wpdb->posts, 
			array( 
				'post_title' => $spot_title,
				'post_content' => $spot_content,
				'post_excerpt' => $rules,
				'post_modified' => date("Y-m-d H:i:s")
			), 
			array( 'ID' => $spot_id ), 
			array( 
				'%s',
				'%s',
				'%s',
				'%s'
			), 
			array( '%d' ) 
		);
		echo $update;
	}
	
	public function delete_spot($spot_id) {
		$this->wpdb->delete( $this->wpdb->posts, array( 'ID' => $spot_id ), array('%d') );
		echo 'spot '.$spot_id.' deleted';
	}
	
	public function get_spot($spot_id) {
	
	}
	
	public function get_all_spots() {
		$spots = $this->wpdb->get_results(
			$this->wpdb->prepare("SELECT ID, post_title, post_content, post_excerpt FROM {$this->wpdb->posts} WHERE post_type='%s'", 'spotim')
		);
		return $spots;
	}
}
?>