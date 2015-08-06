<?php
	// Include config and initiate
	include_once __DIR__ . '/../config/default_config.php';
	includeMyFiles();

	// Initialize goutte
	$goutte = new Goutte\Client();

	$log = fopen(date("Y-m-d_H-i-s", time()).".log", "a");
    fwrite($log, "[START]\r\n");
    fwrite($log, "[STARTED AT]".time()."\r\n");

    // Get Database
    $db = new Db();

    $db->query("UPDATE products_data SET status=0 WHERE source='numberonewholesales'");

	// URL and EndPoints
	$baseURL = 'http://numberonewholesales.com/';
	$endPoints['leggings'] 	= 'http://numberonewholesales.com/leggings-c-1067.html';
	$endPoints['kids'] 		= 'http://numberonewholesales.com/kids-wear-c-1113.html';
	$endPoints['palazzo'] 	= 'http://numberonewholesales.com/palazzo-pants-c-1112.html';
	$endPoints['skirts'] 	= 'http://numberonewholesales.com/skirts-c-1080.html';
	$endPoints['shorts']	= 'http://numberonewholesales.com/shorts-c-1079.html';
	$endPoints['pants']		= 'http://numberonewholesales.com/pants-c-1084.html';
    
    $loginUrl = 'https://numberonewholesales.com/login.php';

    $crawler = $goutte->request('GET', $loginUrl);
	$form = $crawler->selectButton('Sign In')->form(); 

	// exit;
	$crawler = $goutte->submit($form, array(
	        'email_address' => 'nit.abhi85@gmail.com', 
	        'password' => 'abhishek'
	));

    // Default Status code
    $status_code = 200;
    $count = 1;

    $productUrls = array();

    foreach ($endPoints as $key => $value) {
    	$productUrls[] = getLink($goutte, $baseURL.$value);
    	
    	$sleep_time = rand((1 * 1000000), (2 * 1000000));
    	echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
    	usleep($sleep_time);
    }

    $urls = getUrlFromArray($productUrls);

    foreach ($urls as $key => $value) {
	  	// Insert Product URLs
		$db->query("INSERT IGNORE INTO product_urls(url, url_hash, source, status) 
			VALUES(:url,:url_hash,:source,:status)", array("url" => $value,"url_hash" => hash('ripemd160', $value),"source" => "numberonewholesales","status"=>1));
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

			echo $totalProds[0].'PHP_EOL';

			$productCount = $totalProds[0];

			// Get products data
			$products = getUrlArray($crawler);

			array_push($urls, $products);
			$fetched = count($products);
			// If multiple pages are there
			$pages = $productCount / $fetched;

			if($products % $fetched != 0) 
				$pages += 1;

			if($pages > 1) 
				for ($page=2; $page<=$pages ; $page++) {

					$crawler = $goutte->request('GET', $url.'?page='.$page);

					$status_code = $goutte->getResponse()->getStatus();
					if($status_code == 200){
						$products = getUrlArray($crawler);
						array_push($urls, $products);
					}
				}

			
			echo count($urls).PHP_EOL;
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

?>