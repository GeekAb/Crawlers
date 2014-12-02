<?php
	require_once("config/default_config.php");

	includeMyFiles('urlgenerator');

	$goutte = new Goutte\Client();

	$baseURL = 'https://www.lashowroom.com/';
    $womenManuEndpoint = 'women/manufacturers';

    //*[@id="lnv_level_content_cat"]/ul

    
    $status_code = 200;
    $count = 1;

    getLinkEndPoints($goutte, $baseURL . $womenManuEndpoint);

    while ($status_code == 200) {
    	
    	$status_code = getLink($goutte, $baseURL . $womenManuEndpoint . $count);
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
			$domSelector = '//*[@id="lnv_level_content_cat"]/ul';

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