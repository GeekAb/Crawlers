<?php
    
    /*Get subcategory of product*/
    function getSubCategory($category, $subCategory, $title) {

        $removableChars = array(" ", "_", ":", ";");
        $subCategory = strtolower(str_replace($removableChars, "-", $subCategory));

        if( $subCategory == 'basic' || 
            $subCategory == 'missy' || 
            $subCategory == 'leather'  || 
            $subCategory == 'sublimation' ||  
            $subCategory == 'jeggings' ||  
            $subCategory == 'capri' || 
            $subCategory == 'jeans' ||
            $subCategory == 'pants' ||
            $subCategory == 'shorts' ||
            $subCategory == 'jogger' ||
            $subCategory == 'short')
        {
            if($subCategory != 'pants' && $category == 'plus-size' || $category == 'plussize')
                return 'leggings/'.$subCategory;

            return $subCategory;
        }

        else if($subCategory == 'printed-leggings' || $subCategory == 'printed') {

            if($category == 'plus-size' || $category == 'plussize')
                return 'leggings/printed';

            if($category == 'kids')
                return 'leggings';

            return 'printed';
        }

        else if($subCategory == 'fitness' || $subCategory == 'treggings') {
            if($category == 'plus-size' || $category == 'plussize' || $category == 'kids')
                return 'activewear';

            return 'leggings';
        }

        else if($subCategory == 'fleece-leggings' || 
                $subCategory == 'fleece' || 
                $subCategory == 'fur-leggings' || 
                $subCategory == 'fur' || 
                $subCategory == 'rhinestones' || 
                $subCategory == 'high-waist' || 
                $subCategory == 'seamless-leggings' || 
                $subCategory == 'velvet/velour') {

            if($category == 'plus-size' || $category == 'plussize')
                return 'leggings/missy';

            return 'missy';
        }

        else if($subCategory == 'faux-leather') {            
            if($category == 'plus-size' || $category == 'plussize')
                return 'leggings/leather';

            return 'leather';
        }
        
        else if($subCategory == 'skirts')
            return 'skirts';

        else if($subCategory == 'wide-leg/palazzo' || $subCategory == 'palazzo') {
            if($category == 'plus-size' || $category == 'plussize')
                return 'pants/palazzo';

            return 'palazzo';
        }

        else if($subCategory == 'harem' || $subCategory == 'semi-harem-pants') {
            if($category == 'plus-size' || $category == 'plussize')
                return 'pants/harem';

            return 'harem';
        }

        else if($category == 'palazzo' || $category == 'jogger' || $category == 'jeans') {
            return $category;
        }

        if($title != '' && $subCategory == '')
            return getSubCategoryByStr($title,$category);
        else 
            return '';
    }

    function getCategory($category, $subCategory, $title) {

        $categoryStr = '';
        $removableChars = array(" ", "_", ":", ";");

        $category = strtolower(str_replace($removableChars, "-", $category));
        $subcat = strtolower(str_replace($removableChars, "-", $subCategory));

        if ($category == 'leggings')
            return 'leggings';
        
        else if ($category == 'activewear' || $category == 'active-wear')
            return 'activewear';
        
        else if ($category == 'pants' || $category == 'shorts')
            return 'pants';
        
        else if($category == 'plussize' || $category == 'plus-size')
            return 'plus-size';
        
        else if($category == 'kids')
            return 'kids';

        else 
            if($title != '' && $category == '')
                return getCategoryByStr($title);
            else 
                return '';
    }

    function getCategoryByStr($str) {
        $removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $str)));

        $ignore = "/(dress)/i";

        $leggings = "/(legging)/i";
        $plussize = "/(plussize)|(plus-size)/i";
        $kids = "/(kid)/i";
        $pants = "/(pant)/i";
        $skirt = "/(skirt)/i";
        $activewear = "/(activewear)|(active-wear)|(fitness)/i";

         /*Ignore*/
        if(preg_match_all($ignore, $lstr, $matches) != 0)
            return '';

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

    function getSubCategoryByStr($str, $category) {
        $removableChars = array(" ", "_", ":", ";");

        $lstr = trim(strtolower(str_replace($removableChars, "-", $str)));

        $palazzo        = "/(plazzo)/i";
        $pants          = "/(pants)|(pant)/i";
        $printed        = "/(printed)|(print)/i";
        $missy          = "/(missy)|(fleece-legging)|(fleece)|(fur-legging)|(fur)|(rhinestones)|(high-waist)|(waist)|(velvet-velour)|(velvet)|(velour)/i";
        $leather        = "/(leather)|(faux-leather)/i";
        $fitness        = "/(fitness)|(tregging)/i";
        $sublimation    = "/(sublimation)/i";
        $jeggings       = "/(jeggings)/i";
        $capri          = "/(capri)/i";
        $skirts         = "/(skirt)/i";
        $jogger         = "/(jogger)|(track)/i";
        $jeans          = "/(jeans)/i";
        $harem          = "/(harem)/i";
        $seamless       = "/(seamless)/i";
        $shorts         = "/(short)/i";

        $basic          = "/(basic)/i";

        $ignore = "/(dress)/i";
        /*Ignore*/
        if(preg_match_all($ignore, $lstr, $matches) != 0)
            return '';


        if(preg_match_all($palazzo, $lstr, $matches)) {
            return 'palazzo';
        }
        else if(preg_match_all($harem, $lstr, $matches)) {
            return 'harem';
        }
        else if(preg_match_all($jogger, $lstr, $matches)) {
            return 'jogger';
        }
        else if(preg_match_all($shorts, $lstr, $matches)) {
            return 'shorts';
        }
        else if(preg_match_all($pants, $lstr, $matches)) {
            return 'pants';
        }
        else if(preg_match_all($printed, $lstr, $matches)) {
            return 'printed';
        }
        else if(preg_match_all($missy, $lstr, $matches)) {
            return 'missy';
        }
        else if(preg_match_all($leather, $lstr, $matches)) {
            return 'leather';
        }
        else if(preg_match_all($fitness, $lstr, $matches)) {

            if($category == 'plus-size' || $category == 'plussize')
                $val = 'activewear';
            else 
                $val = '';

            $legging = "/(legging)/i";
            if(preg_match_all($legging, $lstr, $matches)) {
                return $val.'/leggings';
            }
            else if(preg_match_all($capri, $lstr, $matches)) {
                return $val.'/capri';
            }
            else if(preg_match_all($shorts, $lstr, $matches)) {
                return $val.'/shorts';
            }

            return $val;
        }
        else if(preg_match_all($sublimation, $lstr, $matches)) {
            return 'sublimation';
        }
        else if(preg_match_all($jeggings, $lstr, $matches)) {
            return 'jeggings';
        }
        else if(preg_match_all($capri, $lstr, $matches)) {
            return 'capri';
        }
        else if(preg_match_all($skirts, $lstr, $matches)) {
            return 'skirts';
        }
        else if(preg_match_all($jeans, $lstr, $matches)) {
            return 'jeans';
        }
        else if(preg_match_all($seamless, $lstr, $matches)) {
            return 'seamless';
        }
        
        else if(preg_match_all($basic, $lstr, $matches)) {
            return 'basic';
        }
    }

    function getCategoryString($category, $subCategory) {

        if($category == 'plus-size') {
            if($subCategory == 'basic' || $subCategory == 'printed' || $subCategory == 'missy' || $subCategory == 'leather' || $subCategory == 'fitness' || $subCategory == 'sublimation' || $subCategory == 'jeggings' || $subCategory == 'capri')
                $categoryStr = $category.'/'.'/leggings'.'/'.$subCategory;

            return $category.'/'.$subCategory;
        }

        else if($category == 'activewear') {
            return $category.'/'.$subCategory;
        }

        else if($category == 'kids') {
            
            if($subCategory == 'palazzo')
                return 'kids/palazzo';
            else if($subCategory == 'skirts')
                return 'kids/skirts';
            else 
                return 'kids/leggings';
        }

        else if($category == 'pants') {
            if($subCategory == 'skirts')
                return 'skirts';

            return $category.'/'.$subCategory;
        }

        else if($category == 'skirts') {
            return $category.'/'.$subCategory;
        }

        else if($category == 'leggings') {
            return $category.'/'.$subCategory;
        }

        else return '';
    }