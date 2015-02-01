<?php 

// Include config and initiate
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';
include_once __DIR__ . '/common.php';

// Get Database
$db = new Db();

$data = $db->query("SELECT * from products_data WHERE source='sngapparelinc' LIMIT 10");

$tempData = array();
$csvData = array();
$count = 0;

$colorSet = array();
$sizeArray = array();
$packArray = array();

$fp = fopen("output_new.csv", "a+");

// Loop through all products
foreach ($data as $value) {

	$imgStr = '';
    $sizeArray = array();
    $packArray = array();

    /*Bundle SKU Array*/
    $bundleSKU = array();
    
    $tempData = json_decode($value['data'],TRUE);

    /*Get Category String*/
    $categoryStr = getCategory($value['category'],$value['subcategory']);
    /*Get dimension*/
    $dimension = getDimensions($value['category'],$value['subcategory']);
    /*Get Sizes*/
    foreach ($tempData['color_size'] as $cdata) {
        $keys = array_keys($cdata['size']);

        foreach ($keys as $k) {
            
            if($k == 'sm') {
                $packArray['s/m'] = $cdata['size'][$k];
            }
            else if($k == 'ml') {
                $packArray['m/l'] = $cdata['size'][$k];
            }
            else if($k == 'lxl') {
                $packArray['l/xl'] = $cdata['size'][$k];
            }
            else if($k == 'xlxxl') {
                $packArray['xl/xxl'] = $cdata['size'][$k];
            }
            else if($k == 'xxlxxxl') {
                $packArray['xxl/xxxl'] = $cdata['size'][$k];
            }
            else if($k == 'xxxlxxxxl') {
                $packArray['xxxl/xxxxl'] = $cdata['size'][$k];
            } else {
                $packArray[$k] = $cdata['size'][$k];
            }

            $sizeArray = array_keys($packArray);
        }
    } 
    
    // If not a valid category
    if(strlen($categoryStr) == 0)
        continue;

    foreach ($tempData['images'] as $img) {
        $imgMain = str_replace(array(",", '\'', '"' ), "", $img['large']);
        $imgStr .= $value['id'].'_wl_'.basename($imgMain).";";
        //download_remote_file_with_curl(trim($imgMain));

        if(isset($img['small'])) {
            $tempimg = str_replace(array(",", '\'', '"' ), "", $img['small']);
            //download_remote_file_with_curl(trim($imgMain));
        }
    }

    /*Loop through all sizes*/
    foreach ($sizeArray as $size) {
    	/*Loop through all colors*/
    	foreach ($tempData['color_size'] as $color) {
    		/*Simple Products*/
    		$csvData[$count]['sku']                       = 'WL#'.$value['id'].'#'.$color['color'].'#'.$size;
    		$csvData[$count]['bin_location']			  = '';
            $csvData[$count]['type']                      = 'simple';
            $csvData[$count]['store']                     = 'default';

            $csvData[$count]['name']                      = $tempData['title'].' '.$color['color'].' '.$size;
            $csvData[$count]['description']               = $tempData['title'];
            $csvData[$count]['short_description']         = $tempData['title'];
            $csvData[$count]['price']                     = $tempData['price'];
            $csvData[$count]['qty']                       = 1;
            $csvData[$count]['weight']                    = getWeight($value['category'],$value['subcategory']);

            $csvData[$count]['is_in_stock']               = 1;
            $csvData[$count]['manage_stock']              = 1;
            $csvData[$count]['use_config_manage_stock']   = 1;

            $csvData[$count]['status']                    = 1;
            $csvData[$count]['visibility']                = '"Not Visible Individually"';
            $csvData[$count]['categories']                = getCategory($value['category'],$value['subcategory']);

            $csvData[$count]['pack']                      = $tempData['pack'];
            $csvData[$count]['fabric']                    = $tempData['fabric'];
            $csvData[$count]['made']                      = $tempData['made'];

            $csvData[$count]['source']                    = 'sngapparelinc';
            $csvData[$count]['source_sku']                = $tempData['item_no'];

            $csvData[$count]['tax_class_id']              = 'None';

            $csvData[$count]['image']                     = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['large']));
            $csvData[$count]['small_image']               = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
            $csvData[$count]['thumbnail']                 = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
            $csvData[$count]['image_label']               = $tempData['title'];
            $csvData[$count]['small_image_label']         = $tempData['title'];
            $csvData[$count]['thumbnail_label']           = $tempData['title'];

            $csvData[$count]['media_gallery']             = "$imgStr";

            $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
            $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
            $csvData[$count]['auctioninc_product_height'] = $dimension['height'];

            $csvData[$count]['attribute_set']             = 'Default';
            $csvData[$count]['configurable_attributes']   = 'leggingscolor';

            $csvData[$count]['model_size']				  = '';
		    $csvData[$count]['brand']				  	  = '';
            /*Setting size*/
            $csvData[$count]['size']                       = $size;

            /*Grouped Product*/
            $csvData[$count]['grouped_skus']			   = '';
            $csvData[$count]['weight_type']			   	   = '';
            $csvData[$count]['price_type']			   	   = '';
            $csvData[$count]['price_view']			   	   = '';


            $csvData[$count++]['leggingscolor']            = $color['color'];
    	}
    	/*Config Products*/
    	$csvData[$count]['sku']                       = 'WL#'.$value['id'].'#'.$size;
    	array_push($bundleSKU, 'WL#'.$value['id'].'#'.$size);

    	$csvData[$count]['bin_location']			  = '';

	    $csvData[$count]['type']                      = 'configurable';
	    $csvData[$count]['store']                     = 'default';

	    $csvData[$count]['name']                      = $tempData['title'];
	    $csvData[$count]['description']               = $tempData['title'];
	    $csvData[$count]['short_description']         = $tempData['title'];
	    $csvData[$count]['price']                     = $tempData['price'];
	    $csvData[$count]['qty']                       = 1;
	    $csvData[$count]['weight']                    = getWeight($value['category'],$value['subcategory']);
	    
	    $csvData[$count]['is_in_stock']               = 1;
	    $csvData[$count]['manage_stock']              = 1;
	    $csvData[$count]['use_config_manage_stock']   = 1;
	    
	    $csvData[$count]['status']                    = 1;
	    $csvData[$count]['visibility']                = '"Not Visible Individually"';
	    $csvData[$count]['categories']                = getCategory($value['category'],$value['subcategory']);

	    $csvData[$count]['pack']                      = $tempData['pack'];
	    $csvData[$count]['fabric']                    = $tempData['fabric'];
	    $csvData[$count]['made']                      = $tempData['made'];

	    $csvData[$count]['source']                    = 'sngapparelinc';
	    $csvData[$count]['source_sku']                = $tempData['item_no'];

	    $csvData[$count]['tax_class_id']              = 'None';

	    
	    $csvData[$count]['image']                     = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['large']));
	    $csvData[$count]['small_image']               = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
	    $csvData[$count]['thumbnail']                 = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
	    $csvData[$count]['image_label']               = $tempData['title'];
	    $csvData[$count]['small_image_label']         = $tempData['title'];
	    $csvData[$count]['thumbnail_label']           = $tempData['title'];

	    $csvData[$count]['media_gallery']             = "$imgStr";

	    $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
	    $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
	    $csvData[$count]['auctioninc_product_height'] = $dimension['height'];

	    $csvData[$count]['attribute_set']             = 'Default';
	    $csvData[$count]['configurable_attributes']   = 'leggingscolor';

	    $csvData[$count]['model_size']				  = '';
	    $csvData[$count]['brand']				  	  = '';

	    /*Setting size*/
    	$csvData[$count]['size']                       = '';

	    $csvData[$count]['grouped_skus']			   = '';
        $csvData[$count]['weight_type']			   	   = '';
        $csvData[$count]['price_type']			   	   = '';
        $csvData[$count]['price_view']			   	   = '';

	    $csvData[$count++]['leggingscolor']            = '';

    }
    /*Bundle Products*/
    $csvData[$count]['sku']                       = 'WL#'.$value['id'];
    $csvData[$count]['bin_location']			  = '';
    $csvData[$count]['type']                      = 'bundle';
    $csvData[$count]['store']                     = 'default';

    $csvData[$count]['name']                      = $tempData['title'];
    $csvData[$count]['description']               = $tempData['title'];
    $csvData[$count]['short_description']         = $tempData['title'];
    $csvData[$count]['price']                     = $tempData['price'];
    $csvData[$count]['qty']                       = 1;
    $csvData[$count]['weight']                    = getWeight($value['category'],$value['subcategory']);
    
    $csvData[$count]['is_in_stock']               = 1;
    $csvData[$count]['manage_stock']              = 1;
    $csvData[$count]['use_config_manage_stock']   = 1;
    
    $csvData[$count]['status']                    = 1;
    $csvData[$count]['visibility']                = '"Catalog, Search"';
    $csvData[$count]['categories']                = getCategory($value['category'],$value['subcategory']);

    $csvData[$count]['pack']                      = $tempData['pack'];
    $csvData[$count]['fabric']                    = $tempData['fabric'];
    $csvData[$count]['made']                      = $tempData['made'];

    $csvData[$count]['source']                    = 'sngapparelinc';
    $csvData[$count]['source_sku']                = $tempData['item_no'];

    $csvData[$count]['tax_class_id']              = 'None';

    $csvData[$count]['image']                     = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['large']));
    $csvData[$count]['small_image']               = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
    $csvData[$count]['thumbnail']                 = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
    $csvData[$count]['image_label']               = $tempData['title'];
    $csvData[$count]['small_image_label']         = $tempData['title'];
    $csvData[$count]['thumbnail_label']           = $tempData['title'];

    $csvData[$count]['media_gallery']             = "$imgStr";

    $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
    $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
    $csvData[$count]['auctioninc_product_height'] = $dimension['height'];

    $csvData[$count]['attribute_set']             = 'Default';
    $csvData[$count]['configurable_attributes']   = 'leggingscolor';
    
    $csvData[$count]['model_size']				  = '';
    $csvData[$count]['brand']				  	  = '';
    /*Setting size*/
    $csvData[$count]['size']                       = '';

    $grouped_skus = '';

    foreach ($bundleSKU as $bsku) {

    	if($grouped_skus != '')
    		$grouped_skus .= ';';

		$grouped_skus .= $tempData['title'].':'.$bsku.':1:0:1:0';
    }
    $csvData[$count]['grouped_skus']			   = $grouped_skus;
    $csvData[$count]['weight_type']			   	   = 0;
    $csvData[$count]['price_type']			   	   = 0;
    $csvData[$count]['price_view']			   	   = 1;

    $csvData[$count++]['leggingscolor']            = '';
}

fputcsv($fp, array_keys($csvData[0]));
foreach ($csvData as $data) {
   fputcsv($fp, $data);
}

fclose($fp);