<?php
class CVcustomFunctions {
    function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'cv_add_meta_boxes_callback' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'cv_enqueue_datepicker'));
        add_action( 'save_post', array( $this,'cv_save_meta_box') );
    }

    function cv_add_meta_boxes_callback() {
    	add_meta_box( 'my-resume-name', __( 'Name', 'my-resume' ), array( $this,'cv_my_resume_name_boxes_callback'), 'page' );
    	add_meta_box( 'my-resume-profile-pic', __( 'Profile Picture', 'my-resume' ), array( $this,'cv_my_resume_profile_pic_boxes_callback'), 'page' );
    	add_meta_box( 'my-resume-about', __( 'About', 'my-resume' ), array( $this,'cv_my_resume_about_boxes_callback'), 'page' );
    	add_meta_box( 'my-resume-objective', __( 'Objective', 'my-resume' ), array( $this,'cv_my_resume_objective_boxes_callback'), 'page' );
    	add_meta_box( 'my-resume-contact-detail', __( 'Contact Detail', 'my-resume' ), array( $this,'cv_my_resume_contact_detail_boxes_callback'), 'page' );
    	add_meta_box( 'my-resume-work', __( 'Work Experience', 'my-resume' ), array( $this,'cv_my_resume_work_boxes_callback'), 'page' );
    	add_meta_box( 'my-resume-education', __( 'Education', 'my-resume' ), array( $this,'cv_my_resume_education_boxes_callback'), 'page' );
    }

    function cv_enqueue_datepicker() {
	    // Load the datepicker script (pre-registered in WordPress).
	    wp_enqueue_script( 'jquery-ui-datepicker' );
	    // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
	    wp_register_style( 'cv-jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
	    wp_enqueue_style( 'cv-jquery-ui' );  

	    if ( ! did_action( 'wp_enqueue_media' ) ) {
	        wp_enqueue_media();
	    }
	}

    public function cv_my_resume_name_boxes_callback($post)
    {
    	$post_id = $post->ID;
    	$resume_name = get_post_meta($post_id,'my-resume-name',true);
    	$resume_dob = get_post_meta($post_id,'my-resume-dob',true);
    	?>
    	<script>
	    jQuery(document).ready(function($) {
		    jQuery("#my_resume_dob").datepicker();
		});
		</script>
    	
    	<table class="personal-detail-table">
    		<tr>
        		<td><label for="my_resume_name">Name</label></td>
        		<td><input id="my_resume_name" type="text" name="my_resume_name" value="<?php echo $resume_name; ?>"></td>
        	</tr>
        	<tr>
        		<td><label for="my_resume_dob">Date Of Birth</label></td>
        		<td><input id="my_resume_dob" type="text" name="my_resume_dob" value="<?php echo $resume_dob; ?>"></td>
        	</tr>
    	</table>

    	<?php
    }

    public function cv_my_resume_profile_pic_boxes_callback($post)
    {
    	$post_id = $post->ID;
    	$image = get_post_meta($post->ID, 'cv_profile_image', true);
	    ?>
	    <script type="text/javascript">
	    	$ = jQuery;
	    	jQuery(function($){
			    $('body').on('click', '.cv_upload_image_button', function(e){
			        e.preventDefault();
			  
			        var button = $(this),
			        aw_uploader = wp.media({
			            title: 'Custom image',
			            library : {
			                uploadedTo : wp.media.view.settings.post.id,
			                type : 'image'
			            },
			            button: {
			                text: 'Use this image'
			            },
			            multiple: false
			        }).on('select', function() {
			            var attachment = aw_uploader.state().get('selection').first().toJSON();
			            $('#cv_profile_image').val(attachment.url);
			        })
			        .open();
			    });
			});
	    </script>
	    <table>
	        <tr>
	            <td><a href="#" class="cv_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a></td>
	            <td><input type="text" name="cv_profile_image" id="cv_profile_image" value="<?php echo $image; ?>" style="width:500px;" /></td>
	        </tr>
	    </table>
	    <?php
    }

    public function cv_my_resume_about_boxes_callback($post)
    {
    	$post_id = $post->ID;
    	$resume_about = get_post_meta($post_id,'my-resume-about',true);
    	?>
    	<p class="meta-options cv_field">
        	<?php
        	wp_editor( $resume_about, 'my_resume_about_content', $settings = array( 'tinymce'=>true, 'textarea_name'=>'my_resume_about_content', 'wpautop' =>false,   'media_buttons' => false ,   'teeny' => false, 'quicktags'=>true, )   ); 
        	?>
    	</p>
    	<?php
    }

    public function cv_my_resume_objective_boxes_callback($post)
    {
    	$post_id = $post->ID;
    	$resume_objective = get_post_meta($post_id,'my-resume-objective',true);
    	?>
    	<table class="personal-detail-table">
    		<tr>
        	<td>
        	<textarea id="my_resume_objective" name="my_resume_objective" cols="150" rows="5"><?php echo $resume_objective; ?></textarea>  </td>
    	</table>
    	<?php
    }

    public function cv_my_resume_contact_detail_boxes_callback($post)
    {
    	$post_id = $post->ID;
    	$resume_email = get_post_meta($post_id,'my-resume-email',true);
    	$resume_mobile = get_post_meta($post_id,'my-resume-mobile',true);
    	?>
    	<table class="personal-detail-table">
    		<tr>
        		<td><label for="my_resume_email">Email</label></td>
        		<td><input id="my_resume_email" type="text" name="my_resume_email" value="<?php echo $resume_email; ?>"></td>
        	</tr>
        	<tr>
        		<td><label for="my_resume_mobile">Mobile No.</label></td>
        		<td><input id="my_resume_mobile" type="text" name="my_resume_mobile" value="<?php echo $resume_mobile; ?>"></td>
        	</tr>
    	</table>
    	<?php
    }

    public function cv_my_resume_work_boxes_callback($post)
    {
    	$post_id = $post->ID;
    	$work_experience = get_post_meta($post_id,'my-resume-work-experience',true);
    	$count = !empty($work_experience) ? count($work_experience) : 1;
    	?>
    	<script type="text/javascript">
    		jQuery(document).ready(function() {
    			var next_val = <?php echo $count;?>;
			   jQuery('.add-more-work-experience').click(function(){
				    //var $tr    = jQuery('.work-experience-table').find('.work-experience-wrap:first');
				    //var total = jQuery('.work-experience-wrap').length;
				    
				    var tr_html = '<tr class="work-experience-wrap">';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_designation">Designation</label>';
			        tr_html += '<input id="my_resume_designation" type="text" name="my_resume_work_experience['+next_val+'][designation]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_work_at">Work At</label>';
			        tr_html += '<input id="my_resume_work_at" type="text" name="my_resume_work_experience['+next_val+'][workat]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_from_to">From and To</label>';
			        tr_html += '<input id="my_resume_from_to" type="text" name="my_resume_work_experience['+next_val+'][fromto]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_about">About</label>';
			        tr_html += '<input id="my_resume_about" type="text" name="my_resume_work_experience['+next_val+'][about]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<a href="javascript:void(0);" class="remove-this-work-experience">Remove</a>';
			    	tr_html += '</td>';
	    			tr_html += '</tr>';
				    //var $clone = $tr.clone();
				    //$clone.find(':text').val('');
				    //$tr.after($clone);
				    //$tr.after($clone);
				    jQuery('.work-experience-table').append(tr_html);
				    hide_show_remove_btn();
				    next_val = next_val+1;
				});
			});

    		function hide_show_remove_btn()
    		{
	    		if(jQuery('.work-experience-wrap').length > 1)
	    		{
	    			jQuery('.remove-this-work-experience').show();
	    		}
	    		else
	    		{
	    			jQuery('.remove-this-work-experience').hide();
	    		}
    		}

			jQuery(document).on('click', '.remove-this-work-experience', function() {
		        jQuery(this).parents('.work-experience-wrap').remove();
		        hide_show_remove_btn();
		    });
    	</script>
    	<div class="work-experience-container">
    		<table class="work-experience-table">
    			<?php
    			if(!empty($work_experience))
		    	{
		    		$work_experience = array_values($work_experience);
		    			foreach ($work_experience as $key => $work_exp) 
		    			{	
		    				$designation = $work_exp['designation'];
		    				$workat = $work_exp['workat'];
		    				$fromto = $work_exp['fromto'];
		    				$about = $work_exp['about'];
		    				?>
				    		<tr class="work-experience-wrap">
						    	<td class="meta-options cv_field">
						        	<label for="my_resume_designation">Designation</label>
						        	<input id="my_resume_designation" type="text" name="my_resume_work_experience[<?php echo $key; ?>][designation]" value="<?php echo $designation; ?>">
						    	</td>
						    	<td class="meta-options cv_field">
						        	<label for="my_resume_work_at">Work At</label>
						        	<input id="my_resume_work_at" type="text" name="my_resume_work_experience[<?php echo $key; ?>][workat]" value="<?php echo $workat; ?>">
						    	</td>
						    	<td class="meta-options cv_field">
						        	<label for="my_resume_from_to">From and To</label>
						        	<input id="my_resume_from_to" type="text" name="my_resume_work_experience[<?php echo $key; ?>][fromto]" value="<?php echo $fromto; ?>">
						    	</td>
						    	<td class="meta-options cv_field">
						        	<label for="my_resume_about">About</label>
						        	<input id="my_resume_about" type="text" name="my_resume_work_experience[<?php echo $key; ?>][about]" value="<?php echo $about; ?>">
						    	</td>
						    	<td class="meta-options cv_field">
						        	<a href="javascript:void(0);" class="remove-this-work-experience">Remove</a>
						    	</td>
			    			</tr>
			    			<?php
			    		}
	    		}
	    		?>
    		</table>
    		<a href="javascript:void(0);" class="button button-primary add-more-work-experience">Add Work Experience</a>
    	</div>
    	<?php
    }

    public function cv_my_resume_education_boxes_callback($post)
    {
    	$post_id = $post->ID;
    	$education = get_post_meta($post_id,'my-resume-education',true);
    	
    	$count = !empty($education) ? count($education) : 1;
    	?>
    	<script type="text/javascript">
    		jQuery(document).ready(function() {
    			var next_edu_val = <?php echo $count;?>;
			   jQuery('.add-more-education').click(function(){
				    //var $tr    = jQuery('.work-experience-table').find('.work-experience-wrap:first');
				    //var total = jQuery('.work-experience-wrap').length;
				    
				    var tr_html = '<tr class="education-wrap">';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_degree">Degree</label>';
			        tr_html += '<input id="my_resume_degree" type="text" name="my_resume_education['+next_edu_val+'][degree]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_complete_from">Complete from</label>';
			        tr_html += '<input id="my_resume_complete_from" type="text" name="my_resume_education['+next_edu_val+'][completefrom]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_degree_from_to">From and To</label>';
			        tr_html += '<input id="my_resume_degree_from_to" type="text" name="my_resume_education['+next_edu_val+'][fromto]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<label for="my_resume_about_degree">About</label>';
			        tr_html += '<input id="my_resume_about_degree" type="text" name="my_resume_education['+next_edu_val+'][aboutdegree]" value="">';
			    	tr_html += '</td>';
			    	tr_html += '<td class="meta-options cv_field">';
			        tr_html += '<a href="javascript:void(0);" class="remove-this-education">Remove</a>';
			    	tr_html += '</td>';
	    			tr_html += '</tr>';
				    //var $clone = $tr.clone();
				    //$clone.find(':text').val('');
				    //$tr.after($clone);
				    //$tr.after($clone);
				    jQuery('.education-table').append(tr_html);
				    education_hide_show_remove_btn();
				    next_edu_val = next_edu_val+1;
				});
			});

    		function education_hide_show_remove_btn()
    		{
	    		if(jQuery('.education-wrap').length > 1)
	    		{
	    			jQuery('.remove-this-education').show();
	    		}
	    		else
	    		{
	    			jQuery('.remove-this-education').hide();
	    		}
    		}

			jQuery(document).on('click', '.remove-this-education', function() {
		        jQuery(this).parents('.education-wrap').remove();
		        education_hide_show_remove_btn();
		    });
    	</script>
    	<div class="education-container">
    		<table class="education-table">
    			<?php
    			if(!empty($education))
    			{
    				$education = array_values($education);
	    			foreach ($education as $key => $edu) 
	    			{	
	    				$degree = $edu['degree'];
	    				$completefrom = $edu['completefrom'];
	    				$fromto = $edu['fromto'];
	    				$aboutdegree = $edu['aboutdegree'];
	    				?>
			    		<tr class="education-wrap">
					    	<td class="meta-options cv_field">
					        	<label for="my_resume_degree">Degree</label>
					        	<input id="my_resume_degree" type="text" name="my_resume_education[<?php echo $key; ?>][degree]" value="<?php echo $degree; ?>">
					    	</td>
					    	<td class="meta-options cv_field">
					        	<label for="my_resume_complete_from">Complete from</label>
					        	<input id="my_resume_complete_from" type="text" name="my_resume_education[<?php echo $key; ?>][completefrom]" value="<?php echo $completefrom; ?>">
					    	</td>
					    	<td class="meta-options cv_field">
					        	<label for="my_resume_degree_from_to">From and To</label>
					        	<input id="my_resume_degree_from_to" type="text" name="my_resume_education[<?php echo $key; ?>][fromto]" value="<?php echo $fromto; ?>">
					    	</td>
					    	<td class="meta-options cv_field">
					        	<label for="my_resume_about_degree">About</label>
					        	<input id="my_resume_about_degree" type="text" name="my_resume_education[<?php echo $key; ?>][aboutdegree]" value="<?php echo $about; ?>">
					    	</td>
					    	<td class="meta-options cv_field">
					        	<a href="javascript:void(0);" class="remove-this-education">Remove</a>
					    	</td>
		    			</tr>
		    			<?php
		    		}
		    	}
	    		?>
    		</table>
    		<a href="javascript:void(0);" class="button button-primary add-more-education">Add Education</a>
    	</div>
    	<?php
    }

    function cv_save_meta_box( $post_id ) {

	    update_post_meta( $post_id,'my-resume-name', sanitize_text_field( $_POST['my_resume_name'] ) );   
	    update_post_meta( $post_id,'my-resume-email',  $_POST['my_resume_email'] );   
	    update_post_meta( $post_id,'my-resume-mobile',  $_POST['my_resume_mobile']  );   
	    update_post_meta( $post_id,'my-resume-about',  $_POST['my_resume_about_content']  );   
	    update_post_meta( $post_id,'my-resume-objective', $_POST['my_resume_objective'] ) ;   
	    update_post_meta( $post_id,'my-resume-work-experience',  $_POST['my_resume_work_experience'] );   
	    update_post_meta( $post_id,'my-resume-education',  $_POST['my_resume_education'] );   
	    update_post_meta( $post_id,'my-resume-dob',  $_POST['my_resume_dob'] );   

	    if (array_key_exists('cv_profile_image', $_POST)) {
	        update_post_meta($post_id,'cv_profile_image',$_POST['cv_profile_image']);
	    }
	}

}

$my_class = new CVcustomFunctions();