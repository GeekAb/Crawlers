<?php 

// Include config and initiate
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';
include_once __DIR__ . '/common.php';
include_once __DIR__ . '/getCategoryStr.php';

// Get Database
$db = new Db();

$data = $db->query("SELECT * from products_data WHERE source='lashowroom'");

$tempData = array();
$csvData = array();
$count = 0;

$colorSet = array();
$sizeArray = array();
$packArray = array();

// Remove file if exist
if (file_exists('las.csv'))
    unlink('las.csv');

$fp = fopen("las.csv", "a+");

foreach ($data as $value) {
    
    $imgStr = '';
    $sizeArray = array();
    $packArray = array();
    
    $tempData = json_decode($value['data'],TRUE);

    // print_r($tempData);

    $packQtySizeArray = array();
    $packQtyArray = array();
    $packSizeArray = array();

    $comments = (isset($tempData['comments'])?$tempData['comments']:'');

    preg_match_all("/[1-9][0-9]*[a-zA-Z]/", $comments, $packQtySizeArray);

    preg_match_all("/[1-9][0-9]*/", implode(",", $packQtySizeArray[0]), $packQtyArray);

    preg_match_all("/[a-zA-Z]/", implode(",", $packQtySizeArray[0]), $packSizeArray);
    

    $totalQty = 0;
    
    foreach ($packQtyArray[0] as $q) {
        $totalQty += $q;
    }

    if($totalQty == 0) {
        preg_match_all("/[1-9][0-9]*/", $comments, $packQtySizeArray);

        $packSizeArray[0] = array(0 => 'onesize');
        $packQtyArray[0] = $packQtySizeArray[0];
    }

    $categoryStr = getCategoryString($tempData['category'],$tempData['subCategory'],$value['category']);

    $temp = explode('/',$categoryStr);

    $category = $temp[0];

    $subcategory = (isset($temp[1])?$temp[1]:'');
    /*Get dimension*/
    $dimension = getDimensions($category,$subcategory);


    foreach ($tempData['images'] as $img) {
        $imgMain = str_replace(array(",", '\'', '"' ), "", $img);
        $imgStr .= $value['id'].'_wl_'.basename($imgMain).";";

        download_remote_file_with_curl(trim($imgMain), $value['id']);
    }

    if(strtolower($tempData['made_in']) == 'usa')
        $made = 'USA';
    else 
        $made = 'Imported';

    if($categoryStr == '' || $value['status'] == 0) {
        $value['status'] = 0;
    }

    if($tempData['style_no'][0] == '')
        $value['status'] = 0;        

    {
        foreach ($tempData['color'] as $color) {

            $csvData[$count]['sku']                       = 'WL#'.$value['id'].'#'.$color;
            $csvData[$count]['bin_location']              = '';
            $csvData[$count]['type']                      = 'simple';
            $csvData[$count]['store']                     = 'default';

            $csvData[$count]['name']                      = $color.' '.$value['category'];
            // $csvData[$count]['description']               = $tempData['description'];
            // $csvData[$count]['short_description']         = $tempData['description'];
            $csvData[$count]['price']                     = $configPrice = str_replace('$', '', $tempData['price']);
            // $csvData[$count]['qty']                       = 100;
            $csvData[$count]['weight']                    = $dimension['weight'];

            $csvData[$count]['is_in_stock']               = $value['status'];
            $csvData[$count]['manage_stock']              = 1;
            $csvData[$count]['use_config_manage_stock']   = 1;

            $csvData[$count]['model_size_desc']           = $tempData['model_text'];

            $csvData[$count]['status']                    = 1;
            $csvData[$count]['visibility']                = '"Not Visible Individually"';
            $csvData[$count]['brand']                     = '';

            $csvData[$count]['categories']                = $categoryStr;

            $csvData[$count]['pack']                      = (isset($tempData['min_order'])?$tempData['min_order']:'Not Available');
            $csvData[$count]['fabric']                    = $tempData['fabric'];
            $csvData[$count]['made']                      = $made;

            $csvData[$count]['source']                    = 'lashowroom';
            $csvData[$count]['source_sku']                = $tempData['style_no'][0];

            $csvData[$count]['tax_class_id']              = 'None';

            $csvData[$count]['image']                     = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]));
            $csvData[$count]['small_image']               = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]));
            $csvData[$count]['thumbnail']                 = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]));
            $csvData[$count]['image_label']               = $tempData['description'];
            $csvData[$count]['small_image_label']         = $tempData['description'];
            $csvData[$count]['thumbnail_label']           = $tempData['description'];

            $csvData[$count]['media_gallery']             = $imgStr;

            $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
            $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
            $csvData[$count]['auctioninc_product_height'] = $dimension['height'];
            $csvData[$count]['auctioninc_calc_method']    = 'C';

            $csvData[$count]['attribute_set']             = 'Default';
            $csvData[$count]['configurable_attributes']   = 'leggingscolor';

            /*Setting size*/
            $csvData[$count]['leggingspacksize']          = $tempData['pack_size'];//implode(",", $packSizeArray[0]);
            $csvData[$count]['leggingspackqty']           = $tempData['pack_qty'];//implode(",", $packQtyArray[0]);
            $csvData[$count]['prod_url']                  = $value['url'];
            $csvData[$count++]['leggingscolor']           = $color;
        }

        // Config Price
        $configPrice = str_replace('$', '', $tempData['pack_price']);
        $configLength = $dimension['length'];
        $configWidth = $dimension['width'];
        $configHeight = $dimension['height'];

        $configWeight = $dimension['weight'];


        /*Create configurable Product*/
        $csvData[$count]['sku']                       = 'WL#'.$value['id'];
        $csvData[$count]['bin_location']              = '';
        $csvData[$count]['type']                      = 'configurable';
        $csvData[$count]['store']                     = 'default';

        $csvData[$count]['name']                      = $tempData['category'] .' '. $tempData['subCategory'];
        // $csvData[$count]['description']               = $tempData['description'];
        // $csvData[$count]['short_description']         = $tempData['description'];
        $csvData[$count]['price']                     = $configPrice;
        // $csvData[$count]['qty']                       = 100;
        $csvData[$count]['weight']                    = $configWeight;
        
        $csvData[$count]['is_in_stock']               = $value['status'];
        $csvData[$count]['manage_stock']              = 1;
        $csvData[$count]['use_config_manage_stock']   = 1;

        $csvData[$count]['model_size_desc']           = $tempData['model_text'];
        
        $csvData[$count]['status']                    = 1;
        $csvData[$count]['visibility']                = '"Catalog, Search"';
        $csvData[$count]['brand']                     = '';

        $csvData[$count]['categories']                = $categoryStr;

        $csvData[$count]['pack']                      = (isset($tempData['min_order'])?$tempData['min_order']:'Not Available');
        $csvData[$count]['fabric']                    = $tempData['fabric'];
        $csvData[$count]['made']                      = $made;

        $csvData[$count]['source']                    = 'lashowroom';
        $csvData[$count]['source_sku']                = $tempData['style_no'][0];

        $csvData[$count]['tax_class_id']              = 'None';

        
        $csvData[$count]['image']                     = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]));
        $csvData[$count]['small_image']               = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]));
        $csvData[$count]['thumbnail']                 = $value['id'].'_wl_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]));
        $csvData[$count]['image_label']               = $tempData['description'];
        $csvData[$count]['small_image_label']         = $tempData['description'];
        $csvData[$count]['thumbnail_label']           = $tempData['description'];

        $csvData[$count]['media_gallery']             = $imgStr;

        $csvData[$count]['auctioninc_product_length'] = $configLength;
        $csvData[$count]['auctioninc_product_width']  = $configWidth;
        $csvData[$count]['auctioninc_product_height'] = $configHeight;
        $csvData[$count]['auctioninc_calc_method']    = 'C';

        $csvData[$count]['attribute_set']             = 'Default';
        $csvData[$count]['configurable_attributes']   = 'leggingscolor';
        
        $csvData[$count]['leggingspacksize']          = $tempData['pack_size'];//implode(",", $packSizeArray[0]);
        $csvData[$count]['leggingspackqty']           = $tempData['pack_qty'];//implode(",", $packQtyArray[0]);
        $csvData[$count]['prod_url']                  = $value['url'];
        $csvData[$count++]['leggingscolor']            = '';
    }

}

fputcsv($fp, array_keys($csvData[0]));
foreach ($csvData as $data) {
   fputcsv($fp, $data);
}

fclose($fp);
