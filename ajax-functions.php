<?php

/*  
*	function used for Login
*	@return messages
*	function name online_login_request
*/
add_action( 'wp_ajax_nopriv_online_login_request', 'online_login_request' );
add_action( 'wp_ajax_online_login_request', 'online_login_request' );

function online_login_request() 
{

	if(isset($_POST['username']) && isset($_POST['password']) ) 
	{
		$username = $_POST['username'];
      	$password = $_POST['password'];

      	$data = array();
      	$status = "";
      	$message = "";
      	$title = "";
      
      	if(empty($username))
      	{
        	$status  = "error";
        	$message = "Please Enter UserName.";
        	$title  = "Username";
        	
      	}
      	else if(empty($password))
      	{
      		$status     = "error";
            $message  = "Please Enter Password.";
            $title   = "Password";
	        
	    }
      	else
      	{ 
	      	if ( email_exists( $username ) || username_exists( $username ) ) 
	      	{
			    // Stuff to do when email address exists
			    $creds = array('user_login' => $username, 'user_password' =>  $password, 'remember' => true );
			    $user = wp_signon( $creds, false );
			    wp_set_current_user($user->ID); 
			    wp_set_auth_cookie($user->ID, true, false );    
			    do_action( 'wp_login', $username );

			    if ( is_wp_error($user) )
			    {
			        //echo $user->get_error_message();
			        if('incorrect_password' == $user->get_error_code())
			        {
			        	$message = "The password you entered for the username $username is incorrect. ";
			        }
			        else
			        {
			        	$message =  $user->get_error_message();
			       	}
		            $status   = "error";
		            $title    = "Login Failed";
			        
			    }
			    else
			    {
			    	$status     = "success";
		            $message  = "Login successfully.";
		            $title  = "Login";
			        
			    }
		  	}
		  	else
		  	{
		  		
		            $status  = "error";
		            $message = "Invalid Username or Email.";
		            $title   = "User";
			        
		  	}

		}

		
	}
	else
	{
		$message =  "Something Wrong.";
	    $status    = "error";
        $title   = "Something Wrong!";
	}

	$data = array(
		            "status"     => $status,
		            "message"  => $message,
		            "title"   => $title
			        );
        echo json_encode($data);
        die();
}

/*  
*	function used for user registration
*	@return messages
*	function name online_register_request
*/
add_action( 'wp_ajax_nopriv_online_register_request', 'online_register_request' );
add_action( 'wp_ajax_online_register_request', 'online_register_request' );

function online_register_request() 
{

	if(isset($_POST['reg_username']) && isset($_POST['reg_email']) && isset($_POST['reg_password']) ) 
	{
		$reg_username = $_POST['reg_username'];
      	$reg_email = $_POST['reg_email'];
      	$reg_password = $_POST['reg_password'];
      	$data = array();
      	$message = "";
      
      	if(empty($reg_username))
      	{
      		$status  = "error";
        	$message = "Please Enter Username.";
        	$title  = "Username";
      	}
      	else if(username_exists( $reg_username))
	    {
	    	$status     = "error";
            $message  = "Username Already exists, Try Different userame.";
            $title   = "Username";
	    }
      	else if(empty($reg_email))
      	{
	      	$status     = "error";
            $message  = "Please Enter Email.";
            $title   = "Email";
	    }
	    else if(!is_email($reg_email))
      	{
	      	$status     = "error";
            $message  = "Please Enter Valid Email.";
            $title   = "Valid Email";
	    }
	    else if(email_exists( $reg_email))
	    {
	    	$status     = "error";
            $message  = "Email Already exists, Try Different Email.";
            $title   = "Password";
	    }
      	else if(empty($reg_password))
      	{
      		$status     = "error";
            $message  = "Please Enter Password.";
            $title   = "Password";
	      	
	    }
	    else if(strlen($reg_password) < 8)
      	{
      		$status     = "error";
            $message  = "Please enter minimum 8 characters in Password.";
            $title   = "Password";
	      	
	    }
      	else
      	{ 
      		$WP_array = array (
		        'user_login'    =>  $reg_username,
		        'user_email'    =>  $reg_email,
		        'user_pass'     =>  $reg_password,
		    ) ;

		    $user_id = wp_insert_user( $WP_array ) ;

		    wp_update_user( array ('ID' => $user_id, 'role' => 'subscriber') ) ;

		    if( is_wp_error( $user_id  ) ) {
			    $message =  $user_id->get_error_message();
			    $status     = "error";
	            $title   = "Registration Failed";
			}
			else
			{
				$message =  "Registration successfully.";
			    $status    = "success";
	            $title   = "Registration";
	            $sent_email = send_welcome_email_to_new_user($user_id);

	            if($sent_email)
	            {
	            	$message .= " Email has been successfully sent to user whose email is " . $reg_email;
	            }
	            else
	            {
	            	$message .= " Email failed to sent to user whose email is " . $reg_email;
	            }
			}

		}
	
	}
	else
	{
		$message =  "Something Wrong.";
	    $status    = "error";
        $title   = "Something Wrong!";
	}

	$data = array(
		            "status"     => $status,
		            "message"  => $message,
		            "title"   => $title
			        );
        echo json_encode($data);
        die();
}


/*  
*	function used for Lost password reset
*	@return messages
*	function name online_lost_password_request
*/
add_action( 'wp_ajax_nopriv_online_lost_password_request', 'online_lost_password_request' );
add_action( 'wp_ajax_online_lost_password_request', 'online_lost_password_request' );

function online_lost_password_request() 
{

	if(isset($_POST['user_login'])) 
	{
		$user_login = $_POST['user_login'];
      	
      	$data = array();
      	$status = "";
      	$message = "";
      	$title = "";
      
      	if(empty($user_login))
      	{
        	$status  = "error";
        	$message = "Please Enter Username or Email.";
        	$title  = "Username";
        	
      	}
      	else
      	{ 
	      	if ( email_exists( $user_login ) || username_exists( $user_login ) ) 
	      	{
			    // Stuff to do when email address exists

			    if(is_email($user_login))
			    {
			    	$user = get_user_by( 'email',$user_login );
			    }
			    else
			    {
			    	$user = get_user_by('login', $user_login);
			    }
	      		
				$user_id = $user->ID;
   				$user_email = $user->user_email;
			    $sent_email = send_password_reset_mail($user_id);
			    if($sent_email)
	            {
	            	$message = " Email has been successfully sent to user whose email is " . $user_email;
	            }
	            else
	            {
	            	$message = " Email failed to sent to user whose email is " . $user_email;
	            }
	            $status  = "success";
        		$title  = "Lost Password";
		  	}
		  	else
		  	{
		  		
		            $status  = "error";
		            $message = "Invalid username or email.";
		            $title   = "Invalid";
			        
		  	}

		}
		
	}
	else
	{
		$message =  "Something Wrong.";
	    $status    = "error";
        $title   = "Something Wrong!";
	}

	$data = array(
		            "status"     => $status,
		            "message"  => $message,
		            "title"   => $title
			        );
        echo json_encode($data);
        die();
}

/*  
*	function used for Reset password 
*	@return messages
*	function name online_reset_password_request
*/
add_action( 'wp_ajax_nopriv_online_reset_password_request', 'online_reset_password_request' );
add_action( 'wp_ajax_online_reset_password_request', 'online_reset_password_request' );

function online_reset_password_request() 
{

	if(isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_POST['user_name']) && isset($_POST['user_key']) ) 
	{
		$user_name = $_POST['user_name'];
		$user_key = $_POST['user_key'];
		$new_password = $_POST['new_password'];
      	$confirm_password = $_POST['confirm_password'];

      	$data = array();
      	$message = "";
      
		if(!username_exists( $user_name))
	    {
	    	$status     = "error";
            $message  = "Username Not exists.";
            $title   = "Username";
	    }
      	else if(empty($new_password))
      	{
      		$status  = "error";
        	$message = "Please Enter New Password.";
        	$title  = "Username";
      	}
      	else if(empty($confirm_password))
      	{
      		$status  = "error";
        	$message = "Please Enter Confirm Password.";
        	$title  = "Username";
      	}
      	else if(strlen($new_password) < 8)
      	{
      		$status     = "error";
            $message  = "Please Enter minimum 8 characters in Password.";
            $title   = "Password";
	      	
	    }
      	else if($new_password != $confirm_password)
	    {
	    	$status     = "error";
            $message  = "Password did not match.";
            $title   = "Password";
	    }
      	else
      	{ 
      		

      		$user_logded = get_user_by('login', $user_name);
      		$user_id = $user_logded->ID;
		    
      		 // Verify key / login combo
	        $user = check_password_reset_key( $user_key, $user_name);
	        if ( ! $user || is_wp_error( $user ) ) {
	            if ( $user && $user->get_error_code() === 'expired_key' ) {
	            	$message =  "Key Expired.";
				    $status    = "error";
		            $title   = "Key";
	                //wp_redirect( home_url( 'member-login?login=expiredkey' ) );
	            } else {
	            	$message =  "Invalid Key.";
				    $status    = "error";
		            $title   = "Key";
	                //wp_redirect( home_url( 'member-login?login=invalidkey' ) );
	            }
	            //exit;
	        }
	        else
	        {
	        
			    wp_update_user(array('ID' => $user_id, 'user_pass' => $new_password));

			    if( is_wp_error( $user_id  ) ) {
				    $message =  $user_id->get_error_message();
				    $status     = "error";
		            $title   = "Failed";
				}
				else
				{
					$message =  "Password Reset successfully.".$user_login;;
				    $status    = "success";
		            $title   = "Reset Password";
		            
				}
			}

		}

		
	}
	else
	{
		$message =  "Something Wrong.";
	    $status    = "error";
        $title   = "Something Wrong!";
	}

	$data = array(
		            "status"     => $status,
		            "message"  => $message,
		            "title"   => $title
			        );
        echo json_encode($data);
        die();
}


/*  
*	function used for Send welcome email
*	@return messages
*	function name send_welcome_email_to_new_user
*/
function send_welcome_email_to_new_user($user_id) {
    $user = get_userdata($user_id);
    $user_email = $user->user_email;
    // for simplicity, lets assume that user has typed their first and last name when they sign up
    //$user_full_name = $user->user_firstname . $user->user_lastname;
    $user_full_name = $user->user_login;

    // Now we are ready to build our welcome email
    $to = $user_email;
    $subject = "Hi " . $user_full_name . ", welcome to our site!";
    $body = '
              <h1>Dear ' . $user_full_name . ',</h1></br>
              <p>Thank you for joining our site. Your account is now active.</p>
              <p>Please go ahead and navigate around your account.</p>
              <p>Let me know if you have further questions, I am here to help.</p>
              <p>Enjoy the rest of your day!</p>
              <p>Kind Regards,</p>
              <p>Online Shop/p>
    ';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    if (wp_mail($to, $subject, $body, $headers)) {
     	 //error_log("email has been successfully sent to user whose email is " . $user_email);
      return true;
    }else{
      	//error_log("email failed to sent to user whose email is " . $user_email);
      	return false;
    }
}

/*  
*	function used for Send password reset email
*	@return messages
*	function name send_password_reset_mail
*/
function send_password_reset_mail($user_id){

    $user = get_user_by('id', $user_id);
    $firstname = $user->first_name;
    $email = $user->user_email;
    $reset_pass_key = get_password_reset_key( $user );
    $user_login = $user->user_login;
    $rp_link = '<a href="' . home_url()."/login-register/?key=$reset_pass_key&login=" . rawurlencode($user_login) . '">' . home_url()."/login-register/?key=$reset_pass_key&login=" . rawurlencode($user_login) . '</a>';

    if ($firstname == "") $firstname = "User";
    $message = "Hi ".$firstname.",<br>";
    $message .= "Click here to Reset the password for your account: <br>";
    $message .= $rp_link.'<br>';

    //deze functie moet je zelf nog toevoegen. 
   $subject = __("Your account on ".get_bloginfo( 'name'));
   $headers = array();

   add_filter( 'wp_mail_content_type', function( $content_type ) {return 'text/html';});
   $headers[] = 'From: Your company name <info@your-domain.com>'."\r\n";
   if(wp_mail( $email, $subject, $message, $headers))
   {
   		return true;
   }
   else
   {
   		return false;
   }

   // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
   remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}