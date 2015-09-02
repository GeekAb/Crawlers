<?php
	$palazzo        = "/(palazzo)|(plazzo)/i";
    $pants          = "/(pants)|(pant)/i";
    $printed        = "/(printed)|(print)|(paisley)|(aztec)|(alphabet)|(botanical)|(floral)|(chevron)/i";
    $missy          = "/(missy)|(fleece-legging)|(fleece)|(fur-legging)|(fur)|(rhinestones)|(high-waist)|(waist)|(velvet-velour)|(velvet)|(velour)/i";
    $leather        = "/(leather)|(faux-leather)/i";
    $fitness        = "/(fitness)|(tregging)/i";
    $sublimation    = "/(sublimation)/i";
    $jeggings       = "/(jeggings)/i";
    $capri          = "/(capri)/i";
    $skirts         = "/(skirt)/i";
    $jogger         = "/(jogger)|(track)/i";
    $active         = "/(active)/i";
    $jeans          = "/(jeans)/i";
    $harem          = "/(harem)/i";
    $seamless       = "/(seamless)/i";
    $shorts         = "/(short)/i";
    $leggings       = "/(legging)|(leggings)/i";

    $basic          = "/(basic)/i";

	function getCategoryByStr($str) {
		global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
        $removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $str)));

        $ignore = "/(dress)/i";

        $leggings = "/(legging)|(treggings)/i";
        $plussize = "/(plussize)|(plus-size)/i";
        $kids = "/(kid)/i";
        $pants = "/(pant)/i";
        $skirt = "/(skirt)/i";
        $activewear = "/(activewear)|(active-wear)|(fitness)/i";

         /*Ignore*/
        if(preg_match_all($ignore, $lstr, $matches) != 0)
            return '-1';

        /*Plus size*/
        if(preg_match_all($plussize, $lstr, $matches) != 0)
            return 'plus-size';

        else if(preg_match_all($activewear, $lstr, $matches) != 0)
            return 'activewear';

        else if(preg_match_all($kids, $lstr, $matches) != 0)
            return 'kids';

        else if(preg_match_all($pants, $lstr, $matches) != 0)
            return 'pants';

        else if(preg_match_all($skirt, $lstr, $matches) != 0)
            return 'skirts';

        else if(preg_match_all($leggings, $lstr, $matches) != 0)
            return 'leggings';

        else
            return ''; 
    }

    function getSubCategory($subCategory, $title) {
    	global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
        $removableChars = array(" ", "_", ":", ";");
        $subCategory = strtolower(str_replace($removableChars, "-", $subCategory));

        if($subCategory == 'fleece-leggings' || 
                $subCategory == 'fleece' || 
                $subCategory == 'fur-leggings' || 
                $subCategory == 'fur' || 
                $subCategory == 'rhinestones' || 
                $subCategory == 'high-waist' || 
                $subCategory == 'seamless-leggings' || 
                $subCategory == 'velvet/velour') {

            $subCategory = 'missy';
        }

        else if($subCategory == 'faux-leather') {
            $subCategory = 'leather';
        }
        
        else if($subCategory == 'skirts')
            $subCategory = 'skirts';

        else if($subCategory == 'wide-leg/palazzo' || $subCategory == 'palazzo') {
            $subCategory = 'palazzo';
        }

        else if($subCategory == 'harem' || $subCategory == 'semi-harem-pants') {
            $subCategory = 'harem';
        }

        if( $subCategory == 'basic' || $subCategory == 'missy' || 
            $subCategory == 'leather'  || $subCategory == 'sublimation' ||  
            $subCategory == 'jeggings' ||  $subCategory == 'harem' ||
            $subCategory == 'capri' || $subCategory == 'jeans' ||
            $subCategory == 'pants' || $subCategory == 'shorts' ||
            $subCategory == 'jogger' || $subCategory == 'skirts' ||
            $subCategory == 'printed-leggings' || $subCategory == 'printed' ||
            $subCategory == 'fitness' || $subCategory == 'treggings' ||
            $subCategory == 'short' || $subCategory == 'palazzo')
        {
            return $subCategory;
        }
        else 
        	return '';
    }

	function getCategoryString ($category, $subCategory, $title) {
		global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
		$categoryStr = '';
        $removableChars = array(" ", "_", ":", ";");

        $category = strtolower(str_replace($removableChars, "-", $category));
        $subcat = strtolower(str_replace($removableChars, "-", $subCategory));

        if(!($category == 'plussize' || $category == 'plus-size' || 
        	 $category == 'kids' || 
        	 $category == 'activewear' || $category == 'active-wear' || 
        	 $category == 'pants' || $category == 'shorts' || 
        	 $category == 'skirt' || $category == 'skirts')) {
        	
        	$category = getCategoryByStr($title);
    	}

    	$temp = getCategoryByStr($title);

    	if($temp == '-1')
    		return '';
    	
    	if($category == 'pants' || $category == 'shorts') {
            if($temp != '')
                $category = $temp;
        }

        if($temp == 'plus-size')
        	$category = 'plus-size';

        if($category == 'plussize' || $category == 'plus-size')
        	return 'plus-size'.plus($category, $subCategory, $title);
        else if($category == 'kids')
        	return 'kids'.kids($category, $subCategory, $title);
        else if ($category == 'activewear' || $category == 'active-wear')
        	return 'activewear'.activewear($category, $subCategory, $title);
        else if ($category == 'skirt' || $category == 'skirts')
        	return 'skirts';
        else if ($category == 'leggings')
        	return 'leggings'.leggings($category, $subCategory, $title);
        else if ($category == 'pants' || $category == 'shorts')
        	return 'pants'.pants($category, $subCategory, $title);
	}

	function leggings ($category, $subCategory, $title) {
		global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
		$subCategory = getSubCategory($subCategory, $title);

		$removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $title)));

        if($subCategory == 'missy' || preg_match_all($missy, $lstr, $matches)) {
            return '/missy';
        }
        else if($subCategory == 'leather' || preg_match_all($leather, $lstr, $matches)) {
            return '/leather';
        }
        else if($subCategory == 'sublimation' || preg_match_all($sublimation, $lstr, $matches)) {
            return '/sublimation';
        }
        else if($subCategory == 'jeggings' || preg_match_all($jeggings, $lstr, $matches)) {
            return '/jeggings';
        }
        else if($subCategory == 'capri' || preg_match_all($capri, $lstr, $matches)) {
            return '/capri';
        }
        else if($subCategory == 'seamless' || preg_match_all($seamless, $lstr, $matches)) {
            return '/seamless';
        }
        else if($subCategory == 'shorts' || $subCategory == 'short' || preg_match_all($shorts, $lstr, $matches)) {
            return '/shorts';
        }
        else if($subCategory == 'pants' || $subCategory == 'pant' || preg_match_all($pants, $lstr, $matches)) {
            return '/pants';
        }
    	else if($subCategory == 'harem' || preg_match_all($harem, $lstr, $matches)) {
            return '/harem';
        }
        if($subCategory == 'printed' || preg_match_all($printed, $lstr, $matches)) {
            return '/printed';
        }
        else 
        	return '/basic';
	}

	function activewear ($category, $subCategory, $title) {
		global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
		$subCategory = getSubCategory($subCategory, $title);

		$removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $title)));

		if($subCategory == 'capri' || preg_match_all($capri, $lstr, $matches)) {
            return '/capri';
        }
        else if($subCategory == 'shorts' || preg_match_all($shorts, $lstr, $matches)) {
            return '/shorts';
        }
        else if($subCategory == 'leggings' || $subCategory == 'legging' || preg_match_all($leggings, $lstr, $matches)) {
            return '/leggings';
        }
        else 
        	return '';
	}

	function pants ($category, $subCategory, $title) {
		global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
		$subCategory = getSubCategory($subCategory, $title);

		$removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $title)));

        if($subCategory == 'shorts' || $subCategory == 'short' || preg_match_all($shorts, $lstr, $matches)) {
            return '/shorts';
        }
		else if($subCategory == 'harem' || preg_match_all($harem, $lstr, $matches)) {
            return '/harem';
        }
        else if($subCategory == 'plazzo' || $subCategory == 'palazzo' ||preg_match_all($palazzo, $lstr, $matches)) {
            return '/palazzo';
        }
        else if($subCategory == 'jogger' || preg_match_all($jogger, $lstr, $matches)) {
            return '/jogger';
        }
        else if($subCategory == 'jeans' || $subCategory == 'jean' || preg_match_all($jeans, $lstr, $matches)) {
            return '/jeans';
        }
        else 
        	return '';
	}

	function plus ($category, $subCategory, $title) {
		global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
		$subCategory = getSubCategory($subCategory, $title);

		$removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $title)));

		if($subCategory == 'activewear' || $subCategory == 'active-wear' || 
			preg_match_all($fitness, $lstr, $matches) || preg_match_all($active, $lstr, $matches)) {
            return '/activewear'.activewear($category, $subCategory, $title);
        }
        else if($subCategory == 'skirts' || $subCategory == 'skirt' || preg_match_all($skirts, $lstr, $matches)) {
            return '/skirts';
        }
        else if($subCategory == 'pant' || $subCategory == 'pants' || preg_match_all($pants, $lstr, $matches)) {
            return '/pants'.pants($category, $subCategory, $title);
        }
        else if($subCategory == 'short' || $subCategory == 'shorts' || preg_match_all($shorts, $lstr, $matches)) {
            return '/pants/shorts';
        }      
        else if($subCategory == 'leggings' || $subCategory == 'legging' || preg_match_all($leggings, $lstr, $matches)) {
            return '/leggings'.leggings($category, $subCategory, $title);
        }
        else if(preg_match_all($jeggings, $lstr, $matches)) {
            return '/leggings/jeggings';
        }
        else 
        	return '';
	}

	function kids ($category, $subCategory, $title) {
		global $leggings, $active, $palazzo, $pants, $printed, $missy, $leather, $fitness, $sublimation, $jeggings, $capri, $skirts, $jogger, $jeans, $harem, $seamless, $shorts, $basic; 
		$subCategory = getSubCategory($subCategory, $title);

		$removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $title)));

		if($subCategory == 'skirt' || $subCategory == 'skirts' || preg_match_all($skirts, $lstr, $matches)) {
            return '/skirt';
        }
        else if($subCategory == 'palazzo' || preg_match_all($palazzo, $lstr, $matches)) {
            return '/palazzo';
        }
        else if($subCategory == 'leggings' || $subCategory == 'legging' || preg_match_all($leggings, $lstr, $matches)) {
            return '/leggings';
        }
        else 
        	return '';
	}


	echo getCategoryString('activewear','','Wholesale Womens Activewear leggings');