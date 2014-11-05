<?php

	include_once __DIR__ . '/../goutte.phar';

	$goutte = new Goutte\Client();

	$baseURL = 'http://www.numberonewholesales.com/';
    $leggingsEndpoint = 'leggings-c-1067.html';
    $palazzoEndpoint = 'palazzo-pants-c-1112.html';
    

    $crawler = $goutte->request('GET', $baseURL . $leggingsEndpoint);

    $status_code = $goutte->getResponse()->getStatus();
echo $status_code;

    // echo $crawler->html();

	if($status_code == 200){

		$domSelector = '//*[@class="productListing"]';


		$crawler->filterXPath($domSelector)->each(function ($node) {
	    	print $node->text()."\n";
		});

		// $domSelector = '//*[@id="product-54391"]/div/div[2]';

		// $crawler->filterXPath($domSelector)->each(function ($node) {
	 //    	print_r($node);
	 //    	echo "\n";
		// });
	}

	