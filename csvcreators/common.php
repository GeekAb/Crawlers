<?php 
/*
Update data for no data fields
*/
function getDimensions($category, $subCategory) {

	$category = strtolower($category);

	$removableChars = array(" ", "_", ":", ";");
	$subcat = strtolower(trim(str_replace($removableChars, "-", $subCategory)));

	/*-------------------------------Main Category Leggings-----------------------------------------*/

	if ($category == 'leggings' || ($category == 'active-wear' || $category == 'activewear')) {

		/*Weight : 0.3*/
		if ($subcat == 'basic' || $subcat == 'printed' || $subcat == 'missy' || $subcat == 'leather' || $subcat == 'sublimation' || $subcat == 'capri') {
				
			return array('length' => 13,'width' => 10,'height' => 3, 'weight' => 2);
		}

		else if ($subcat == 'jeggings')
			return array('length' => 13,'width' => 12,'height' => 4, 'weight' => 3);

		else if($subcat == 'capri')
			return array('length' => 13,'width' => 12,'height' => 2, 'weight' => 2);

		return array('length' => 13,'width' => 12,'height' => 3, 'weight' => 3);
	}
	
	/*-------------------------------Main Category Pants-----------------------------------------*/

	else if ($category == 'pants') {

		if ($subcat == 'harem')
			return array('length' => 10,'width' => 15,'height' => 3, 'weight' => 3);

		else if ($subcat == 'palazzo')
			return array('length' => 15,'width' => 11,'height' => 3, 'weight' => 3);

		else if ($subcat == 'jogger')
			return array('length' => 15,'width' => 14,'height' => 2, 'weight' => 3);
		
		else if ($subcat == 'shorts'){
			return array('length' => 12,'width' => 10,'height' => 2, 'weight' => 2);
		}

		return array('length' => 10,'width' => 15,'height' => 3, 'weight' => 3);
	}

	/*--------------------------------Main Category Skirts---------------------------------------*/

	else if ($category == 'skirts')
		return array('length' => 16,'width' => 10,'height' => 5, 'weight' => 3);
	
	/*--------------------------------Main Category Plus Size-----------------------------------------*/

	else if ($category == 'plussize' || $category == 'plus-size') {

		if ($subcat == 'skirts')
			return array('length' => 16,'width' => 10,'height' => 5, 'weight' => 3);

		else if ($subcat == 'activewear' || $subcat == 'activewear/leggings' || $subcat == 'activewear/shorts' || $subcat == 'activewear/capri')
			return array('length' => 13,'width' => 12,'height' => 4, 'weight' => 3);

		return array('length' => 14,'width' => 10,'height' => 3, 'weight' => 3);
	}

	/*--------------------------------Main Category Kids--------------------------------------*/

	else if ($category == 'kids') {
		return array('length' => 13,'width' => 10,'height' => 2, 'weight' => 2);
	}

	else if ($category == 'activewear' || $category == 'active-wear') {
		return array('length' => 10,'width' => 15,'height' => 3, 'weight' => 3);
	}

}


function getSalePrice($costPrice) {
	return ((ceil($costPrice*2)-1)/2)+2.25;
}

function download_remote_file_with_curl($fileUrl,$id)
{
    $saveTo = $id.'_wl_'.basename($fileUrl);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch,CURLOPT_URL,$fileUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file_content = curl_exec($ch);
    curl_close($ch);

    if(!file_exists('/var/www/wholesaleleggings/media/import/')) {
        mkdir ('/var/www/wholesaleleggings/media/import/',0700, TRUE);
    }

    $downloaded_file = fopen('/var/www/wholesaleleggings/media/import/'.$saveTo, 'w+');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);

}
