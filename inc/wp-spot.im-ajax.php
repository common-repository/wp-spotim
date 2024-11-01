<?php 
class wp_spotim_ajax {

	private $action;
	private $post_req;
	private $spots;
	private $rules;
	
	public function __construct() {
		$rules = new wp_spotim_rules();
		$this->rules = $rules;
		$this->get_action();
		$this->make_action();
	}
	
	
	private function get_action() {
		if(isset($_POST['action'])) {
			if($_POST['action'] == 'spotim') {
				$this->action = $_POST['spotim_action'];
				$this->post_req = $_POST;
			}
		}
	}
	
	private function make_action() {
		$db = new wp_spotim_db;
		switch($this->action) {
			case "new":
				if(check_ajax_referer('spotim_new_'.WP_SPOTIM_NEW, 'spotim_new_nonce')) {
					$db->insert_spot();
				}
				break;
			case "update":
				if(check_ajax_referer('spotim_update_'.$this->post_req['spotim_id'], 'spotim_update_nonce')) {
					$db->edit_spot($this->post_req['spotim_id'] ,$this->post_req['spotim_title'], $this->post_req['spotim_content'], $this->post_req['spotim_rules']);
				}
				break;
			case "delete":
				if(check_ajax_referer('spotim_delete_'.$this->post_req['spotim_id'], 'spotim_delete_nonce')) {
					$db->delete_spot($this->post_req['spotim_id']);
				}
				break;
			case "get_spots":
				if(check_ajax_referer('spotim_load_'.WP_SPOTIM_LOAD, 'get_spots_nonce')) {
					$this->spots = $db->get_all_spots();
					$this->build_spots();
				}
				break;
			case "rules":
				if(check_ajax_referer('spotim_rules_'.$this->post_req['spotim_id'], 'spotim_rules_nonce')) {
					$this->ajax_sub_rules();
				}
				break;
		}
	}
	
	private function build_spots() {
		$spots = $this->spots;
		$total = count($spots);
		$x = 1;
		$y = 1;
		foreach($spots as $s) {
			$y = ($y > 2) ? 1 : $y;
			$delete_nonce = wp_create_nonce('spotim_delete_'.$s->ID);
			$update_nonce = wp_create_nonce('spotim_update_'.$s->ID);
			?>
			<tr class="spot-id-<?php echo $s->ID; ?>">
				<td class="spotim_spot">
					<div class="spotim-spot-show">
						<a class="row-title" href="#"><?php echo $s->post_title; ?></a>
						<a class="spot-btn delete-spot" data-value="<?php echo $s->ID; ?>" data-nonce="<?php echo $delete_nonce; ?>" data-action="delete" href="#"><?php _e('Delete'); ?></a>
						<a class="spot-btn edit-spot" data-value="<?php echo $s->ID; ?>" href="#"><?php _e('Edit'); ?></a>
						<a class="spot-btn preview-spot" data-action="preview" data-value="<?php echo $s->ID; ?>" href="#"><?php _e('Preview'); ?></a>
					</div>
					<div class="spotim-spot-hidden">
						<div class="spotim-spot-box spotim-spot-box1">
							<div class="inp-box">
								<label><?php _e('Title'); ?></label>
								<input type="text" name="spotim_title[<?php echo $s->ID; ?>]" class="spotim_title" value="<?php echo $s->post_title; ?>" />
							</div>
							<div class="inp-box">
								<label><?php _e('Code'); ?></label>
								<textarea name="spotim_code[<?php echo $s->ID; ?>]" class="spotim_textarea"><?php echo $this->fix_code($s->post_content); ?></textarea>
							</div>
						</div>
						<div class="spotim-spot-box">
							<div class="inp-box">
								<label><?php _e('Rules'); ?></label>
								<?php $this->rules->build_rules($s); ?>
							</div>
						</div>
						<div class="spotim-spot-box">
							<div class="inp-box spotim-buttons">
								<label><?php _e('Publish'); ?></label>
								<button type="button" class="button button-secondary" data-action="preview" data-value="<?php echo $s->ID; ?>"><?php _e('Preview'); ?></button>
								<button type="button" class="button button-primary" data-action="update" data-value="<?php echo $s->ID; ?>" data-nonce="<?php echo $update_nonce; ?>"><?php _e('Update'); ?></button>
								<button type="button" class="button button-primary" data-action="delete" data-value="<?php echo $s->ID; ?>" data-nonce="<?php echo $delete_nonce; ?>"><?php _e('Delete'); ?></button>
								<div class="ajax-status"></div>
							</div>
						</div>
					</div>
				</td>			
			</tr>
			<?php
			$x++;
			$y++;
		}
	}
	
	private function ajax_sub_rules() {
		$sub = $this->rules->get_sub_rules($this->post_req['spotim_rules_by']);
		if($sub) {
		?>
		<select name="sub_rules" class="spotim_sub_rules">
			<?php foreach($sub as $sk=>$sv) { ?>
			<option value="<?php echo $sk; ?>"><?php echo $sv; ?></option>
			<?php } ?>	
		</select>
		<?php
		}
	}
	
	private function fix_code($code) {
		return stripslashes($code);
	}
}
?>