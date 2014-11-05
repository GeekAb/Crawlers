<?php 
	function logError()
	{
		fwrite($this->log, "\tHTTP Response: " . $http_response . "\r\n");
        
        $errors ++;

        if ($errors >= 10) {

            fwrite($this->log, "[BREAK]\r\n");
            die("Too many HTTP errors. Breaking...\n");
        }
	}
?>