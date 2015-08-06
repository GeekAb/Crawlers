<?php

function getSalePrice($costPrice) {
	return ((ceil($costPrice*2)-1)/2)+2.25;
}

$priceList = array('2.14','3.14','.9','.75','0.1');

foreach($priceList as $price)
	echo $price.'----------------'.getSalePrice($price)."\r\n";