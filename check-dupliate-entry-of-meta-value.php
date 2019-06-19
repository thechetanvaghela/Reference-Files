<?php
class Check_duplicate_entry {
  public function __construct(){
   add_action( 'save_post', array( $this, 'save_post' ) );
   add_action( 'admin_notices', array( $this, 'admin_notices_callback' ) );
  }

   public function save_post( $post_id ) {
  	global $wpdb;
  	$issue_id = $_POST['issue_magazine_id'];
  	$issue_number = $_POST['issue_number'];
  	//echo $query = "SELECT * FROM wp_postmeta WHERE 'issue_magazine_id' = ".$issue_id." AND 'issue_number'=". $_POST['issue_number'];
  	$query = "SELECT $wpdb->posts.ID  FROM $wpdb->posts  INNER JOIN wp_postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )  INNER JOIN $wpdb->postmeta AS mt1 ON ( wp_posts.ID = mt1.post_id ) WHERE 1=1  AND ( 
  ( $wpdb->postmeta.meta_key = 'issue_magazine_id' AND $wpdb->postmeta.meta_value = $issue_id ) 
  AND 
  ( mt1.meta_key = 'issue_number' AND mt1.meta_value = $issue_number )
)";
	$result = $wpdb->get_results($query);
	
	if($result && $post_id != $result[0]->ID){
		
   	// Add your query var if the coordinates are not retreive correctly.
   		add_filter( 'redirect_post_location', array( $this, 'add_notice_query_variable' ), 99 );
	}
	else
	{
		if ( !empty( $_POST[ 'issue_magazine_id' ] ) ) {
			update_post_meta( $post_id, 'issue_magazine_id', $_POST[ 'issue_magazine_id' ]);
		} else {
			delete_post_meta( $post_id, 'issue_magazine_id' );
		}

		if ( !empty( $_POST[ 'issue_number' ] ) ) {
			update_post_meta( $post_id, 'issue_number', $_POST[ 'issue_number' ]);
		} else {
			delete_post_meta( $post_id, 'issue_number' );
		}
	}
  }

  public function add_notice_query_variable( $location ) {
   remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_variable' ), 99 );
   return add_query_arg( array( 'issue_magazine_id' => $_POST['issue_magazine_id'],'issue_number' => $_POST['issue_number'] ), $location );
  }

  public function admin_notices_callback() {
   if ( ! isset( $_GET['issue_magazine_id'] ) && ! isset( $_GET['issue_number'] ) ) {
     return;
   }
   ?>
   <div class="error notice">
      <p><?php esc_html_e( 'Issue ID : '.$_GET['issue_magazine_id'].' and Issue Number : '. $_GET['issue_number'].' already updated in other post, Please try different!', '' ); ?></p>
   </div>
   <?php
  }
}
$Check_duplicate_entry = new Check_duplicate_entry();

add_action( 'add_meta_boxes','my_meta_box' );
     
function my_meta_box() {
	 add_meta_box(
		        'my-page-meta',
		        __( 'Page Data', '' ),
		        'my_page_meta_box_callback',
		        'page'
		    );
		}
function my_page_meta_box_callback( $post ) {

			$issue_magazine_id = get_post_meta( $post->ID, 'issue_magazine_id', true );	
			$issue_number = get_post_meta( $post->ID, 'issue_number', true );				
			?>
			<div class="form-row form-row-full page-title-wrapper" id="-page-title-wrapper">
	            <h4><?php esc_html_e( 'issue_magazine_id', '' ) ?></h4>
	            <div class="page-listing-title-container">
	                <div class="page-listing-title">						
				        <div class="title-data-container">
				            <input type="text" name="issue_magazine_id" value="<?php echo $issue_magazine_id ?>" size="100">
				        </div>								
	                </div>
	            </div>
	        </div>
	        <div class="form-row form-row-full page-image-wrapper" id="-page-image-wrapper">
	            <h4><?php esc_html_e( 'issue_number', '' ) ?></h4>
	             <div class="page-listing-title-container">
	                <div class="page-listing-title">						
				        <div class="title-data-container">
				            <input type="text" name="issue_number" value="<?php echo $issue_number ?>" size="100">
				        </div>								
	                </div>
	            </div>
	        </div>
			<?php
	}
	?>