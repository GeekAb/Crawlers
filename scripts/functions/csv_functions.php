<?php

	// Function will return CSV header
	function csvHeader($data) {

		$line = '';
		$comma = '';

		foreach($data as $name => $value) {
	    	$line .= $comma . '"' . str_replace('"', '""', $name) . '"';
	    	$comma = ",";
		}

		$line .= "\n";

		return $line;
	}

	// Function will return CSV data row 
	function csvData($data) {

		$line = '';
		$comma = '';

		foreach($data as $value) {
	        $line .= $comma . '"' . str_replace('"', '""', $value) . '"';
	        $comma = ",";
	    }
	    $line .= "\n";
		return $line;
	}