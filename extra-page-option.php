<?php
/***
**  Extra Option Page
***/

class extra_options_page {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'extra_admin_menu' ) );
	}

	function extra_admin_menu() {
		add_menu_page( 'Welcome Page Message', 'Welcome Page Message', 'edit_posts', 'awesome_page', array($this,'my_awesome_page_display'), 'dashicons-star-empty
', 24);
		add_options_page('Extra Option Page','Extra Option Page','manage_options','extra_options_page',array($this,'extra_settings_page'));
		add_submenu_page( 'index.php', 'Welcome', 'Welcome', 'manage_options', 'welcome-page',  array( $this, 'dashboard_submenu_welcome_page_callback' ));
		add_submenu_page( 'tools.php', 'Custom Submenu Page', 'Custom Submenu Page', 'manage_options', 'custom-submenu-page',  array( $this, 'custom_submenu_page_callback' ));
	}

	function dashboard_submenu_welcome_page_callback()
	{
		echo "<style>
		body {
			width: 100wh;
			height: 90vh;
			color: #fff;
			background: linear-gradient(-45deg, #EE7752, #E73C7E, #23A6D5, #23D5AB);
			background-size: 400% 400%;
			-webkit-animation: Gradient 15s ease infinite;
			-moz-animation: Gradient 15s ease infinite;
			animation: Gradient 15s ease infinite;
		}

		@-webkit-keyframes Gradient {
			0% {
				background-position: 0% 50%
			}
			50% {
				background-position: 100% 50%
			}
			100% {
				background-position: 0% 50%
			}
		}

		@-moz-keyframes Gradient {
			0% {
				background-position: 0% 50%
			}
			50% {
				background-position: 100% 50%
			}
			100% {
				background-position: 0% 50%
			}
		}

		@keyframes Gradient {
			0% {
				background-position: 0% 50%
			}
			50% {
				background-position: 100% 50%
			}
			100% {
				background-position: 0% 50%
			}
		}

		.typewriter {
			    font-family: Courier, monospace;
				display: inline-block;
			}

			.typewriter-text {
			    display: inline-block;
			  	overflow: hidden;
			  	/*letter-spacing: 2px;*/
			 	animation: typing 5s steps(30, end), blink .75s step-end infinite;
			    white-space: nowrap;
			    font-size: 80px;
			    font-weight: 700;
			    border-right: 4px solid orange;
			    box-sizing: border-box;
			}

			@keyframes typing {
			    from { 
			        width: 0% 
			    }
			    to { 
			        width: 100% 
			    }
			}

			@keyframes blink {
			    from, to { 
			        border-color: transparent 
			    }
			    50% { 
			        border-color: orange; 
			    }
			}
</style>";
		?>
		<div class="typewriter">
				<div class="typewriter-text">
				  <h1><?php echo !empty(get_option('awesome_text')) ? get_option('awesome_text') : "Welcome."  ;?></h1>
				</div>
			</div> 
		<?php
	}
	function  custom_submenu_page_callback() {
		 if ( isset( $_POST['extra-submenu-text'] ) ) {
				update_option( 'extra-submenu-text', $_POST['extra-submenu-text'] );
			} else {
				delete_option( 'extra-submenu-text' );
			}
		$extra_submenu_text = get_option( 'extra-submenu-text' );
		?>
		
		<div class="wrap">
				<h1>Submenu Page</h1>
				<p class="description">
					Select your options from the following set of choices...
				</p>

				<form action="" method="post">
				  
					<!-- The set of option elements, such as checkboxes, would go here -->
					<label>Add Text Here</label>
						<input type="text" name="extra-submenu-text" value="<?php echo $extra_submenu_text; ?>" />
					<?php submit_button( 'Save' ); ?>
					<?php //wp_nonce_field( 'acme-submenu-page-save', 'acme-submenu-page-save-nonce' ); ?>

				</form>

			</div><!-- .wrap -->
		<?php
	}

	function  extra_settings_page() {
		if (isset($_POST['extra_options_group'])) {
		        $value = $_POST['extra_options_group'];
		        update_option('extra_options_group', $value);
		    }
		?>
			<div>
			  <?php screen_icon(); ?>
			  <h2>Extra Page Title</h2>
			  <form method="post" action="">
			  <?php settings_fields( 'extra_options_group' ); ?>
			 
			  <p>Some text here.</p>
			  <table>
			  <tr valign="top">
			  <th scope="row"><label for="extra_options_group">Label</label></th>
			  <td><input type="text" id="extra_options_group" name="extra_options_group" value="<?php echo get_option('extra_options_group'); ?>" /></td>
			  </tr>
			  </table>
			  <?php  submit_button(); ?>
			  </form>
			  </div>
		<?php
	}

	function my_awesome_page_display() {

		if (isset($_POST['awesome_text'])) {
		        $value = $_POST['awesome_text'];
		        update_option('awesome_text', $value);
		    }

    $value = get_option('awesome_text', 'hey-ho');

    	?>
    	<h1>Welcome Page Message Settings</h1>

			<form method="POST">
			    <label for="awesome_text">Awesome Text</label>
			    <input type="text" name="awesome_text" id="awesome_text" value="<?php echo $value; ?>">
			    <input type="submit" value="Save" class="button button-primary button-large">
			</form>
    	<?php
	}

}

new extra_options_page;
?>