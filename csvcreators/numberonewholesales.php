<?php

// Include config and initiate
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';
include_once __DIR__ . '/common.php';
include_once __DIR__ . '/getCategoryStr.php';

// Get Database
$db = new Db();

$data = $db->query("SELECT * from products_data WHERE source='numberonewholesales'");

//$data = $db->query("SELECT * from products_data WHERE source='numberonewholesales' AND updated_on >= CURDATE()");
$tempData = array();
$csvData = array();
$count = 0;

$colorSet = array();
$sizeArray = array();
$packArray = array();

if (file_exists('numberonewholesales.csv'))
    unlink('numberonewholesales.csv');

$fp = fopen("numberonewholesales.csv", "a+");

foreach ($data as $value) {

	$imgStr = '';
    $categoryStr = '';
    $sizeArray = array();
    $packArray = array();

    $fabric = '';
    $made = 'Imported';


    $packQtySizeArray = array();
    $packQtyArray = array();
    $packSizeArray = array();
    
    $tempData = json_decode($value['data'],TRUE);

 	foreach ($tempData['images'] as $img) {
        $imgMain = str_replace(array(",", '\'', '"' ), "", $img);
        $imgStr .= $value['id'].'_wl_'.basename($imgMain).";";

        // download_remote_file_with_curl(trim('http://numberonewholesales.com/'.$imgMain), $value['id']);
    }

    // $categoryStr = getCategory($value['category'],'');


    /*Get Category String*/
    $categoryStr = getCategoryString($value['category'],$value['subcategory'],$tempData['title'][0]);
    $temp = explode('/',$categoryStr);

    $category = $temp[0];

    $subCategory = (isset($temp[1])?$temp[1]:'');
    /*Get dimension*/
    $dimension = getDimensions($category,$subCategory);

    $price = getSalePrice(str_replace('$', '', trim($tempData['price'])));

    $pack = (isset($tempData['pack'])?$tempData['pack']:'');

    $removableChars = array(" ", "_", ":", ";");
    $pack = str_replace('<br>', '', $pack);
    $tpack = strtolower(trim(str_replace($removableChars, "-", $pack)));
    $qtyExist = false;

    if ((strpos($tpack, 'onesize') !== false) || (strpos($tpack, 'one-size') !== false)) {
        $packSizeArray = array(0 => 'onesize');
        $packQtyArray = $tempData['packValue'];
    }
    else if($pack == 'One Size') {
        $packSizeArray = array(0 => 'onesize');
        $packQtyArray = $tempData['packValue'];
    }    
    else {
        /*Pack details*/
        $packData = explode('-',$pack);
          
        if(count($packData) > 0) {
            foreach ($packData as $data) {
                preg_match_all("/(([^()]+))/", $data,$matches);
                $results = $matches[1];

                array_push($packSizeArray, $matches[1][0]);

                if(isset($matches[1][1]))
                {
                    array_push($packQtyArray, $matches[1][1]);
                    $qtyExist = true;
                }
                else 
                    array_push($packQtyArray, '-');
            }
        }
    }

    $temp = array();
 
    if(isset($tempData['fab']))
        $temp = explode('<br>',$tempData['fab']);
    if(count($temp) == 2) {
        $fabric = trim($temp[0]);
        if(strpos($temp[0], 'usa') !== false)
            $made = 'USA';
    }

    if(!$qtyExist) {
        if(count($tempData['packValue'])>1) {
            $pValue = 0;
            foreach ($tempData['packValue'] as $v) {
                $pValue += $v;
            }

            $tempData['packValue'][0] = $pValue;
        }
    }

    $qty = 100;

    if($price == 0 || is_null($price) || $price == '') {
        $value['status'] = 0;
        $qty = 0;
    }

    // If not a valid category
    if(strlen($categoryStr) == 0 || $categoryStr == '' || $value['status'] == 0) {
        $qty = 0;
        $value['status'] = 0;
    }

    /*If its not empty category string*/
    {
        foreach ($tempData['colors'] as $color) {   

            if(strlen($color)>64)
                $color = 'As Preview';

        	$csvData[$count]['sku']                       = 'WL#'.$value['id'].'#'.trim($color);
            $csvData[$count]['bin_location']              = '';
            $csvData[$count]['type']                      = 'simple';
            $csvData[$count]['store']                     = 'default';

            $csvData[$count]['name']                      = $tempData['title'][0].' '.$color;
            $csvData[$count]['description']               = $tempData['title'][0];
            $csvData[$count]['short_description']         = $tempData['title'][0];
            $csvData[$count]['price']                     = $price;
            $csvData[$count]['qty']                       = $qty;
            $csvData[$count]['weight']                    = $dimension['weight'];

            $csvData[$count]['is_in_stock']               = $value['status'];
            $csvData[$count]['manage_stock']              = 1;
            $csvData[$count]['use_config_manage_stock']   = 1;

            $csvData[$count]['model_size_desc']           = '';

            $csvData[$count]['status']                    = 1;
            $csvData[$count]['visibility']                = '"Not Visible Individually"';
            $csvData[$count]['brand']                     = '';

            $csvData[$count]['categories']                = $categoryStr;

            $csvData[$count]['pack']                      = $tempData['pack'].' ( '.$tempData['packValue'][0].' )';
            $csvData[$count]['fabric']                    = $fabric;
            $csvData[$count]['made']                      = $made;

            $csvData[$count]['source']                    = 'numberonewholesales';
            $csvData[$count]['source_sku']                = $tempData['style'];

            $csvData[$count]['tax_class_id']              = 'None';

            $csvData[$count]['image']                     = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['mainImage'][0]));
            $csvData[$count]['small_image']               = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['mainImage'][0]));
            $csvData[$count]['thumbnail']                 = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['mainImage'][0]));
            $csvData[$count]['image_label']                = $tempData['title'][0];
            $csvData[$count]['small_image_label']         = $tempData['title'][0];
            $csvData[$count]['thumbnail_label']           = $tempData['title'][0];

            $csvData[$count]['media_gallery']             = $imgStr;

            $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
            $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
            $csvData[$count]['auctioninc_product_height'] = $dimension['height'];
            $csvData[$count]['auctioninc_calc_method']    = 'C';

            $csvData[$count]['attribute_set']             = 'Default';
            $csvData[$count]['configurable_attributes']   = 'leggingscolor';

            /*Setting size*/
            $csvData[$count]['leggingspacksize']          = implode(",", $packSizeArray);
            $csvData[$count]['leggingspackqty']           = implode(",", $packQtyArray);
            $csvData[$count]['prod_url']                  = $value['url'];
            $csvData[$count++]['leggingscolor']           = trim($color);
        	
        }

        $csvData[$count]['sku']                       = 'WL#'.$value['id'];
        $csvData[$count]['bin_location']              = '';
        $csvData[$count]['type']                      = 'configurable';
        $csvData[$count]['store']                     = 'default';

        $csvData[$count]['name']                      = $tempData['title'][0];
        $csvData[$count]['description']               = $tempData['title'][0];
        $csvData[$count]['short_description']         = $tempData['title'][0];
        $csvData[$count]['price']                     = $price*$tempData['packValue'][0];
        $csvData[$count]['qty']                       = $qty;
        $csvData[$count]['weight']                    = $dimension['weight'];

        $csvData[$count]['is_in_stock']               = $value['status'];
        $csvData[$count]['manage_stock']              = 1;
        $csvData[$count]['use_config_manage_stock']   = 1;

        $csvData[$count]['model_size_desc']           = '';

        $csvData[$count]['status']                    = 1;
        $csvData[$count]['visibility']                = '"Catalog, Search"';
        $csvData[$count]['brand']                     = '';

        $csvData[$count]['categories']                = $categoryStr;

        $csvData[$count]['pack']                      = $tempData['pack'].' ( '.$tempData['packValue'][0].' )';
        $csvData[$count]['fabric']                    = $fabric;
        $csvData[$count]['made']                      = $made;

        $csvData[$count]['source']                    = 'numberonewholesales';
        $csvData[$count]['source_sku']                = $tempData['style'];

        $csvData[$count]['tax_class_id']              = 'None';

        $csvData[$count]['image']                     = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['mainImage'][0]));
        $csvData[$count]['small_image']               = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['mainImage'][0]));
        $csvData[$count]['thumbnail']                 = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['mainImage'][0]));
        $csvData[$count]['image_label']                = $tempData['title'][0];
        $csvData[$count]['small_image_label']         = $tempData['title'][0];
        $csvData[$count]['thumbnail_label']           = $tempData['title'][0];

        $csvData[$count]['media_gallery']             = $imgStr;

        $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
        $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
        $csvData[$count]['auctioninc_product_height'] = $dimension['height'];
        $csvData[$count]['auctioninc_calc_method']    = 'C';

        $csvData[$count]['attribute_set']             = 'Default';
        $csvData[$count]['configurable_attributes']   = 'leggingscolor';

        /*Setting size*/
        $csvData[$count]['leggingspacksize']          = implode(",", $packSizeArray);
        $csvData[$count]['leggingspackqty']           = implode(",", $packQtyArray);
        $csvData[$count]['prod_url']                  = $value['url'];
        $csvData[$count++]['leggingscolor']           = '';
    }
}


fputcsv($fp, array_keys($csvData[0]));
foreach ($csvData as $data) {
   fputcsv($fp, $data);
}

fclose($fp);

?>
