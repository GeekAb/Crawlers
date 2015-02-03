<?php 

function getCategory($category, $subCategory) {

	$categoryStr = '';
	$removableChars = array(" ", "_", ":", ";");

	$subcat = strtolower(str_replace($removableChars, "-", $subCategory));

	switch (strtolower($category)) {
		case 'leggings':
				
			$categoryStr = 'leggings';

			if($subcat == 'basic' || $subcat == 'printed' || $subcat == 'missy' || $subcat == 'jeggings' || $subcat == 'sublimation' || $subcat == 'fleece' || $subcat == 'fur')
				$categoryStr .= '/'.$subcat;

			else if($subcat == 'fleece-leggings')
				$categoryStr .= '/fleece';

			else if($subcat == 'fur-leggings')
				$categoryStr .= '/fur';

			else if($subcat == 'faux-leather')
				$categoryStr .= '/leather';

			else if($subcat == 'treggings')
				$categoryStr .= '/fitness';

			else if($subcat == 'rhinestones' || $subcat == 'high-waist' || $subcat == 'velvet/velour')
				$categoryStr .= '/missy';
			
			break;

		case 'pants':
		case 'shorts':

			$categoryStr = 'pants';

			if($category == 'shorts')
				$categoryStr .= '/shorts';

			if($subcat == 'wide-leg/palazzo')
				$categoryStr .= '/palazzo';

			else if($subcat == 'harem' || $subcat == 'semi-harem-pants')
				$categoryStr .= '/harem';

			break;
		case 'plussize':
			
			$categoryStr = 'plus-size';

			if($subcat == 'basic' || $subcat == 'printed' || $subcat == 'missy' || $subcat == 'jeggings' || $subcat == 'sublimation' || $subcat == 'fleece' || $subcat == 'fur')
				$categoryStr .= '/leggings/'.$subcat;

			else if($subcat == 'faux-leather')
				$categoryStr .= '/leggings/leather';

			else if($subcat == 'treggings')
				$categoryStr .= '/leggings/fitness';

			else if($subcat == 'seamless-leggings' || $subcat == 'high-waist' || $subcat == 'velvet/velour')
				$categoryStr .= '/leggings/missy';

			else if($subcat == 'fleece-leggings')
				$categoryStr .= '/leggings/fleece';

			else if($subcat == 'fur-leggings')
				$categoryStr .= '/leggings/fur';

			else if($subcat == 'pants')
				$categoryStr .= '/pants';

			else if($subcat == 'semi-harem-pants' || $subcat == 'velvet/velour')
				$categoryStr .= '/pants/harem';

			else if($subcat == 'tops')
				$categoryStr = '';

			break;
		case 'kids':
			
			$categoryStr = 'kids';
			break;
		
		default:
			break;
	}

	return $categoryStr;
}

function getDimensions($category, $subCategory) {

	$removableChars = array(" ", "_", ":", ";");
	$subcat = strtolower(str_replace($removableChars, "-", $subCategory));

	if($category == 'leggings' || $subcat == 'printed')
		return array('length' => 9,'width' => 7,'height' => 0.5);
	else 
		return array('length' => 0,'width' => 0,'height' =>0);

}

function getWeight($category, $subCategory) {

	$removableChars = array(" ", "_", ":", ";");
	$subcat = strtolower(str_replace($removableChars, "-", $subCategory));

	if($category == 'leggings' || $subcat == 'printed')
		return 5;
	else 
		return 0;
}
function getSalePrice($costPrice) {
	return ((ceil($costPrice*2)-1)/2)+2.25;
}
