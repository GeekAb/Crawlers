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

	/*Master URL*/
	$crawlUrl = 'https://www.lashowroom.com/wholesalefashionistas/browse/all/1/srd/large/70/';

	$count = 1;
	$status = 1;

	$urls = array();

	while($status == 1){
		// Selector string
		$domSelector = '//*[@class="store-front-browse-gallery"]/ul/li/div/a';
		/*Count selector for page number*/
		$countSelector = '//*[@class="store-front-browse-title"]/text()';
		/*Main crawl , adding count to master*/
		$crawler = $goutte->request('GET', $crawlUrl.$count);
		/*Getting total products string*/
		$pcount = $crawler->filterXPath($countSelector)->each(function ($node) {
	    	return $node->text();
		});
		$tProducts = '';
		foreach ($pcount as $value) {
			if(trim($value) != '')
				$tProducts = trim($value);
		}
		/*Total products*/
		$tProducts = ereg_replace("[^0-9]", "", $tProducts );
		$tempUrls = $crawler->filterXPath($domSelector)->each(function ($node) {
	    	return $node->attr('href');
		});

		foreach ($tempUrls as $url) {
			if($url != '#') {
				array_push($urls, $url);
			}
		}

		if(count($tempUrls) < 2 || (count($urls) >= $tProducts)) {
			$status = 0;
		}

		$count++;
	}


	foreach($urls as $url) {
		
		$db->query("INSERT INTO product_urls(url, url_hash, source,status)
		    			VALUES(:url, :url_hash, :source, :status)
		    			ON DUPLICATE KEY UPDATE updated_on = NOW();",
		    			array(	"url"=>'https://www.lashowroom.com'.$url,
		    					"url_hash" => hash('ripemd160', 'https://www.lashowroom.com'.$url),
		    					"source"=>"lashowroom",
		    					"status"=>1
		    				));

		$product = getProductData($goutte, $url);

		$temp = $db->query("SELECT 1 FROM products_data WHERE url_hash = '".hash('ripemd160', 'https://www.lashowroom.com'.$url)."'");
		
		if(count($temp) == 0) {
			/*Insert Product*/
			$db->query("INSERT INTO products_data(url,url_hash,source,data,category,subcategory,status) 
		    			VALUES(:url,:url_hash,:source,:data,:category,:subcategory,:status);", 
		    				array(	"url"=>'https://www.lashowroom.com'.$url,
		    						"url_hash" => hash('ripemd160', 'https://www.lashowroom.com'.$url),
		    						"source"=>"lashowroom",
		    						"data"=>json_encode($product),
		    						"category" => $product['category'],
									"subcategory" => '',
		    						"status"=>1
		    					));
		} else {
			/*Insert Product*/
			$db->query("UPDATE products_data SET `data`= :data, `status`= :status,`updated_on`= NOW() WHERE url_hash = :url_hash;",
				array(
					"data"=>json_encode($product),
					"status"=>1,
					"url_hash" => hash('ripemd160', 'https://www.lashowroom.com'.$url)
				));

			echo "Updated".PHP_EOL;
		}

		goToSleep();
	}


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

				return $data;
			});

			$domSelector = '//*[@class="item-detail-main-content-details clearfix"]/div/div/h2';

			$result['style_no'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return trim($node->text());
			});

			if(isset($result['style_no'][0]) && trim($result['style_no'][0]) == '') {
				$domSelector = '//*[@class="item-detail-main-content-details clearfix"]/div/div/h3';

				$result['style_no'] = $crawler->filterXPath($domSelector)->each(function ($node) {
			    	return trim($node->text());
				});
			}

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
		$sleep_time = rand((1 * 1000000), (2 * 1000000));
    	echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
    	usleep($sleep_time);

    	return 1;
	}
