<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Customers_List extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
    	/** Process bulk action */
		$this->process_bulk_action();

        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {

        $columns = array(
        	'cb'      => '<input type="checkbox" />',
            'name'          => 'Name',
            'address'       => 'Address',
            'city' => 'City',
            
        );
        return $columns;
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('name' => array('name', false));
    }

    /**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {

    	/*//Detect when a bulk action is being triggered...
		if ( 'delete' === $_GET['action'] ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				
				self::delete_customer( absint( $_GET['customer'] ) );
		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect('admin.php?page=customer_list_page');
				exit;
			}

		}*/
    	
    	$data = array();

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}customers";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$data = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $data;
    }

    function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}customers",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}customers";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	function no_items() {
		_e( 'No customers avaliable.', 'sp' );
	}

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'name':
            case 'address':
            case 'city':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }

    /**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	public  function column_name( $item ) {

		$edit_nonce = wp_create_nonce( 'sp_edit_customer' );
		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'edit' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Edit</a>', esc_attr('customer_update_page'), 'edit', absint( $item['id'] ), $edit_nonce ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'title';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }

    public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {

				self::delete_customer( absint( $_GET['customer'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect('admin.php?page=customer_list_page');
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect('admin.php?page=customer_list_page') ;
			exit;
		}
	}

}

class Customer_class {

	// class constructor
	function __construct() {
		add_action( 'admin_init', array($this, 'create_tabel' ) );
		add_action( 'admin_menu', array($this, 'customer_menu' ) );
	}
	
	function customer_menu() {

		add_menu_page('Customers','Customers','manage_options','customer_list_page',array($this, 'customer_settings_page' ),'dashicons-groups
');
		add_submenu_page('customer_list_page','Add Customers','Add Customers','manage_options','customer_update_page',array($this, 'update_customer_settings_page' ));
	}

	function create_tabel()
	{
		global $wpdb;

		$table_name = "{$wpdb->prefix}customers";

		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    		//echo $table_name;
    		$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  name tinytext NOT NULL,
			  address text NOT NULL,
			  city varchar(55) DEFAULT '' NOT NULL,
			  PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}


	/**
	 * Plugin settings page
	 */
	function customer_settings_page() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Customers',
			'default' => 5,
			'option'  => 'customers_per_page'
		];

		add_screen_option( $option, $args );



		 $Customers_List = new Customers_List();
        $Customers_List->prepare_items();

		?>
		<div class="wrap">
			<h2>Customers</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								// $Customers_List->search_box('Search', 'search');
								$Customers_List->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}	

	function update_customer_settings_page()
	{
		$message = "";
		if(!empty($_POST['wp-insert-customer']))
		{
			
			if(!empty($_POST['customer_name']))
			{
				global $wpdb;
				$customer_name = $_POST['customer_name'];
				$customer_address = $_POST['customer_address'];
				$customer_city = $_POST['customer_city'];
				$table_name = $wpdb->prefix . "customers";
				$wpdb->insert( $table_name, array(
				    'name' => $customer_name,
				    'address' => $customer_address,
				    'city' => $customer_city
				) );

				$message = "Customer Inserted";
			}
			else 
			{
				$message = "Please Insert Customer Name";
			}

		}

		if(!empty($_POST['wp-update-customer']))
		{
			
			if(!empty($_POST['customer_name']))
			{
				global $wpdb;
				$customer_name = $_POST['customer_name'];
				$customer_address = $_POST['customer_address'];
				$customer_city = $_POST['customer_city'];
				$table_name = $wpdb->prefix . "customers";
				$wpdb->update( 
					$table_name, 
					array( 
						'name' => $customer_name,
					    'address' => $customer_address,
					    'city' => $customer_city
					), 
					array( 'ID' => $_GET['customer'] ), 
					array( 
						'%s',	// value1
						'%s',	// value2
						'%s'
					), 
					array( '%d' ) 
				);

				$message = "Customer Updated";
			}
			else 
			{
				$message = "Please Insert Customer Name";
			}

		}

		$action_name = "wp-insert-customer";
		$action_text = "Insert";
		if(isset($_GET['customer']) && !empty($_GET['customer']))
		{
			$action_name = "wp-update-customer";
			$action_text = "Update";


			$customer_id = $_GET['customer'];
			global $wpdb;
			$sql = "SELECT * FROM {$wpdb->prefix}customers WHERE id=".$customer_id;
			$data = $wpdb->get_results( $sql, 'ARRAY_A' );
			
			$c_name = $data[0]['name'];
			$c_address = $data[0]['address'];
			$c_city = $data[0]['city'];
			
		}
		?>
		<div class="wrap">
			<h2><?php echo $action_text; ?> Customers</h2>
			<?php echo $message; ?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<table>
								  <tr valign="top">
								  <th scope="row"><label for="customer_name">Name</label></th>
								  <td><input type="text" id="customer_name" name="customer_name" value="<?php echo $c_name; ?>"/></td>
								  </tr>
								  <tr valign="top">
								  <th scope="row"><label for="customer_address">Address</label></th>
								  <td><input type="text" id="customer_address" name="customer_address" value="<?php echo $c_address; ?>"/></td>
								  </tr>
								   <tr valign="top">
								  <th scope="row"><label for="customer_city">City</label></th>
								  <td><input type="text" id="customer_city" name="customer_city" value="<?php echo $c_city; ?>"/></td>
								  </tr>
								  </table>
			 					 <?php  submit_button( $action_text, 'primary', $action_name); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}
}
new Customer_class;