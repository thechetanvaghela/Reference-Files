<?php
function custom_array_search($array, $key, $value) {
    $results = array();
      
    // if it is array
    if (is_array($array)) {
          
        // if array has required key and value
        // matched store result 
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }
          
        // Iterate for each element in array
        foreach ($array as $subarray) {
              
            // recur through each element and append result 
            $results = array_merge($results, 
                    custom_array_search($subarray, $key, $value));
        }
    }
  
    return $results;
}
function mytheme_add_product_content($product, $data)
{
	if(!empty($data))
	{
		$product_id = $data['id'];
		$name = $data['name'];
		$meta_data = $data['meta_data'];
		if(!empty($meta_data))
		{
			$result = custom_array_search($meta_data, 'key', 'spec_sheet');
			if(!empty($result))
			{
				$file_url = $result[0]['value'];
				if(!empty($file_url))
				{
					$file_url = filter_var($file_url, FILTER_SANITIZE_URL);
					if (filter_var($file_url, FILTER_VALIDATE_URL) !== false) 
					{

						$file_array  = [ 'name' => wp_basename( $file_url ), 'tmp_name' => download_url( $file_url ) ];
						$attchemnt_id = media_handle_sideload( $file_array, $product_id, $name );
						if(!empty($attchemnt_id))
						{
							$uploaded_file_url = wp_get_attachment_url( $attchemnt_id );
							if(!empty($uploaded_file_url))
							{
								update_post_meta($product_id,'spec_sheet',$uploaded_file_url);
							}
						}
					}
				}
			}
		}	
	}
}
add_action('woocommerce_product_import_inserted_product_object', 'mytheme_add_product_content',10,2);