	<?php

	include_once __DIR__ . '/../goutte.phar';

	$goutte = new Goutte\Client();

	// $baseURL = 'http://www.numberonewholesales.com/';
 //    $leggingsEndpoint = 'leggings-c-1067.html';
 //    $palazzoEndpoint = 'palazzo-pants-c-1112.html';
    

 //    $crawler = $goutte->request('GET', $baseURL . $leggingsEndpoint);

 //    $status_code = $goutte->getResponse()->getStatus();
	// echo $status_code;

 //    // echo $crawler->html();

	// if($status_code == 200){

	// 	$domSelector = '//*[@class="productListing"]';


	// 	$crawler->filterXPath($domSelector)->each(function ($node) {
	//     	print $node->text()."\n";
	// 	});

	// 	// $domSelector = '//*[@id="product-54391"]/div/div[2]';

	// 	// $crawler->filterXPath($domSelector)->each(function ($node) {
	//  //    	print_r($node);
	//  //    	echo "\n";
	// 	// });
	// }

// 	wholesalefashionistas@lashowroom.com
// Pass: uplp

	// 

	$url = 'https://www.lashowroom.com/login?previous=/wholesalefashionistas/browse/category/3/srd/small/70/1';

	$crawler = $goutte->request('GET', $url);
	$form = $crawler->selectButton('Log In')->form(); 

	// exit;
	$crawler = $goutte->submit($form, array(
	        'head_login_id' => 'wholesalefashionistas@lashowroom.com', 
	        'head_login_key' => 'uplp'
	));

	$crawlUrl = 'https://www.lashowroom.com/wholesalefashionistas/browse/category/3/srd/small/70/';

	$count = 1;
	$status = 1;

	$urls = array();

	while($status == 1){
		// Selector string
		$domSelector = '//*[@id="store_browse"]/div[3]/ul/li/div/a';

		$countSelector = '//*[@id="store_browse"]/h1/text()';
		
		$crawler = $goutte->request('GET', $crawlUrl.$count);

		$pcount = $crawler->filterXPath($countSelector)->each(function ($node) {
	    	return $node->text();
		});

		$tProducts = '';
		foreach ($pcount as $value) {
			if(trim($value) != '')
				$tProducts = trim($value);
		}

		$tProducts = ereg_replace("[^0-9]", "", $tProducts );

		$tempUrls = $crawler->filterXPath($domSelector)->each(function ($node) {
	    	return $node->attr('href');
		});

		foreach ($tempUrls as $url) {
			if($url != '#') {
				array_push($urls, $url);
			}
		}

		echo count($urls);

		if(count($tempUrls) < 2 || (count($urls) >= $tProducts)) {
			$status = 0;
		}

		$count++;
	}
	
	print_r($urls);

	exit;
	echo $crawler->html();