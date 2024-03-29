<?php

/*
Plugin Name: Pixlogix Infotech Custom Plugin
Plugin URI: https://wordpress.com/
Description: Post Table
Version: 1.0
Author: Hardik Devani
Author URI:  https://wordpress.com/
*/

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Posts_List extends WP_List_Table
{

	/** Class constructor */
	public function __construct()
	{

		parent::__construct([
			'singular' => __('Post', 'sp'), //singular name of the listed records
			'plural'   => __('Posts', 'sp'), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		]);
	}


	/**
	 * Retrieve posts data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_my_post($per_page = 5, $page_number = 1)
	{

		global $wpdb;

		$sql = "SELECT ID, post_title, post_date, post_status FROM {$wpdb->prefix}posts where post_type = 'post'";

		if (!empty($_REQUEST['s'])) {
			$search = esc_sql($_REQUEST['s']);
			$sql .= " AND post_title LIKE '%{$search}%'";
			$sql .= " OR post_content LIKE '%{$search}%'";
		}

		if (!empty($_REQUEST['orderby'])) {
			$sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
			$sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ($page_number - 1) * $per_page;
		//echo $sql;

		$result = $wpdb->get_results($sql, 'ARRAY_A');
		//print_r($result);
		return $result;
	}


	/**
	 * Delete a post record.
	 *
	 * @param int $id post ID
	 */
	public static function delete_post($id)
	{
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}posts",
			['ID' => $id],
			['%d']
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count()
	{
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}posts where post_type = 'post'";

		if (!empty($_REQUEST['s'])) {
			$search = esc_sql($_REQUEST['s']);
			$sql .= " AND post_title LIKE '%{$search}%'";
			$sql .= " OR post_content LIKE '%{$search}%'";
		}

		if (!empty($_REQUEST['orderby'])) {
			$sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
			$sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		}



		return $wpdb->get_var($sql);
	}


	/** Text displayed when no post data is available */
	public function no_items()
	{
		_e('No Posts avaliable.', 'sp');
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'post_status':
			case 'post_date':
			case 'post_title':
				return $item[$column_name];
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />',
			$item['ID']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_post_title($item)
	{

		$delete_nonce = wp_create_nonce('sp_delete_post');

		$title = '<strong>' . $item['post_title'] . '</strong>';

		$actions = [
			'delete' => sprintf('<a href="?page=%s&action=%s&postid=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce)
		];

		return $title . $this->row_actions($actions);
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns()
	{
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'post_title'    => __('Name', 'sp'),
			'post_date' => __('Date', 'sp'),
			'post_status'    => __('Status', 'sp')
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns()
	{
		$sortable_columns = array(
			'post_title' => array('post_title', true),
			'post_date' => array('post_date', false),
			'post_status' => array('post_status', false),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions()
	{
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items()
	{

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page('post_per_page', 5);
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args([
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		]);

		$this->items = self::get_my_post($per_page, $current_page);
	}

	public function process_bulk_action()
	{

		//Detect when a bulk action is being triggered...
		if ('delete' === $this->current_action()) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr($_REQUEST['_wpnonce']);

			if (!wp_verify_nonce($nonce, 'sp_delete_post')) {
				die('Go get a life script kiddies');
			} else {
				self::delete_post(absint($_GET['postid']));

				// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
				// add_query_arg() return the current url
				wp_redirect(esc_url_raw(add_query_arg()));
				exit;
			}
		}

		// If the delete bulk action is triggered
		if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
			|| (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
		) {

			$delete_ids = esc_sql($_POST['bulk-delete']);

			// loop over the array of record IDs and delete them
			foreach ($delete_ids as $id) {
				self::delete_post($id);
			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
			// add_query_arg() return the current url
			wp_redirect(esc_url_raw(add_query_arg()));
			exit;
		}
	}
}


class SP_Plugin
{

	// class instance
	static $instance;

	// post WP_List_Table object
	public $allpost_object;

	// class constructor
	public function __construct()
	{
		add_filter('set_screen_option', [__CLASS__, 'set_screen'], 10, 3);
		add_action('admin_menu', [$this, 'plugin_menu']);
		add_action('admin_footer', [$this, 'plugin_footer']);
	}


	public static function set_screen($status, $option, $value)
	{
		return $value;
	}

	public function plugin_menu()
	{

		$hook = add_menu_page(
			'Sitepoint WP_List_Table Example',
			'Posts List Table',
			'manage_options',
			'wp_list_table_class',
			[$this, 'plugin_settings_page']
		);

		add_action("load-$hook", [$this, 'screen_option']);
	}

	public function plugin_footer()
	{
		?>
			<script>
				if(jQuery(".post-page-wrap").length) {
					if(jQuery("#search-search-input").length) {
						var search_string = jQuery("#search-search-input").val();
						
						if(search_string != "") {
							
							jQuery(".table-view-list th a").each(function() {
								this.href = this.href + "&s=" + search_string;
							});

							jQuery(".post-page-wrap .pagination-links a").each(function() {
								this.href = this.href + "&s=" + search_string;
							});
						}
					}
				}
			</script>
		<?php
		
	}

	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page()
	{
		?>
		<div class="wrap">
			<h2>Posts List table</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content" class="post-page-wrap">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->allpost_object->prepare_items();
								$this->allpost_object->search_box('Search', 'search');
								$this->allpost_object->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		
<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option()
	{

		$option = 'per_page';
		$args   = [
			'label'   => 'Posts',
			'default' => 5,
			'option'  => 'post_per_page'
		];

		add_screen_option($option, $args);

		$this->allpost_object = new Posts_List();
	}


	/** Singleton instance */
	public static function get_instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}


add_action('plugins_loaded', function () {
	SP_Plugin::get_instance();
});
