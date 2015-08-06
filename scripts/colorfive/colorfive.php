<?php

	// Include config and initiate
	include_once __DIR__ . '/../config/default_config.php';
	includeMyFiles();

	// Initialize goutte
	$goutte = new Goutte\Client();

    // Get Database
    $db = new Db();
    
    $loginUrl = 'http://www.colorfive.com/sign-in';
	$crawler = $goutte->request('GET', $loginUrl);
print_r($crawler->html());
exit;
    // Default Status code
    $status_code = 200;
    $count = 1;
    
    $data = $db->query("SELECT url from product_urls WHERE source='numberonewholesales'");

    $url = '';
    foreach ($data as $value) {
    	$url = $value['url'];
    	$data = getProductData($goutte, $url, $db);
    }

   

    function getProductData($goutte, $url, $db) {

    	$crawler = $goutte->request('GET', $url);

	    $status_code = $goutte->getResponse()->getStatus();

	    $data = array();

		if($status_code == 200){

			$attribs = array();

			$domSelector = '//*[@class="productInfoName"]';
			
			$attribs['title'] = $data = $crawler->filterXPath($domSelector)->each(function ($node) {
				return $node->text();
			});	

			$domSelector = '//*[@id="zoom1-big"]';
			$attribs['mainImage'] = $data = $crawler->filterXPath($domSelector)->each(function ($node) {
				return $node->attr('href');
			});

			$domSelector = '//*[@id="Zoom1"]';
			$attribs['images'] = $data = $crawler->filterXPath($domSelector)->each(function ($node) {
				return $node->attr('href');
			});	

			$packValueReg = '//*[@class="Quantity_box_pack"]';
			$attribs['packValue'] = $data = $crawler->filterXPath($packValueReg)->each(function ($node) {
				return $node->attr('value');
			});	


			$domSelector = '//*[@class="main_table"]/tr/td[2]';

			$data = $data = $crawler->filterXPath($domSelector)->each(function ($node) {
				return $node->html();
			});			

			foreach ($data as $key => $value) {
				
				/*Style Number*/
				$styleReg = '.*?<td.*?>Style.*?<td.*?><span.*?>(.*?)</span>.*?</td>.*?';
		    	preg_match('|' . $styleReg . '|smi', $value, $match);
		    	if(isset($match[1]))
			    	$attribs['style'] = $match[1];

				// Get price
			    $priceReg = '.*?<td.*?>Price.*?<td.*?>(.*?)</td>.*?';
		    	preg_match('|' . $priceReg . '|smi', $value, $match);
		    	if(isset($match[1]))
			    	$attribs['price'] = $match[1];

			    // Get stock
			    $stockReg = '.*?<td.*?>Stock.*?<td.*?>(.*?)</td>.*?';
		    	preg_match('|' . $stockReg . '|smi', $value, $match);
		    	if(isset($match[1]))
			    	$attribs['stock'] = $match[1];

			    /*Fabric Reg*/
			    $fabricReg = '.*?class="productInfo".*?<p>(.*?)</p>.*?';
			 	preg_match('|' . $fabricReg . '|smi', $value, $match);
		    	if(isset($match[1]))
			    	$attribs['fab'] = $match[1];

			    /*Pack Reg*/
			    $packReg = '.*?Pack Only.*?class="productInfoTitle" bgcolor="#EEEEEE".*?>(.*?)<input type="text".*?</td>';
			    preg_match('|' . $packReg . '|smi', $value, $match);
		    	if(isset($match[1]))
			    	$attribs['pack'] = $match[1];



			    $colorReg = '.*?</script>.*?productInfoContents">(.*?)</td>.*?';
			    preg_match_all('|' . $colorReg . '|smi', $value, $match,PREG_PATTERN_ORDER);
			    
		    	if(isset($match[1]))
			    	$attribs['colors'] = $match[1];

			    $stockReg = '.*?</script>.*?productInfoContents">.*?productInfoContents">.*?productInfoContents">(.*?)</td>.*?';
			    preg_match_all('|' . $stockReg . '|smi', $value, $match,PREG_PATTERN_ORDER);
			    
		    	if(isset($match[1]))
			    	$attribs['temp_stock'] = $match[1];

			    $attribs['stock'] = array();

			    foreach ($attribs['temp_stock'] as $key => $value) {
			    	
			    	$color = trim($attribs['colors'][$key]);

			    	$attribs['stock'][$color] = (trim($value) == 'Sold Out')?0:100;
			    }

			    unset($attribs['temp_stock']);

			}

			$category = getCategoryFromUrlOrTitle($url,$attribs['title']);


			/*Insert Product*/
			$db->query("INSERT INTO products_data(url,url_hash,source,data,category,subcategory,status) 
			    			VALUES(:url,:url_hash,:source,:data,:category,:subcategory,:status)
			    			ON DUPLICATE KEY UPDATE updated_on = NOW();", 
			    				array(	"url"=>$url,
			    						"url_hash" => hash('ripemd160', $url),
			    						"source"=>"numberonewholesales",
			    						"data"=>json_encode($attribs),
			    						"category" => $category,
										"subcategory" => '',
			    						"status"=>1
			    					));
			$db->query("UPDATE products_data SET status=1 WHERE url_hash='".hash('ripemd160', $url)."'");
			
		}

    }

    function getCategoryFromUrlOrTitle($url,$title) {

		if(!(strpos(strtolower(str_replace(' ', '-', $url)), 'kids')) || !(strpos(strtolower(str_replace(' ', '-', $title[0])), 'kids')))
			return 'kids';
		else if(!(strpos(strtolower(str_replace(' ', '-', $url)), 'palazzo')) || !(strpos(strtolower(str_replace(' ', '-', $title[0])), 'palazzo')))
			return 'palazzo';
		else if(!(strpos(strtolower(str_replace(' ', '-', $url)), 'skirts')) || !(strpos(strtolower(str_replace(' ', '-', $title[0])), 'skirts')))
			return 'skirts';
		else if(!(strpos(strtolower(str_replace(' ', '-', $url)), 'shorts')) || !(strpos(strtolower(str_replace(' ', '-', $title[0])), 'shorts')))
			return 'shorts';
		else if(!(strpos(strtolower(str_replace(' ', '-', $url)), 'short')) || !(strpos(strtolower(str_replace(' ', '-', $title[0])), 'short')))
			return 'shorts';
		else if(!(strpos(strtolower(str_replace(' ', '-', $url)), 'pants')) || !(strpos(strtolower(str_replace(' ', '-', $title[0])), 'pants')))
			return 'pants';
		else if (!(strpos(strtolower(str_replace(' ', '-', $title[0])), 'leggings')))
			return 'leggings';
		else if(!(strpos(strtolower(str_replace(' ', '-', $url)), 'leggings')))
			return 'leggings';
    }
