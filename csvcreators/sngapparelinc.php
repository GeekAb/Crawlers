<?php 

	// Include config and initiate
	include_once __DIR__ . '/../scripts/config/default_config.php';
	includeMyFiles();

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

    	// print_r($tempData);exit;

    	$csvData[$count]['store'] = 'default';
    	$csvData[$count]['sku'] = 'WL'.$value['category'].'#'.$value['id'];
    	// $csvData[$count]['in_stock'] = ;

    	$csvData[$count]['name'] = $tempData['title'];
    	$csvData[$count]['price'] = $tempData['price'];
    	$csvData[$count]['qty'] = 1; /*TODO : Check what QTY will be*/

    	$csvData[$count]['short_description'] = $tempData['title'].' - '.$tempData['price'];
    	$csvData[$count]['description'] = 'WL '.$value['category'].' '.$tempData['title'].' at $'.$tempData['price'];

    	$csvData[$count]['meta_title'] = $tempData['title'];
    	$csvData[$count]['meta_description'] = $tempData['title'].' - '.$tempData['price'];

    	$csvData[$count]['attribute_set'] = 'Default';
    	$csvData[$count]['pack_size'] = $tempData['pack'];

    	$csvData[$count]['categories'] = $value['category'].'/'.$value['subcategory'];

    	$csvData[$count]['image'] = $tempData['images'][0]['large'];
    	$csvData[$count]['small_image'] = $tempData['images'][0]['small'];
    	$csvData[$count]['thumbnail'] = $tempData['images'][0]['small'];
    	$csvData[$count]['image_label'] = '';
    	$csvData[$count]['small_image_label'] = '';
    	$csvData[$count]['thumbnail_label'] = '';
    	$csvData[$count++]['media_gallery'] = '';
    }

    // Writing Header
    fputcsv($fp, array_keys($csvData[0]));

    foreach ($csvData as $data) {
	    fputcsv($fp, $data);
	}

	fclose($fp);

    



?>

