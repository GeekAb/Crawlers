<?php 

// Include config and initiate
include_once __DIR__ . '/../scripts/config/default_config.php';
include_once __DIR__ . '/../scripts/config/database.php';
include_once __DIR__ . '/../scripts/config/log.php';
include_once __DIR__ . '/common.php';
include_once __DIR__ . '/master-fn.php';

// Get Database
$db = new Db();

/*$data = $db->query("SELECT * from products_data WHERE source='sngapparelinc' AND updated_on >= CURDATE()");*/
$data = $db->query("SELECT * from products_data WHERE source='numberonewholesales'");

$tempData = array();
$csvData = array();
$count = 0;

$colorSet = array();
$sizeArray = array();
$packArray = array();

// Remove file if exist
if (file_exists('no1.csv'))
    unlink('no1.csv');

$fp = fopen("no1.csv", "a+");

foreach ($data as $value) {

    $imgStr = '';
    $sizeArray = array();
    $packArray = array();

    $category = $value['category'];

    if($category == 'active-wear')
        $category = 'activewear';

    $subcategory = $value['subcategory'];

    $tempData = json_decode($value['data'],TRUE);

    /*Get Category String*/
    $category       = getCategory($value['category'],$value['subcategory'],$tempData['title'][0]);
    $subCategory    = getSubCategory($value['category'],$value['subcategory'],$tempData['title'][0]);

    $categoryStr = getCategoryString($category, $subCategory);

    /*Get dimension*/
    $dimension = getDimensions($category,$subcategory);

    $csvData[$count]['category'] = $category;
    $csvData[$count]['subCategory'] = $subCategory;
    $csvData[$count]['category_str'] = $categoryStr;
    $csvData[$count]['price']                     = getSalePrice(str_replace('$', '', trim($tempData['price'])));
    $csvData[$count]['weight']                    = $dimension['weight'];
    $csvData[$count]['auctioninc_product_length'] = $dimension['length'];
    $csvData[$count]['auctioninc_product_width']  = $dimension['width'];
    $csvData[$count]['auctioninc_product_height'] = $dimension['height'];
    $csvData[$count++]['prod_url']                  = $value['url'];

}

// Writing Header
fputcsv($fp, array_keys($csvData[0]));
foreach ($csvData as $data) {
   fputcsv($fp, $data);
}

fclose($fp);



