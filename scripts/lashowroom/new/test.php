<?php

	// Include config and initiate
	include_once __DIR__ . '/../config/default_config.php';
	includeMyFiles();

	$goutte = new Goutte\Client();

	// Get Database
    $db = new Db();

    $db->query("UPDATE products_data SET status=0 WHERE source='lashowroom'");

	$url = 'https://www.lashowroom.com/login?previous=/wholesalefashionistas/browse/category/3/srd/small/70/1';

	$crawler = $goutte->request('GET', $url);
	$form = $crawler->selectButton('Log In')->form(); 

	// exit;
	$crawler = $goutte->submit($form, array(
	        'login_id' => 'wholesalefashionistas@lashowroom.com', 
	        'login_key' => 'uplp'
	));

	

	

		$product = getProductData($goutte, 'https://www.lashowroom.com/wholesalefashionistas/item/1215');

		print_r($product);
		

	function getProductData($goutte, $url){
		$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

	    $data = array();
	    $result = array();

		if($status_code == 200){

			$filterNav = '//*[@class="item-detail-header"]/p/a';

			$catStruct = $crawler->filterXPath($filterNav)->each(function ($node) {
		    	return $node->html();
			});

			foreach ($catStruct as $key => $catVal) {
				$catStruct[$key] = trim(str_replace(' ', '', $catVal));
			}

			$result['category'] = $catStruct[1];
			$result['subCategory'] = $catStruct[2];

			// Master Images
			$domSelector = '//*[@id="item_detail_image_display"]';

			$data['masterImage'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->attr('src');
			});

			/*Model Text*/
			$domSelector = '//*[@class="item-detail-main-content-image-thumbnail-disclaimer"]/p';
			$data['model_text'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->html();
			});

			/*Other Images*/
			$domSelector = '//*[@class="item-detail-main-content-image-thumbnail"]/ul/li/a';

			$data['otherImages'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	
		    	return $node->attr('href');
			});

			/*Product Data*/
			$domSelector = '//*[@class="item-detail-main-content-details clearfix"]';

			$data['data'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	
		    	$data = array();

		    	$data['otherData'] = $node->html();

		    	/*Style Number*/
		    	$domSelector = '//h2';
		    	$data['style_no'] = $node->filterXPath($domSelector)->each(function ($node) {
			    	return trim($node->text());
				});
				
				return $data;
			});

			$result = array_merge($result,formatProductData($data));
			return $result;
		}
	}

	function formatProductData($data)
	{
		$finalSet = array();
		$images = array();

		foreach ($data as $key => $value) {

			/*Image Set*/
			if($key == 'masterImage') {

				array_push($images, $value[0]);

				foreach ($data['otherImages'] as $img) {
					array_push($images, $img);					
				}

				$finalSet['images'] = $images;
			}

			/*Model Text*/
			if($key == 'model_text') {
				$val = '';
				
				if(isset($value[1]) && trim($value[1]) != '')
					$finalSet['model_text'] = trim($value[1]);

				else 
					$finalSet['model_text'] = 'Not Available';
			}

			if($key == 'data') {
				$d = $value[0]['otherData'];

				/*Main Price*/
				$mainPriceReg = '.*?Our Price:</strong>(.*?)</p>.*?';

    			preg_match('|' . $mainPriceReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['price'] = trim($match[1]);}
			    else if (isset($match[0])) {    $finalSet['price'] = trim($match[0]);}

				/*Prepack Price*/
				$prePackPriceReg = '.*?Prepack Price:</strong>(.*?)</p>.*?';

    			preg_match('|' . $prePackPriceReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['pack_price'] = trim($match[1]);}
			    else if (isset($match[0])) {    $finalSet['pack_price'] = trim($match[0]);}

				/*Sizes Per Pack*/
				$packSizeReg = '.*?Sizes Per Prepack:.*?</span>(.*?)</p>';

    			preg_match('|' . $packSizeReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['pack_size'] = $match[1];} 
			    else if (isset($match[0])) {    $finalSet['pack_size'] = $match[0];}

			    /*Units per pack*/
			    $packSizeReg = '.*?Units Per Prepack:</strong>(.*?)</p>';

    			preg_match('|' . $packSizeReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['pack_qty'] = $match[1];} 
			    else if (isset($match[0])) {    $finalSet['pack_qty'] = $match[0];}

			    /*Product color*/
			    $colorReg = '.*?item-detail-color-preview.*?>(.*?)</a>.*?';

    			preg_match_all('|' . $colorReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['color'] = $match[1];}
			    else if (isset($match[0])) {    $finalSet['color'] = trim($match[0]);}
				
				/*Description*/
				$descReg = '.*?<strong>Description:</strong>(.*?)</p>.*?';

    			preg_match('|' . $descReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['description'] = trim($match[1]);}
			    else if (isset($match[0])) {    $finalSet['description'] = trim($match[0]);}
				

				/*Fabric*/
				$fabricReg = '.*?<strong>Fabric:.*?span>(.*?)</span>.*?';

    			preg_match('|' . $fabricReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['fabric'] = trim($match[1]);}
			    else if (isset($match[0])) {    $finalSet['fabric'] = trim($match[0]);}

			    /*Content*/
				$contentReg = '.*?<strong>Content:</strong>(.*?)</p>.*?';

    			preg_match('|' . $contentReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['content'] = trim($match[1]);}
			    else if (isset($match[0])) {    $finalSet['content'] = trim($match[0]);}

			    /*Made In*/
				$madeReg = '.*?<strong>Made In:</strong>(.*?)</p>.*?';

    			preg_match('|' . $madeReg . '|smi', $d, $match);

			    if 		(isset($match[1])) {	$finalSet['made_in'] = trim($match[1]);}
			    else if (isset($match[0])) {    $finalSet['made_in'] = trim($match[0]);}
			}
		}

		return $finalSet;
	}

	function goToSleep()
	{
		$sleep_time = rand((2 * 1000000), (3 * 1000000));
    	echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
    	usleep($sleep_time);

    	return 1;
	}
