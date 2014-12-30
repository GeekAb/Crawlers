<?php 

	// Include config and initiate
// include_once __DIR__ . '/../scripts/config/default_config.php';
// includeMyFiles();
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';

	// Get Database
$db = new Db();

$data = $db->query("SELECT * from products_data WHERE source='sngapparelinc'");

$tempData = array();
$csvData = array();
$count = 0;

$colorSet = array();
$fp = fopen("output.csv", "a+");


foreach ($data as $value) {

    $tempData = json_decode($value['data'],TRUE);

    $imgStr = '';
    foreach ($tempData['images'] as $img) {
        $img = str_replace(array(",", '\'', '"' ), "", $img['large']);
        $imgStr .= $img.";";
    }


    foreach ($tempData['color_size'] as $color) {

        // $colorSet[] = $color['color'];

        $csvData[$count]['sku']                       = 'WL'.$value['category'].'#'.$value['id'].'#'.$color['color'];
        $csvData[$count]['type']                      = 'simple';
        $csvData[$count]['store']                     = 'default';

        $csvData[$count]['name']                      = $tempData['title'].' '.$color['color'];
        $csvData[$count]['description']               = $tempData['title'];
        $csvData[$count]['short_description']         = $tempData['title'];
        $csvData[$count]['price']                     = $tempData['price'];
        $csvData[$count]['qty']                       = 1;
        $csvData[$count]['weight']                    = 0;

        $csvData[$count]['is_in_stock']               = 1;
        $csvData[$count]['manage_stock']              = 1;
        $csvData[$count]['use_config_manage_stock']   = 1;

        $csvData[$count]['status']                    = 1;
        $csvData[$count]['visibility']                = '"Catalog, Search"';
        $csvData[$count]['categories']                = $value['category'].'/'.$value['subcategory'];

        $csvData[$count]['pack']                      = $tempData['pack'];
        $csvData[$count]['fabric']                    = $tempData['fabric'];
        $csvData[$count]['made']                      = $tempData['made'];

        $csvData[$count]['source']                    = 'sngapparelinc';
        $csvData[$count]['source_sku']                = $tempData['item_no'];

        $csvData[$count]['tax_class_id']              = 'None';

        $csvData[$count]['image']                     = str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['large']);
        $csvData[$count]['small_image']               = str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']);
        $csvData[$count]['thumbnail']                 = str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']);
        $csvData[$count]['image_label']               = $tempData['title'];
        $csvData[$count]['small_image_label']         = $tempData['title'];
        $csvData[$count]['thumbnail_label']           = $tempData['title'];

        $csvData[$count]['media_gallery']             = $imgStr;

        $csvData[$count]['attribute_set']             = 'Default';
        $csvData[$count]['configurable_attributes']   = 'leggingscolor';
        $csvData[$count++]['leggingscolor']            = $color['color'];
    }

    $csvData[$count]['sku']                       = 'WL'.$value['category'].'#'.$value['id'];
    $csvData[$count]['type']                      = 'configurable';
    $csvData[$count]['store']                     = 'default';

    $csvData[$count]['name']                      = $tempData['title'];
    $csvData[$count]['description']               = $tempData['title'];
    $csvData[$count]['short_description']         = $tempData['title'];
    $csvData[$count]['price']                     = $tempData['price'];
    $csvData[$count]['qty']                       = 1;
    $csvData[$count]['weight']                    = 0;
    
    $csvData[$count]['is_in_stock']               = 1;
    $csvData[$count]['manage_stock']              = 1;
    $csvData[$count]['use_config_manage_stock']   = 1;
    
    $csvData[$count]['status']                    = 1;
    $csvData[$count]['visibility']                = '"Catalog, Search"';
    $csvData[$count]['categories']                = $value['category'].'/'.$value['subcategory'];

    $csvData[$count]['pack']                      = $tempData['pack'];
    $csvData[$count]['fabric']                    = $tempData['fabric'];
    $csvData[$count]['made']                      = $tempData['made'];

    $csvData[$count]['source']                    = 'sngapparelinc';
    $csvData[$count]['source_sku']                = $tempData['item_no'];

    $csvData[$count]['tax_class_id']              = 'None';

    
    $csvData[$count]['image']                     = str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['large']);
    $csvData[$count]['small_image']               = str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']);
    $csvData[$count]['thumbnail']                 = str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']);
    $csvData[$count]['image_label']               = $tempData['title'];
    $csvData[$count]['small_image_label']         = $tempData['title'];
    $csvData[$count]['thumbnail_label']           = $tempData['title'];

    $csvData[$count]['media_gallery']             = $imgStr;

    $csvData[$count]['attribute_set']             = 'Default';
    $csvData[$count]['configurable_attributes']   = 'leggingscolor';
    $csvData[$count++]['leggingscolor']            = '';
}

// echo "<pre>";
// print_r(($csvData));
// echo "</pre>";

// exit;
//   // Writing Header
fputcsv($fp, array_keys($csvData[0]));
foreach ($csvData as $data) {
   fputcsv($fp, $data);
}

fclose($fp);
