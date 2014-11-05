<?php

	include_once __DIR__ . '/../goutte.phar';

	$goutte = new Goutte\Client();

	$baseURL = 'http://sngapparelinc.com/';
    $leggingsEndpoint = 'leggings.html?p=';
    $plusSizeEndpoint = 'plus-size-leggings-1215.html?p=';
    $winterEndPoint = 'jeggings.html?p=';
    $pantsEndpoint = 'pants.html?p=';
    $shortsEndpoint = 'shorts.html?p=';
    $kidsEndpoint = 'kids.html?p=';
    
    $status_code = 200;
    $count = 1;

    getLinkEndPoints($goutte, $baseURL . $leggingsEndpoint);exit;

    while ($status_code == 200) {
    	
    	$status_code = getLink($goutte, $baseURL . $leggingsEndpoint . $count);
    	$count++;

    	$sleep_time = rand((3 * 1000000), (4 * 1000000));
    	echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
    	usleep($sleep_time);
    }

    function getLinkEndPoints($goutte, $url)
    {
    	$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

		if($status_code == 200){

			// Plus size
			$domSelector = '//*[@id="left_nav"]/li[3]/ul';

			$crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	$node->filterXPath($domS)->each(function ($node) {
		    		print_r($node->attr('href'));
		    		echo "\n";
		    	});
		    	
		    	echo "\n";
			});

			// Leggings
			$domSelector = '//*[@id="left_nav"]/li[4]/ul';

			$crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	$node->filterXPath($domS)->each(function ($node) {
		    		print_r($node->attr('href'));
		    		echo "\n";
		    	});
		    	
		    	echo "\n";
			});

			// Winter
			$domSelector = '//*[@id="left_nav"]/li[5]/ul';

			$crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	$node->filterXPath($domS)->each(function ($node) {
		    		print_r($node->attr('href'));
		    		echo "\n";
		    	});
		    	
		    	echo "\n";
			});

			// Pants
			$domSelector = '//*[@id="left_nav"]/li[6]/ul';

			$crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	$node->filterXPath($domS)->each(function ($node) {
		    		print_r($node->attr('href'));
		    		echo "\n";
		    	});
		    	
		    	echo "\n";
			});

			// Shorts
			$domSelector = '//*[@id="left_nav"]/li[7]/ul';

			$crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	$node->filterXPath($domS)->each(function ($node) {
		    		print_r($node->attr('href'));
		    		echo "\n";
		    	});
		    	
		    	echo "\n";
			});

			// kids
			$domSelector = '//*[@id="left_nav"]/li[9]/ul';

			$crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	$node->filterXPath($domS)->each(function ($node) {
		    		print_r($node->attr('href'));
		    		echo "\n";
		    	});
		    	
		    	echo "\n";
			});
		}

		return $status_code;
    }


    function getLink($goutte, $url)
    {
    	$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

		if($status_code == 200){

			$domSelector = '//*[@class="products-grid"]/li/a';

			$crawler->filterXPath($domSelector)->each(function ($node) {
		    	print_r($node->attr('href'));
		    	echo "\n";
			});
		}

		return $status_code;
    }