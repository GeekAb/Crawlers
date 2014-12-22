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
	$baseURL = 'http://numberonewholesales.com/';
	$endPoints['leggings'] = 'leggings-c-1067.html';
	$endPoints['pants'] = 'palazzo-pants-c-1112.html';
    
    // Default Status code
    $status_code = 200;
    $count = 1;

    $productUrls = array();

    // foreach ($endPoints as $key => $value) {

    // 	$productUrls[] = getLink($goutte, $baseURL.$value);
    // 	print_r($productUrls);
    // 	$sleep_time = rand((3 * 1000000), (4 * 1000000));
    // 	echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
    // 	usleep($sleep_time);
    // }

    // $urls = getUrlFromArray($productUrls);

    // foreach ($urls as $url) {
	$url = 'http://numberonewholesales.com/fleece-inside-ankle-leggings-p-42.html?cPath=1067';
    	$data = getProductData($goutte, $url);
    	exit;
    // }

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

    function getLink($goutte, $url)
    {
    	$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

	    $urls = array();

		if($status_code == 200){

			// Get total Pages
			$selector = '//table/tr/td/b[3]';

		    $totalProds = $crawler->filterXPath($selector)->each(function ($node) {
			    return $node->text();
			});

			$productCount = $totalProds[0];

			// Get products data
			$products = getUrlArray($crawler);

			array_push($urls, $products);
			$fetched = count($products);
			// If multiple pages are there
			$pages = $productCount / $fetched;

			if($products % $fetched != 0) 
				$pages += 1;
			echo "page 1\n";
			if($pages > 1) 
				for ($page=2; $page<=$pages ; $page++) {

					echo "page $page \n";
					$crawler = $goutte->request('GET', $url.'?page='.$page);

					$status_code = $goutte->getResponse()->getStatus();
					if($status_code == 200){
						$products = getUrlArray($crawler);
						array_push($urls, $products);
					}
				}

			
			echo count($urls);
		}

		return $urls;
    }

    function getUrlArray($crawler)
    {
    	// Parsing first page
		$domSelector = '//table[@class="productListing"]/tr/td';

    	return $crawler->filterXPath($domSelector)->each(function ($node) {

			$domS = '//table/tr/td';
			$url = $node->filterXPath($domS)->each(function ($node) {
	    	
	    		$linkData = $node->attr('onclick');
	    		$regex = "window.location='(.*?)'";

    			preg_match('|' . $regex . '|smi', $linkData, $match);

    			if 		(isset($match[1])) {	return $match[1];} 
    			return 0;
	    	});		    	

	    	return $url[0];

		});
    }

    function getProductData($goutte, $url) {

    	$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

	    $data = array();

		if($status_code == 200){

			// Get data blocks 
			$domSelector = '//*[@class="productInfoTable"]';

			$data = $crawler->filterXPath($domSelector)->each(function ($node) {

				$attribs = array();
		    	
		    	// Get title
		    	$titleReg = '.*?<td.*?>Name.*?<td.*?>(.*?)</td>.*?';
		    	preg_match('|' . $titleReg . '|smi', $node->html(), $match);
		    	if(isset($match[1]))
			    	$attribs['title'] = $match[1];

			    // Get style
			    $styleReg = '.*?<td.*?>Style.*?<td.*?><span.*?>(.*?)</span>.*?</td>.*?';
		    	preg_match('|' . $styleReg . '|smi', $node->html(), $match);
		    	if(isset($match[1]))
			    	$attribs['style'] = $match[1];

			    // Get price
			    $priceReg = '.*?<td.*?>Price.*?<td.*?>(.*?)</td>.*?';
		    	preg_match('|' . $priceReg . '|smi', $node->html(), $match);
		    	if(isset($match[1]))
			    	$attribs['price'] = $match[1];

			    // Get stock
			    $stockReg = '.*?<td.*?>Stock.*?<td.*?>(.*?)</td>.*?';
		    	preg_match('|' . $stockReg . '|smi', $node->html(), $match);
		    	if(isset($match[1]))
			    	$attribs['stock'] = $match[1];

			    // Get Product Images
			    $domS = '//table/tr/td';

		    	$node->filterXPath($domS)->each(function ($node) {
		    		print_r($node);
		    	});

			    print_r($attribs);
			});

			exit;
		}

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