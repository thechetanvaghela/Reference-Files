<?php
/*
# add new links in user listing page in wp-admin/ users
# https://prnt.sc/pgtqzp
*/
add_filter( 'views_users', function( $views ) 
{   

    global $wpdb; 
    $sql = "SELECT u.ID
        FROM $wpdb->users u
        INNER JOIN $wpdb->usermeta m ON m.user_id = u.ID
        WHERE m.meta_key = 'wp_capabilities'
        AND m.meta_value LIKE '%customer%'
        ORDER BY u.user_registered";

    $result = $wpdb->get_results( $sql, 'ARRAY_A' );
    $total_user = count($result);
    $approved_user = 0;
    $unapproved_user = 0;
    if(!empty($result))
    {
        foreach($result as $row)
        {
            $user_id = $row['ID'];
            $approved_status = get_user_meta($user_id, '_user_activation_status', true);
            if(empty($approved_status))
            {
                $unapproved_user++;
            }
            else
            {
                $approved_user++;
            }
        }
        $app_user_class= '';
        $unappuser_class= '';
        if($_GET[ 'user_status'] == 'approved')
        {
            $app_user_class =  "current";
        }   
        elseif($_GET[ 'user_status'] == 'unapproved')
        {
            $unappuser_class =  "current";
        } 
        if(!empty($approved_user))
        {
            $views['approved_users'] = '<a href="' . admin_url( 'users.php' ) . '?user_status=approved" class="'.$app_user_class.'">' . __( 'Approved ('.$approved_user.')', 'basta' ) . '</a>';
        }
        if(!empty($unapproved_user))
        {
            $views['unapproved_users'] = '<a href="' . admin_url( 'users.php' ) . '?user_status=unapproved" class="'.$unappuser_class.'">' . __( 'Unpproved ('.$unapproved_user.')', 'basta' ) . '</a>';
        }
    }
    return $views;
} );

/*
# change user listing quesry as per link
*/
function filter_users_by_course_section($query)
{
    global $pagenow;
    if (is_admin() && 'users.php' == $pagenow) {
        if (!empty($_GET[ 'user_status'])) 
        {
            $user_status= '';
            if($_GET[ 'user_status'] == 'approved')
            {
                $user_status = 1;
            }   
            elseif($_GET[ 'user_status'] == 'unapproved')
            {
                $user_status = 0;
            }  
            $meta_query = [['key' => '_user_activation_status','value' => $user_status, 'compare' => 'LIKE']];
            $query->set('meta_key', '_user_activation_status');
            $query->set('meta_query', $meta_query);
        }
    }
}
add_filter('pre_get_users', 'filter_users_by_course_section');