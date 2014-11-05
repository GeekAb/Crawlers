<?php
    //Checking if session is not started and starting new session
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    
    //Application Path & website default address
    define("DEFAULT_WEB_ADDRESS",'localhost:82');
    // define("APPLICATION_PATH",$_SERVER['DOCUMENT_ROOT'].'/');
    define("APPLICATION_PATH",'/home/geekab/development/wholesakeleggings/robots/scripts/');

    define("APPLICATION_ENV",'development'); //Possible values : development,testing,production
    
    //Setting up error reporting based on env.
    if(APPLICATION_ENV=='development' || APPLICATION_ENV=='testing') error_reporting(E_ALL);
    else if(APPLICATION_ENV=='production') error_reporting(0);
        
    //Status
    define("SUCCESS","1");
    define("YES","1");
    define("FAIL","0");
    define("NO","0");
    
    //Error Codes
    define("NO_RECORDS_FOUND",-1);
    define("DATABASE_ERROR",-2);
    define("API_ERROR",-3);
    
    //DB Constants
    define("MYSQL_HOST","localhost");
    define("MYSQL_USERNAME","root");
    define("MYSQL_PASSWD","root");
    define("MYSQL_DB","wordpress");

    define("MIN_SLEEP_TIME",2);
    define("MAX_SLEEP_TIME",2);
    define("OUTPUT_DIR","csv");
    define("USERAGENT","Mozilla/5.0 (Windows NT 6.2) Firefox/20.0");

    define("LOGFILE_DIR","logs");
    
    //Function will manage all include requests
    function includeMyFiles($filename='')
    {
        require_once(APPLICATION_PATH."/config/database.php");

        if($filename == 'master') {
            require_once(APPLICATION_PATH."/config/process_config.php");
        }
        else if($filename == 'bestbuy') {
            require_once(APPLICATION_PATH."/functions/curl_functions.php");
            define("LOGFILE_NAME","bestbuy_");
        }
        else if($filename == 'input_file_generator') {
            require_once(APPLICATION_PATH."/functions/csv_functions.php");
        } 
        else if($filename=='google_scraper')
        {
            require_once(APPLICATION_PATH."/functions/csv_functions.php");
            require_once(APPLICATION_PATH."/functions/curl_functions.php");
        }
    }
?>