<?php 
class wp_spotim_admin {

	private $current_user;
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'spotim_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_spotim_page' ) );
	}
	
	public function spotim_admin_init() {
		wp_register_script( 'spotim-admin-js', WP_SPOTIM_URL.'/js/script.js' );
		wp_register_style( 'spotim-admin-css', WP_SPOTIM_URL.'/css/style.css' );
		add_action('wp_ajax_spotim', array($this, 'spotim_ajax'));
		global $current_user;
		get_currentuserinfo();
		$this->current_user = $current_user;
	}
	
	public function add_spotim_page() {
	
		$page_hook_suffix = add_submenu_page( 
			'options-general.php',
			'WP Spot.IM',
			'WP Spot.IM',
			'manage_options',
			'spotim',
			array($this, 'spotim_admin')
		);

		add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'spotim_admin_js'));
		add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'spotim_admin_css'));
	}
	
	public function spotim_admin_js() {
	    wp_enqueue_script( 'spotim-admin-js' );
	}
	
	public function spotim_admin_css() {
	    wp_enqueue_style( 'spotim-admin-css' );
	}
	
	public function spotim_admin() {
		$load_nonce = wp_create_nonce( 'spotim_load_'.WP_SPOTIM_LOAD );
		$new_nonce = wp_create_nonce( 'spotim_new_'.WP_SPOTIM_NEW );
		?>
		<input type="hidden" name="spotim_nonce" value="<?php echo $load_nonce; ?>">
		<div class="wrap">
			<h2>WP Spot.IM <a href="#" class="add-new-h2 spotim-add-new" data-nonce="<?php echo $new_nonce; ?>"><?php _e('Add New'); ?></a></h2>
			<h3>Add / Edit Spot.IM</h3>
			<table class="wp-list-table widefat posts spotim_table">
				<thead>
					<tr>
						<th>Spot.IM</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
				<tfoot>
					<tr>
						<th>Spot.IM</th>
					</tr>
				</tfoot>
			</table>
			<h2><a href="#" class="add-new-h2 spotim-add-new" data-nonce="<?php echo $new_nonce; ?>"><?php _e('Add New'); ?></a></h2>
		</div>
		<?php
	}
	
	public function spotim_ajax() {
		new wp_spotim_ajax();
		die();
	}
}
?>