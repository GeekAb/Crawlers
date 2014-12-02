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

    // Get master links
    $data = getLinkEndPoints($goutte, $baseURL . $leggingsEndpoint);

    while ($data['status'] == 200) {

    	$urls = getUrlFromArray($data['urls']);

    	// Get product urls
    	foreach ($urls as $url) {
    		// print_r($url);exit;
    		$status_code = getLink($goutte, $url);

	    	$sleep_time = rand((3 * 1000000), (4 * 1000000));
	    	echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
	    	usleep($sleep_time);
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

			$urls[$count++] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return $node->attr('href');
		    	});
			});

			// Leggings
			$domSelector = '//*[@id="left_nav"]/li[4]/ul';

			$urls[$count++] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return $node->attr('href');
		    	});
			});

			// Winter
			$domSelector = '//*[@id="left_nav"]/li[5]/ul';

			$urls[$count++] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return $node->attr('href');
		    	});
			});

			// Pants
			$domSelector = '//*[@id="left_nav"]/li[6]/ul';

			$urls[$count++] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
					return $node->attr('href');
		    	});
			});

			// Shorts
			$domSelector = '//*[@id="left_nav"]/li[7]/ul';

			$urls[$count++] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return $node->attr('href');
		    	});
			});

			// kids
			$domSelector = '//*[@id="left_nav"]/li[9]/ul';

			$urls[$count++] = $crawler->filterXPath($domSelector)->each(function ($node) {

				$domS = '//li/a';

		    	return $node->filterXPath($domS)->each(function ($node) {
		    		return $node->attr('href');
		    	});
			});
		}

		return array('status' => $status_code , 'urls' => $urls);
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