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
    	$csvData[$count]['weight'] = 0;
    	
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
    	$csvData[$count]['media_gallery'] = '';
    	
    	$csvData[$count]['made'] = '';
    	$csvData[$count]['fabric'] = '';
    	$csvData[$count][''] = '';
    }

    // Writing Header
//    fputcsv($fp, array_keys($csvData[0]));
exit;
    foreach ($csvData as $data) {
	    fputcsv($fp, $data);
	}

	fclose($fp);

    

function flatenData($data) {
      $tempData = json_decode($data['data'],TRUE);

    	// print_r($tempData);exit;

    	$csvData[$count]['store'] = 'default';
    	$csvData[$count]['sku'] = 'WL'.$value['category'].'#'.$value['id'];
    	// $csvData[$count]['in_stock'] = ;

    	$csvData[$count]['name'] = $tempData['title'];
    	$csvData[$count]['price'] = $tempData['price'];
    	$csvData[$count]['qty'] = 1; /*TODO : Check what QTY will be*/
    	$csvData[$count]['weight'] = 0;
    	
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
    	$csvData[$count]['media_gallery'] = '';
    	
    	$csvData[$count]['made'] = '';
    	$csvData[$count]['fabric'] = '';
}

?>


{"images":[
  {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8059blk_2.jpg',"
  ,"large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8059blk_2.jpg'"},
  {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ss8059_1_2.jpg',"
  ,"large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ss8059_1_2.jpg'"}
  ],

"title":"Wholesale Womens Plus Size Faux Leather Leggings with Studs",
"item_no":"SSP8059",
"size":"ONE SIZE (STRETCH XL TO 4XL)",
"pack":"6PCS\/PREPACK",
"fabric":"90% POLYESTER\r\n10% SPANDEX",
"made":"CHINA",
"price":"5.50",
"color_size":[{"color":"Black","size":{"onesize":"6"}}]}


{"images":[{
    "small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_d_1.jpg',",
    "large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_d_1.jpg'"},
    {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_d_0.jpg',",
    "large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_d_0.jpg'"},
    {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_l_0.jpg',",
    "large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_l_0.jpg'"},
    {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_d_2.jpg',",
    "large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_black_d_2.jpg'"},
    {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_red_l_1.jpg',",
    "large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_red_l_1.jpg'"},
    {"small":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/thumbnail\/320x480\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_grey_l_2.jpg',",
    "large":" 'http:\/\/sngapparelinc.com\/media\/catalog\/product\/cache\/1\/image\/9df78eab33525d08d6e5fb8d27136e95\/s\/s\/ssp8056_grey_l_2.jpg'"}],
    
"title":"Wholesale Womens Plus Size Faux Leather Leggings with Zippers",
"item_no":"SSP8056",
"size":" 1X\/2X 3X\/4X",
"pack":"6PCS\/PREPACK",
"fabric":"92% POLYESTER\r\n8% SPANDEX",
"made":"CHINA",
"price":"5.50",
"color_size":[
        {"color":"Burgundy","size":{"xlxxl":"3","xxxlxxxxl":"3"}},
        {"color":"Black","size":{"xlxxl":"3","xxxlxxxxl":"3"}},
        {"color":"Charcoal","size":{"xlxxl":"3","xxxlxxxxl":"3"}}]}











  