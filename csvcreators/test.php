<?php 

	// Include config and initiate
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';

	// Get Database
$db = new Db();

$data = $db->query("SELECT * from products_data WHERE source='sngapparelinc'");

$tempData = array();
$csvData = array();
$count = 0;

$fp = fopen("output.csv", "a+");

foreach ($data as $value) {

    				// [id] => 238
       //      [url] => http://sngapparelinc.com/kids/pants/wholesale-kids-semi-harem-pants-geo-tribal.html
       //      [source] => sngapparelinc
       //      [category] => kids
       //      [subcategory] => Pants
       //      [data] => {"images":[
       //      {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/k\/s\/ksh277.jpg',","large":" 
       //      'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/k\/s\/ksh277.jpg'"}],
       //      "title":"Wholesale Kids Semi Harem Pants Geo Tribal",
       //      "item_no":"KSH277","size":"S (4-6) M (6-8) L (8-10)",
       //      "pack":"6PCS\/PREPACK",
       //      "fabric":"92% POLYESTER\r\n8% SPANDEX",
       //      "made":"CHINA",
       //      "price":"3.50",
       //      "color_size":[{"color":"Just as picture","size":{"s":"2","m":"2","l":"2"}}]}
       //      [status] => 1

    $tempData = json_decode($value['data'],TRUE);

    $simpleProdSet = array();
    $count = 0;

    foreach ($tempData['color_size'] as $color) {

        $simpleProdSet[$count]['sku']                       = 'WL'.$value['category'].'#'.$value['id'].'#'.$color['color'];
        $simpleProdSet[$count]['attribute_set']             = '';
        $simpleProdSet[$count]['type']                      = 'simple';
        $simpleProdSet[$count]['store']                     = 'default';
        $simpleProdSet[$count]['configurable_attributes']   = '';
        $simpleProdSet[$count]['config_color']              = $color['color'];
        $simpleProdSet[$count]['name']                      = $tempData['title'];
        $simpleProdSet[$count]['description']               = '';
        $simpleProdSet[$count]['price']                     = $tempData['price'];
        $simpleProdSet[$count]['qty']                       = 1;
        $simpleProdSet[$count]['is_in_stock']               = 1;
        $simpleProdSet[$count]['manage_stock']              = 1;
        $simpleProdSet[$count]['use_config_manage_stock']   = 1;
        $simpleProdSet[$count]['status']                    = 1;
        $simpleProdSet[$count]['visibility']                = 'Catalog, Search';
        $simpleProdSet[$count]['weight']                    = 0;
        $simpleProdSet[$count]['categories']                = $value['category'].'/'.$value['subcategory'];

        $simpleProdSet[$count]['pack']                      = $tempData['pack'];
        $simpleProdSet[$count]['fabric']                    = $tempData['fabric'];
        $simpleProdSet[$count]['made']                      = $tempData['made'];

        $simpleProdSet[$count]['tax_class_id']              = 'None';
        $simpleProdSet[$count]['thumbnail']                 = '';
        $simpleProdSet[$count]['small_image']               = '';
        $simpleProdSet[$count]['image']                     = '';
        $simpleProdSet[$count++]['media_gallery']           = '';
    }

    $simpleProdSet[$count]['sku']                       = 'WL'.$value['category'].'#'.$value['id'];
    $simpleProdSet[$count]['attribute_set']             = '';
    $simpleProdSet[$count]['type']                      = 'configurable';
    $simpleProdSet[$count]['store']                     = 'default';
    $simpleProdSet[$count]['configurable_attributes']   = '';
    $simpleProdSet[$count]['config_color']              = '';
    $simpleProdSet[$count]['name']                      = $tempData['title'].' '.$color['color'];
    $simpleProdSet[$count]['description']               = '';
    $simpleProdSet[$count]['price']                     = $tempData['price'];
    $simpleProdSet[$count]['qty']                       = 1;
    $simpleProdSet[$count]['is_in_stock']               = 1;
    $simpleProdSet[$count]['manage_stock']              = 1;
    $simpleProdSet[$count]['use_config_manage_stock']   = 1;
    $simpleProdSet[$count]['status']                    = 1;
    $simpleProdSet[$count]['visibility']                = 'Catalog, Search';
    $simpleProdSet[$count]['weight']                    = 0;
    $simpleProdSet[$count]['categories']                = $value['category'].'/'.$value['subcategory'];

    $simpleProdSet[$count]['tax_class_id']              = 'None';
    $simpleProdSet[$count]['thumbnail']                 = '';
    $simpleProdSet[$count]['small_image']               = '';
    $simpleProdSet[$count]['image']                     = '';
    $simpleProdSet[$count]['media_gallery']             = '';

    echo "<pre>";
    print_r($simpleProdSet);
    echo "</pre>";

}




?>











