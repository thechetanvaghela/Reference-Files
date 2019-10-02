<?php
function search_in_array($array, $search_list) { 
    // Create the result array 
    $result = array(); 
    // Iterate over each array element 
    foreach ($array as $key => $value) { 
        // Iterate over each search condition 
        foreach ($search_list as $k => $v) { 
            // If the array element does not meet 
            // the search condition then continue 
            // to the next element 
            if (!isset($value[$k]) || $value[$k] != $v) 
            { 
                // Skip two loops 
                continue 2; 
            } 
        } 
        // Append array element's key to the 
        //result array 
        $result[] = $value; 
    }  
    // Return result  
    return $result; 
}


$wp_users_payment_summary = 'a:4:{i:0;a:3:{s:15:"iu_payment_date";i:1474934400;s:10:"iu_payment";s:5:"1,000";s:15:"iu_payment_year";s:4:"2016";}i:1;a:3:{s:15:"iu_payment_date";i:1506470400;s:10:"iu_payment";s:5:"1,000";s:15:"iu_payment_year";s:4:"2017";}i:2;a:3:{s:15:"iu_payment_date";b:0;s:10:"iu_payment";s:1:"-";s:15:"iu_payment_year";s:4:"2018";}i:3;a:3:{s:15:"iu_payment_date";i:1569628800;s:10:"iu_payment";s:4:"1000";s:15:"iu_payment_year";s:4:"2019";}}';
$get_user_summary = get_user_meta(23, 'wp-users-payment-summary',true);
if(!empty($get_user_summary))
{
	$get_user_summary = array_values($get_user_summary);
	if (!empty($get_user_summary)) {
		/*echo "<pre>";
		print_r($get_user_summary);
		echo "</pre>";*/
		// Define search list with multiple key=>value pair 
		$search_items = array('iu_payment_year'=> date('Y')); 
		// Call search and pass the array and 
		// the search list 
		$res = search_in_array($get_user_summary, $search_items); 

	}
}
  
// Print search result 
foreach ($res as $var) { 
	$iu_payment_date = (!empty($iu_get_payment_date)) ? date("F j, Y l -  h:i:s A",$var['iu_payment_date']) : "-";
    echo 'Year: ' . $var['iu_payment_year'] . '<br>'; 
    echo 'Date: ' .$iu_payment_date. '<br>'; 
    echo 'Payment: ' . $var['iu_payment'] . '<br>';         
} 

#--------------------------------------------------------------------------------------

function searchForId($search_value, $array, $id_path) { 
  
    // Iterating over main array 
    foreach ($array as $key1 => $val1) { 
  
        $temp_path = $id_path; 
          
        // Adding current key to search path 
        array_push($temp_path, $key1); 
  
        // Check if this value is an array 
        // with atleast one element 
        if(is_array($val1) and count($val1)) { 
  
            // Iterating over the nested array 
            foreach ($val1 as $key2 => $val2) { 
  
                if($val2 == $search_value) { 
                          
                    // Adding current key to search path 
                    array_push($temp_path, $key2); 
                          
                    return join(" --> ", $temp_path); 
                } 
            } 
        } 
          
        elseif($val1 == $search_value) { 
            return join(" --> ", $temp_path); 
        } 
    } 
      
    return null; 
} 