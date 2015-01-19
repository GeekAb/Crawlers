<?php

	include_once __DIR__ . '/../goutte.phar';

	$goutte = new Goutte\Client();

	$url = 'https://www.lashowroom.com/login?previous=/wholesalefashionistas/browse/category/3/srd/small/70/1';

	$crawler = $goutte->request('GET', $url);
	$form = $crawler->selectButton('Log In')->form(); 

	// exit;
	$crawler = $goutte->submit($form, array(
	        'head_login_id' => 'wholesalefashionistas@lashowroom.com', 
	        'head_login_key' => 'uplp'
	));

	$crawlUrl = 'https://www.lashowroom.com/wholesalefashionistas/browse/category/3/srd/small/70/';

	// $count = 1;
	// $status = 1;

	// $urls = array();

	// while($status == 1){
	// 	// Selector string
	// 	$domSelector = '//*[@id="store_browse"]/div[3]/ul/li/div/a';

	// 	$countSelector = '//*[@id="store_browse"]/h1/text()';
		
	// 	$crawler = $goutte->request('GET', $crawlUrl.$count);

	// 	$pcount = $crawler->filterXPath($countSelector)->each(function ($node) {
	//     	return $node->text();
	// 	});

	// 	$tProducts = '';
	// 	foreach ($pcount as $value) {
	// 		if(trim($value) != '')
	// 			$tProducts = trim($value);
	// 	}

	// 	$tProducts = ereg_replace("[^0-9]", "", $tProducts );

	// 	$tempUrls = $crawler->filterXPath($domSelector)->each(function ($node) {
	//     	return $node->attr('href');
	// 	});

	// 	foreach ($tempUrls as $url) {
	// 		if($url != '#') {
	// 			array_push($urls, $url);
	// 		}
	// 	}

	// 	if(count($tempUrls) < 2 || (count($urls) >= $tProducts)) {
	// 		$status = 0;
	// 	}

	// 	$count++;
	// }

	// foreach($urls as $url) {
	$url = 'https://www.lashowroom.com/wholesalefashionistas/item/610';
		getProductData($goutte, $url);
		exit;
	// }


	function getProductData($goutte, $url){
		$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

	    $data = array();

		if($status_code == 200){

			// Master Images
			$domSelector = '//*[@id="store_item_detail_image"]';

			$data['masterImage'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->attr('src');
			});

			/*Model Text*/
			$domSelector = '//*[@id="store_item_detail_l"]/div[1]';
			$data['model_text'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	return $node->html();
			});

			/*Other Images*/
			$domSelector = '//*[@id="more_view_box"]/ul/li/a';

			$data['otherImages'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	
		    	return $node->attr('href');
		    	/*TODO : Add option to get thumbnail also*/
		    	// $domS = '//li/a';

		    	// return $node->filterXPath($domS)->each(function ($node) {
		    	// 	return array($node->attr('href'),$node->text());
		    	// });
			});

			/*Product Data*/
			$domSelector = '//*[@id="store_item_detail_r"]';

			$data['data'] = $crawler->filterXPath($domSelector)->each(function ($node) {
		    	
		    	$data = array();

		    	/*Style Number*/
		    	$domSelector = '//*[@id="store_item_description"]/h1';
		    	$data['style_no'] = $node->filterXPath($domSelector)->each(function ($node) {
			    	return $node->text();
				});

				/*Description*/
				$domSelector = '//*[@id="store_item_description"]/table';
				$data['description'] = $node->filterXPath($domSelector)->each(function ($node) {
			    	return $node->html();
				});

				/*Price Info*/
				$domSelector = '//*[@id="store_item_price"]';
				$data['priceInfo'] = $node->filterXPath($domSelector)->each(function ($node) {
			    	return $node->html();
				});

				/*Color Options*/
				$domSelector = '//*[@id="item_order_form"]/div[2]/table/tbody/tr/th/a';
				$data['colors'] = $node->filterXPath($domSelector)->each(function ($node) {
			    	return $node->text();
				});

				return $data;
			});
			// print_r($data);
			print_r(formatProductData($data));
			
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
				if(is_array($value[0]) && isset($value[0]))
					$val = $value[0];
				else
					$val = $value;

				$finalSet['model_text'] = $val;
			}

			/*Product Data*/
			if($key == 'data') {

				if(is_array($value[0]) && isset($value[0])) {

					foreach ($value[0] as $key => $val) {

						print_r($key.'---------');
						
						/*Get Style Number*/
						if($key == 'style_no') {

							if(is_array($val) && isset($val[0]))
								$finalSet['style_no'] = $val[0];
						}

						if($key == 'description') {
							/*Description*/
							echo "came description-----";

							print_r($val[0]);

							

							$descReg = '.*?<span class="b">Description:</span>(.*?)</td></tr>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

			    			print_r($match);
			    			exit;

						    if 		(isset($match[1])) {	$finalSet['description'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['description'] = $match[0];}


							/*Category*/
							/*Min. Order*/
							$orderReg = '.*?<span class="b">Minimum Order:</span>(.*?)</td></tr>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

						    if 		(isset($match[1])) {	$finalSet['min_order'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['min_order'] = $match[0];}

							/*Fabric*/
							$fabricReg = '.*?<span class="b">Fabric::</span>(.*?)</td></tr>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

						    if 		(isset($match[1])) {	$finalSet['fabric'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['fabric'] = $match[0];}

							/*Content*/
							$contentReg = '.*?<span class="b">Content:</span>(.*?)</td></tr>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

						    if 		(isset($match[1])) {	$finalSet['content'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['content'] = $match[0];}

							/*Made In*/
							$madeReg = '.*?<span class="b">Made In:</span>(.*?)</td></tr>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

						    if 		(isset($match[1])) {	$finalSet['made_in'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['made_in'] = $match[0];}

							/*Comments*/
							$commentsReg = '.*?<span class="b">Comments:</span>(.*?)</td></tr>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

						    if 		(isset($match[1])) {	$finalSet['comments'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['comments'] = $match[0];}
						}

						/*Price*/
						if($key == 'priceInfo') {

							/*Main Price*/
							$mainPriceReg = '.*?Our Price:</label>(.*?)</span>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

						    if 		(isset($match[1])) {	$finalSet['price'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['price'] = $match[0];}

							/*Prepack Price*/
							$prePackPriceReg = '.*?Prepack Price:</label>(.*?)</span>.*?';

			    			preg_match('|' . $descReg . '|smi', $val[0], $match);

						    if 		(isset($match[1])) {	$finalSet['pack_price'] = $match[1];} 
						    else if (isset($match[0])) {    $finalSet['pack_price'] = $match[0];}

							/*Units Per Pack*/
							// $unitsReg = '.*?<span class="b">Made In:</span>(.*?)</td></tr>.*?';

			    // 			preg_match('|' . $descReg . '|smi', $val[0], $match);

						 //    if 		(isset($match[1])) {	$finalSet['made_in'] = $match[1];} 
						 //    else if (isset($match[0])) {    $finalSet['made_in'] = $match[0];}
						}
						
						/*Colors*/
						if($key == 'colors') {

						}


					}
				}
			}
		}

		return $finalSet;
	}