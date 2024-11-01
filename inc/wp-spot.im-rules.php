<?php 
class wp_spotim_rules {

	public $rules;

	public function __construct() {
		$rules = array(
			'Basic' => array(
				'all_site' => 'All site',
				'post_type' => 'Post Type'
			),
			'Post' => array(
				'post' => 'Post',
				'category' => 'Post Category'
			),
			'Page' => array(
				'page' => 'Page',
				'page_parent' => 'Page Parent',
				'page_template' => 'Page Template',
			)
		);
		$this->rules = $rules;
	}
	
	public function get_rules() {
		return $this->rules;
	}
	
	public function spotim_rules_post_type() {
		$a = array(
			'post'=>'Post',
			'page'=>'Page'
		);
		
		return $a;
	}
	
	public function spotim_rules_post() {
		$posts = get_posts(array('posts_per_page' => -1));
		foreach($posts as $p) {
			$a[$p->ID] = $p->post_title;
		}
		
		return $a;
	}
	
	public function spotim_rules_categories() {
		$categories = get_categories();
		foreach($categories as $c) {
			$a[$c->cat_ID] = $c->name;
		}
			
		return $a;
	}
	
	public function spotim_rules_page() {
		$pages = get_pages();
		foreach($pages as $p) {
			$ancestors = get_ancestors( $p->ID, 'page' );
			$total = count($ancestors);
			$parent = '';
			for($i=1;$i<=$total;$i++) {
				$parent .= '-'; 
			}
			$a[$p->ID] = $parent.' '.$p->post_title;
		}
		
		return $a;
	}
	
	public function spotim_rules_page_template() {
		$templates = wp_get_theme()->get_page_templates();
		if($templates) {
			$a = ($templates);
			return $a;
		}
	}
	
	public function get_sub_rules($by) {
		switch ($by) {
			case "all_site":
				return array();
				break;
			case "post_type":
				return $this->spotim_rules_post_type();
				break;
			case "post":
				return $this->spotim_rules_post();
				break;
			case "category":
				return $this->spotim_rules_categories();
				break;
			case "page":
				return $this->spotim_rules_page();
				break;
			case "page_parent":
				return $this->spotim_rules_page();
				break;
			case "page_template":
				return $this->spotim_rules_page_template();
				break;
			
		}
	}
	
	public function build_rules($spot) {
		$s = $spot;
		$rules = unserialize($s->post_excerpt);
		$rules_nonce = wp_create_nonce('spotim_rules_'.$s->ID);
		if($rules) {
			foreach($rules as $r) {
				$sub = $this->get_sub_rules($r['spotim_rules']);
			?>
			<div class="spot-rules-box">
				<div class="spot-rules">
					<select name="spotim_rules[<?php echo $s->ID; ?>][]" class="spotim_rules" data-value="<?php echo $s->ID; ?>" data-nonce="<?php echo $rules_nonce; ?>">
						<?php foreach($this->rules as $rgk=>$rgv) { ?>
							<optgroup label="<?php echo $rgk; ?>">
							<?php foreach($rgv as $rk=>$rv) { ?>
							<option value="<?php echo $rk; ?>" <?php echo ($r['spotim_rules'] == $rk) ? 'selected="selected"' : ''; ?>><?php echo $rv; ?></option>
							<?php } ?>
							</optgroup>
						<?php } ?>
					</select>
				</div>
				<div class="spot-rules-equal <?php echo (!$sub) ? 'spot-rules-equal-hidden' : ''; ?>">
					<input type="hidden" name="spotim_rules_equal[<?php echo $s->ID; ?>][]" value="<?php echo $r['spotim_rules_equal']; ?>" />
					<button type="button" class="button button-secondary <?php echo ($r['spotim_rules_equal'] == 1) ? 'active' : ''; ?>" data-value="1">=</button>
					<button type="button" class="button button-secondary <?php echo ($r['spotim_rules_equal'] == 2) ? 'active' : ''; ?>" data-value="2">!=</button>
				</div>
				<div class="spot-sub-rules">
				<?php 
				
				if($sub) {
				?>
					<select name="sub_rules" class="spotim_sub_rules">
						<?php foreach($sub as $sk=>$sv) { ?>
						<option value="<?php echo $sk; ?>" <?php echo ($r['spotim_sub_rules'] == $sk) ? 'selected="selected"' : ''; ?>><?php echo $sv; ?></option>
						<?php } ?>	
					</select>
				<?php 
				}
				?>
				</div>
				<div class="clear"></div>
			</div>
			<?php
			}
		}else{
		?>
		<div class="spot-rules-box">
			<div class="spot-rules">
				<select name="spotim_rules[<?php echo $s->ID; ?>][]" class="spotim_rules" data-value="<?php echo $s->ID; ?>" data-nonce="<?php echo $rules_nonce; ?>">
						<?php foreach($this->rules as $rgk=>$rgv) { ?>
							<optgroup label="<?php echo $rgk; ?>">
							<?php foreach($rgv as $rk=>$rv) { ?>
							<option value="<?php echo $rk; ?>"><?php echo $rv; ?></option>
							<?php } ?>
							</optgroup>
						<?php } ?>
				</select>
			</div>
			<div class="spot-rules-equal spot-rules-equal-hidden">
				<input type="hidden" name="spotim_rules_equal[<?php echo $s->ID; ?>][]" value="1" />
				<button type="button" class="button button-secondary active" data-value="1">=</button>
				<button type="button" class="button button-secondary" data-value="2">!=</button>
			</div>
			<div class="spot-sub-rules"></div>
			<div class="clear"></div>
		</div>
		<?php
		}
	}
}
?>