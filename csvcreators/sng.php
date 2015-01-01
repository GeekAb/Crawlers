<?php 

	// Include config and initiate
// include_once __DIR__ . '/../scripts/config/default_config.php';
// includeMyFiles();
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';
include_once __DIR__ . '/common.php';

$categoryArray = array( 'Basic'             => array('Printed','Jeggings','Sublimation','Leather','Fitness'),
                        'Pants'             => array('Palazzo','Jeans','Harem','Shorts'),
                        'Plus Size'         => array(
                                                    'Plus Size Leggings'    => array('Basic Plus Size','Printed Plus Size','Jeggings Plus Size','Sublimation Plus Size','Leather Plus Size','Fitness Plus Size'),
                                                    'Plus Size Pants'    => array('Palazzo Plus Size','Jeans Plus Size','Harem Plus Size','Shorts Plus Size')
                                                )
                    );

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

    $categoryStr = getCategory($value['category'],$value['subcategory']);

    $dimension = getDimensions($value['category'],$value['subcategory']);


    // If not a valid category
    if(strlen($categoryStr) == 0)
        continue;

    $imgStr = '';
    foreach ($tempData['images'] as $img) {
        $imgMain = str_replace(array(",", '\'', '"' ), "", $img['large']);
        $imgStr .= 'sngapparelinc_'.basename($imgMain).";";

        //download_remote_file_with_curl(trim($imgMain));

        if(isset($img['small'])) {
            $tempimg = str_replace(array(",", '\'', '"' ), "", $img['small']);
            //download_remote_file_with_curl(trim($tempimg));
        }
            
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
        $csvData[$count]['weight']                    = getWeight($value['category'],$value['subcategory']);

        $csvData[$count]['is_in_stock']               = 1;
        $csvData[$count]['manage_stock']              = 1;
        $csvData[$count]['use_config_manage_stock']   = 1;

        $csvData[$count]['status']                    = 1;
        $csvData[$count]['visibility']                = '"Catalog, Search"';
        // $csvData[$count]['categories']                = $value['category'].'/'.$value['subcategory'];
        $csvData[$count]['categories']                = getCategory($value['category'],$value['subcategory']);

        $csvData[$count]['pack']                      = $tempData['pack'];
        $csvData[$count]['fabric']                    = $tempData['fabric'];
        $csvData[$count]['made']                      = $tempData['made'];

        $csvData[$count]['source']                    = 'sngapparelinc';
        $csvData[$count]['source_sku']                = $tempData['item_no'];

        $csvData[$count]['tax_class_id']              = 'None';

        $csvData[$count]['image']                     = 'sngapparelinc_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['large']));
        $csvData[$count]['small_image']               = 'sngapparelinc_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
        $csvData[$count]['thumbnail']                 = 'sngapparelinc_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
        $csvData[$count]['image_label']               = $tempData['title'];
        $csvData[$count]['small_image_label']         = $tempData['title'];
        $csvData[$count]['thumbnail_label']           = $tempData['title'];

        $csvData[$count]['media_gallery']             = $imgStr;

        $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
        $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
        $csvData[$count]['auctioninc_product_height'] = $dimension['height'];

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

    
    $csvData[$count]['image']                     = 'sngapparelinc_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['large']));
    $csvData[$count]['small_image']               = 'sngapparelinc_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
    $csvData[$count]['thumbnail']                 = 'sngapparelinc_'.basename(str_replace(array(",", '\'', '"' ), "", $tempData['images'][0]['small']));
    $csvData[$count]['image_label']               = $tempData['title'];
    $csvData[$count]['small_image_label']         = $tempData['title'];
    $csvData[$count]['thumbnail_label']           = $tempData['title'];

    $csvData[$count]['media_gallery']             = $imgStr;

    $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
    $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
    $csvData[$count]['auctioninc_product_height'] = $dimension['height'];

    $csvData[$count]['attribute_set']             = 'Default';
    $csvData[$count]['configurable_attributes']   = 'leggingscolor';
    $csvData[$count++]['leggingscolor']            = '';


}
// basename($fileUrl)
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


function download_remote_file_with_curl($fileUrl)
{
    echo $fileUrl.PHP_EOL;
    $saveTo = basename($fileUrl);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0); 
    curl_setopt($ch,CURLOPT_URL,$fileUrl); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $file_content = curl_exec($ch);
    curl_close($ch);

    if(!file_exists('/var/www/wholesaleleggings82/media/import/')) {
        mkdir ('/var/www/wholesaleleggings82/media/import/',0700, TRUE);
    }

    $downloaded_file = fopen('/var/www/wholesaleleggings82/media/import/sngapparelinc_'.$saveTo, 'w+');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);

}
