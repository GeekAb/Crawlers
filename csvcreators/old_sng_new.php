<?php 

// Include config and initiate
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';
include_once __DIR__ . '/common.php';

// Get Database
$db = new Db();

$data = $db->query("SELECT * from products_data WHERE source='sngapparelinc'");

$tempData = array();
$csvData = array();
$count = 0;

$colorSet = array();
$sizeArray = array();
$packArray = array();

$fp = fopen("output.csv", "a+");

foreach ($data as $value) {

    $imgStr = '';
    $sizeArray = array();
    $packArray = array();
    
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
                array_push($sizeArray, 's/m');
                $packArray['s/m'] = $cdata['size'][$k];
            }
            else if($k == 'ml') {
                array_push($sizeArray, 'm/l');
                $packArray['m/l'] = $cdata['size'][$k];
            }
            else if($k == 'lxl') {
                array_push($sizeArray, 'l/xl');
                $packArray['l/xl'] = $cdata['size'][$k];
            }
            else if($k == 'xlxxl') {
                array_push($sizeArray, 'xl/xxl');
                $packArray['xl/xxl'] = $cdata['size'][$k];
            }
            else if($k == 'xxlxxxl') {
                array_push($sizeArray, 'xxl/xxxl');
                $packArray['xxl/xxxl'] = $cdata['size'][$k];
            }
            else if($k == 'xxxlxxxxl') {
                array_push($sizeArray, 'xxxl/xxxxl');
                $packArray['xxxl/xxxxl'] = $cdata['size'][$k];
            } else {
                array_push($sizeArray, $k);
                $packArray[$k] = $cdata['size'][$k];
            }
        }
    }

    // If not a valid category
    if(strlen($categoryStr) == 0)
        continue;

    foreach ($tempData['images'] as $img) {
        $imgMain = str_replace(array(",", '\'', '"' ), "", $img['large']);
        $imgStr .= $value['id'].'_wl_'.basename($imgMain).";";
        download_remote_file_with_curl(trim($imgMain), $value['id']);

        if(isset($img['small'])) {
            $tempimg = str_replace(array(",", '\'', '"' ), "", $img['small']);
        }
    }

    /*Create simple Products*/
    foreach ($tempData['color_size'] as $color) {

        $csvData[$count]['sku']                       = 'WL#'.$value['id'].'#'.$color['color'];
        $csvData[$count]['type']                      = 'simple';
        $csvData[$count]['store']                     = 'default';

        $csvData[$count]['name']                      = $tempData['title'].' '.$color['color'];
        $csvData[$count]['description']               = $tempData['title'];
        $csvData[$count]['short_description']         = $tempData['title'];
        $csvData[$count]['price']                     = getSalePrice($tempData['price']);
        $csvData[$count]['qty']                       = 1;
        $csvData[$count]['weight']                    = getWeight($value['category'],$value['subcategory']);

        $csvData[$count]['is_in_stock']               = 1;
        $csvData[$count]['manage_stock']              = 1;
        $csvData[$count]['use_config_manage_stock']   = 1;

        $csvData[$count]['status']                    = 1;
        $csvData[$count]['visibility']                = '"Catalog, Search"';
        $csvData[$count]['brand']                     = '';

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

        $csvData[$count]['media_gallery']             = $imgStr;

        $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
        $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
        $csvData[$count]['auctioninc_product_height'] = $dimension['height'];

        $csvData[$count]['attribute_set']             = 'Default';
        $csvData[$count]['configurable_attributes']   = 'leggingscolor';

        /*Setting size*/
        $csvData[$count]['leggingspacksize']          = implode(",", array_keys($packArray));
        $csvData[$count]['leggingspackqty']           = implode(",", $packArray);
        $csvData[$count++]['leggingscolor']           = $color['color'];

    }

    /*Config Prodcut Price*/
    $totalQty = 0;
    foreach ($packArray as $q) {
        $totalQty += $q;
    }

    // Config Price
    $configPrice = getSalePrice($tempData['price']) * $totalQty;
    $configLength = $dimension['length'];
    $configWidth = $dimension['width'];
    $configHeight = $dimension['height'];

    $configWeight = getWeight($value['category'],$value['subcategory']) * $totalQty;


    /*Create configurable Product*/
    $csvData[$count]['sku']                       = 'WL#'.$value['id'];
    $csvData[$count]['type']                      = 'configurable';
    $csvData[$count]['store']                     = 'default';

    $csvData[$count]['name']                      = $tempData['title'];
    $csvData[$count]['description']               = $tempData['title'];
    $csvData[$count]['short_description']         = $tempData['title'];
    $csvData[$count]['price']                     = $configPrice;
    $csvData[$count]['qty']                       = 1;
    $csvData[$count]['weight']                    = $configWeight;
    
    $csvData[$count]['is_in_stock']               = 1;
    $csvData[$count]['manage_stock']              = 1;
    $csvData[$count]['use_config_manage_stock']   = 1;
    
    $csvData[$count]['status']                    = 1;
    $csvData[$count]['visibility']                = '"Catalog, Search"';
    $csvData[$count]['brand']                     = '';

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

    $csvData[$count]['media_gallery']             = $imgStr;

    $csvData[$count]['auctioninc_product_length'] = $configLength;
    $csvData[$count]['auctioninc_product_width']  = $configWidth;
    $csvData[$count]['auctioninc_product_height'] = $configHeight;

    $csvData[$count]['attribute_set']             = 'Default';
    $csvData[$count]['configurable_attributes']   = 'leggingscolor';
    
    $csvData[$count]['leggingspacksize']          = implode(",", array_keys($packArray));
    $csvData[$count]['leggingspackqty']           = implode(",", $packArray);
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


