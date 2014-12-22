<?php

	// Include config and initiate
	include_once __DIR__ . '/../config/default_config.php';
	includeMyFiles();

	// Initialize goutte
	$goutte = new Goutte\Client();

	$log = fopen(date("Y-m-d_H-i-s", time()).".log", "a");
    fwrite($log, "[START]\r\n");
    fwrite($log, "[STARTED AT]".time()."\r\n");


	// URL and EndPoints
	$baseURL = 'http://sngapparelinc.com/';
    $leggingsEndpoint = 'leggings.html?p=';
    $plusSizeEndpoint = 'plus-size-leggings-1215.html?p=';
    $winterEndPoint = 'jeggings.html?p=';
    $pantsEndpoint = 'pants.html?p=';
    $shortsEndpoint = 'shorts.html?p=';
    $kidsEndpoint = 'kids.html?p=';
    
    // Default Status code
    $status_code = 200;
    $count = 1;

    // Get Database
    $db = new Db();

    $productUrls = array();
    // Get master links
    $data = getLinkEndPoints($goutte, $baseURL . $leggingsEndpoint);

    if($data['status'] == 200) {
	    foreach ($data['urls'] as $key => $value) {
	    	foreach ($value[0] as $val) {
	    		
				// $insert   =  $db->query("INSERT INTO master_urls(url,source,category,subcategory,status) 
				// 							VALUES(:url,:source,:category,:subcategory,:status)", 
				// 							array(	"url" => $val[0],
				// 									"source" => "sngapparelinc",
				// 									"category" => $key,
				// 									"subcategory" => $val[1],
				// 									"status"=>1
				// 									));

				// Get Product URLS
		    	$productUrls = getLink($goutte, $val[0]);

		    	$sleep_time = rand((2 * 1000000), (3 * 1000000));
		    	echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
		    	usleep($sleep_time);

		    	foreach ($productUrls as $url) {

		    		// // Insert Product URLs
		    		// $db->query("INSERT INTO product_urls(url,source,status) 
		    		// 	VALUES(:url,:source,:status)", array("url"=>$url,"source"=>"sngapparelinc","status"=>1));

		    		// Get Data
		    		$data = getProductData($goutte, $url);
		    		$db->query("INSERT INTO products_data(url,source,data,category,subcategory,status) 
		    			VALUES(:url,:source,:data,:category,:subcategory,:status)", 
		    				array(	"url"=>$url,
		    						"source"=>"sngapparelinc",
		    						"data"=>json_encode($data),
		    						"category" => $key,
									"subcategory" => $val[1],
		    						"status"=>1
		    					));
		    	}
	    	}
	    }
	}

    function getUrlFromArray($data)
    {

    	if (!is_array($data)) {
	        // nothing to do if it's not an array
	        return array($data);
	    }

	    $result = array();
	    foreach ($data as $value) {
	        // explode the sub-array, and add the parts
	        $result = array_merge($result, getUrlFromArray($value));
	    }

	    return $result;
    }

    function getLinkEndPoints($goutte, $url)
    {
    	$crawler = $goutte->request('GET', $url);

    	$count = 0;
    	$urls = array();

	    $status_code = $goutte->getResponse()->getStatus();

		if($status_code == 200){

			// Plus size
			$domSelector = '//*[@id="left_nav"]/li[3]/ul';

			$urls['plussize'] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return array($node->attr('href'),$node->text());
		    	});
			});

			// Leggings
			$domSelector = '//*[@id="left_nav"]/li[4]/ul';

			$urls['leggings'] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return array($node->attr('href'),$node->text());
		    	});
			});

			// Winter
			$domSelector = '//*[@id="left_nav"]/li[5]/ul';

			$urls['winter'] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return array($node->attr('href'),$node->text());
		    	});
			});

			// Pants
			$domSelector = '//*[@id="left_nav"]/li[6]/ul';

			$urls['pants'] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
					return array($node->attr('href'),$node->text());
		    	});
			});

			// Shorts
			$domSelector = '//*[@id="left_nav"]/li[7]/ul';

			$urls['shorts'] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return array($node->attr('href'),$node->text());
		    	});
			});

			// kids
			$domSelector = '//*[@id="left_nav"]/li[9]/ul';

			$urls['kids'] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return array($node->attr('href'),$node->text());
		    	});
			});
		}

		return array('status' => $status_code , 'urls' => $urls);
    }


    function getLink($goutte, $url)
    {
    	$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

	    $products = array();

		if($status_code == 200){

			$domSelector = '//*[@class="products-grid"]/li/a';

			$products = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->attr('href');	
			});
		}

		return $products;
    }

    function getProductData($goutte, $url) {

    	$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

	    $data = array();

		if($status_code == 200){

			// Images
			$domSelector = '//*[@id="mycarousel"]/li/a';

			$data['images'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->attr('rel');
			});

			// Title 
			$domSelector = '//*[@id="product_addtocart_form"]/div[2]/div[1]/h1';
			$data['title'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->html();
			});

			// Item Desc 
			$domSelector = '//*[@id="product_addtocart_form"]/div[2]/div[2]/div';
			$data['item_desc'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->html();
			});

			// Item Price 
			$domSelector = '//*[@id="product_addtocart_form"]/div[2]/div[3]/p/span';
			$data['price'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->text();
			});

			// Color Options table
			$domSelector = '//*[@id="super-product-table"]/tbody/tr';
			$data['color_options'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->html();
			});
		}

		return processProductData($data);

    }

    function processProductData($data) {

    	$processedData = array();

    	foreach ($data as $key => $value) {
    		
    		if($key == 'images') {

    			$images = array();

    			$count = 0;

    			foreach ($value as $v) {

    				// Get Images
	    			$smallImgReg = '.*?smallimage:(.*?)largeimage.*?';
	    			$largeImgReg = '.*?largeimage:(.*?)}.*?';

	    			preg_match('|' . $smallImgReg . '|smi', $v, $match);

				    if 		(isset($match[1])) {	$images[$count]['small'] = $match[1];} 
				    else if (isset($match[0])) {    $images[$count]['small'] = $match[0];}

				    preg_match('|' . $largeImgReg . '|smi', $v, $match);

				    if 		(isset($match[1])) {	$images[$count]['large'] = $match[1];} 
				    else if (isset($match[0])) {    $images[$count]['large'] = $match[0];}

				    $count++;
    			}

    			$processedData['images'] = $images;

    		} else if($key == 'title') {
    			
    			$temp = explode('<br>', $value[0]);

    			$processedData['title'] = preg_replace('/[^a-zA-Z0-9_ ]/s', '', $temp[0]);
    			$processedData['item_no'] = preg_replace('/[^a-zA-Z0-9_ ]/s', '', $temp[1]);

    		} else if($key == 'item_desc') {
    			
    			// Get Size
    			$sizeReg = '.*?SIZE:(.*?)<br>.*?';

    			preg_match('|' . $sizeReg . '|smi', $value[0], $match);

			    if 		(isset($match[1])) {	$processedData['size'] = $match[1];} 
			    else if (isset($match[0])) {    $processedData['size'] = $match[0];}

			    // Get Package
    			$packReg = '.*?PACKAGE:(.*?)<br>.*?';

    			preg_match('|' . $packReg . '|smi', $value[0], $match);

			    if 		(isset($match[1])) {	$processedData['pack'] = $match[1];} 
			    else if (isset($match[0])) {    $processedData['pack'] = $match[0];}

			    // Get Fabric Type
    			$fabricReg = '.*?PACKAGE:.*?<br>(.*?)MADE.*?';

    			preg_match('|' . $fabricReg . '|smi', $value[0], $match);

			    if 		(isset($match[1])) {	$processedData['fabric'] = trim(preg_replace('/<br>/s','',$match[1]));} 
			    else if (isset($match[0])) {    $processedData['fabric'] = trim(preg_replace('/<br>/s','',$match[0]));}


			    // Get Made
    			$madeReg = '.*?MADE IN(.*?)$';

    			preg_match('|' . $madeReg . '|smi', $value[0], $match);

			    if 		(isset($match[1])) {	$processedData['made'] = trim($match[1]);} 
			    else if (isset($match[0])) {    $processedData['made'] = trim($match[0]);}


    		} else if($key == 'price') {
    			
    			$processedData['price'] = preg_replace('/[^0-9.]/s', '', $value[0]);

    		} else if($key == 'color_options') {

    			$colorSize = array();
    			$count = 0;
    			foreach ($value as $v) {

    				// Color
    				$colorReg = '<td>(.*?)</td>.*?';
	    			preg_match('|' . $colorReg . '|smi', $v, $match);
	    			if(isset($match[1]) && trim($match[1])!='Total') {	$colorSize[$count]['color'] = trim($match[1]);}

	    			$sizes = explode('<td class="size', $v);

	    			foreach ($sizes as $size) {

	    				if($size[0] == "_") {

	    					$sizeReg = '.*?_(.*?)">.*?';
	    					$sizeInvalidReg = '.*?_(.*?)style.*?">.*?';

	    					if(preg_match('|' . $sizeInvalidReg . '|smi', $size, $match) == 1)
	    						continue;
	    					else {
	    						preg_match('|' . $sizeReg . '|smi', $size, $match);
	    						
	    						$sizeCountReg = '_.*?">(.*?)</td>.*?';
	    						preg_match('|' . $sizeCountReg . '|smi', $size, $matchCount);

	    						if(isset($match[1])) {	$colorSize[$count]['size'][$match[1]] = trim($matchCount[1]);}
	    					} 
	    					
	    				}
	    			}
	    			$count++;	
    			}
    			$processedData['color_size'] = $colorSize;
    		}	
    	}

    	return $processedData;
    }

// <td>Burgundy</td>
// <td class="size_xlxxl">3</td>
// <td class="size_xlxxl_hide" style="display:none;">3</td>
// <td class="size_xxxlxxxxl">3</td>
// <td class="size_xxxlxxxxl_hide" style="display:none;">3</td>
// <td class="a-center">
// <td class="a-center subqty">0</td>
// <td class="a-center amount last">$0.00</td>


// <td>Black</td>
// <td class="size_onesize">6</td>
// <td class="size_onesize_hide" style="display:none;">6</td>
// <td class="a-center">
// <td class="a-center subqty">6</td>
// <td class="a-center amount last">$33.00</td>