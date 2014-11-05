<?php

require_once("config/default_config.php");

includeMyFiles('urlgenerator');

//Check if input file and site name to search is passed (2 parameters)
if(count($argv) < 3) {
  print("Please check parameters and Run again.\n");
  exit(0);
}

// Retrive parameters
$script_name = $argv[0];
$input_file = $argv[1];
$website = $argv[2];

// Define it as constant
// TODO : Remove this constant
define("SITE", $website);


define("GOOGLE_BASE_URL", "https://www.google.com/search?&q=");
define("GOOGLE_SEARCH_URL",
    "https://www.googleapis.com/customsearch/v1element?" .
    "key=AIzaSyCVAXiUzRYsML1Pv6RwSG1gunmMikTzQqY&" .
    "rsz=filtered_cse&" .
    "num=10&" .
    "hl=en&" .
    "prettyPrint=false&" .
    "source=gcsc&" .
    "gss=.com&" .
    "sig=ee93f9aae9c9e9dba5eea831d506e69a&" .
    // "cx=013998397238731544638:-vr_r85tdmm&" .
    "cx=000351285113061488967:p1lh-gcxv08&" .
    "q=_QUERY&" .
    "sort=&" .
    "googlehost=www.google.com&" .
    "oq=_QUERY&" .
    "gs_l=partner.12...25371.25371.0.26346.1.1.0.0.0.0.170.170.0j1.1.0.gsnos%2Cn%3D13...0.1981j3853693j3..1ac.1.25.partner..1.0.0.Wsa_5yXJf84&" .
    "callback=google.search.Search.apiary15963"
  );


//defining Output CSV file options
$csv['dir']      = OUTPUT_DIR;
$csv['file']     = SITE."_results_" . date("Y-m-d_H-i-s", time()) . ".csv";
$csv['columns']  = array("Page Title","Query","Google URL",SITE." URL");

//Log file based on running script , website and timestamp
$log_file = $script_name."_".SITE."_".date("Y-m-d_H-i-s", time()).".log";

//Scraper object
$scraper = new Scraper(USERAGENT, MIN_SLEEP_TIME, MAX_SLEEP_TIME, $input_file, $csv, $log_file);

//Start crawling
$scraper->crawl();

//Finish crawling
fwrite($scraper->log, "[END]\r\n\r\n");
echo "\nCSV file created: " . $csv['dir'] . "/" . $csv['file'] . "\n";
echo "\nDONE\n";

/**
*@desc
* Scraper class
*/
class Scraper {
  public function __construct($useragent, $min_sleep_time, $max_sleep_time, $input_file, $csv, $log_file) {

    //CURL
    $this->curl = new CurlClient();
    $this->curl->set_user_agent($useragent);
    $this->http_errors = 0;

    //Sleep time
    $this->min_sleep_time = $min_sleep_time;
    $this->max_sleep_time = $max_sleep_time;

    //Visited URLs
    $this->visited_urls = array();
    $this->total_urls_visited = 0;

    //Input file
    $this->input_file = $input_file;

    //CSV file
    $this->csv_filename = $csv['file'];
    $this->csv = $this->create_csv_file($this->csv_filename, $csv['dir']);
    fputcsv($this->csv, $csv['columns']);

    //Log file
    $this->log = fopen($log_file, "a");
    fwrite($this->log, "[START]\r\n");

    $this->start_time = time();
  }

  /**
  *@desc    Crawls the site
  *@return  boolean
  */
  function crawl() {
    
    //Get search terms
    $search_terms = $this->get_search_terms();

    //Get result URLs
    $result_urls = $this->get_result_urls($search_terms);

    foreach ($result_urls as $search_term => $result_url) {
      //Parse results
      $this->parse_results($result_url, $search_term);
      $this->_sleep();
    }
  }

  /**
  *@desc    Imports search terms from CSV file
  *@param    integer $limit (limit number of search terms, 0 = no limit)
  *@return  array $search_terms
  */
  function get_search_terms($limit = 0) {
    $search_terms = array();

    //Read CSV file
    if (! ($handle = @fopen($this->input_file, "r")) ) {
        die("Error: Input file not found: " . $this->input_file . "\n");
    } else {
        fwrite($this->log,"Reading file " . $this->input_file . "\r\n");
    }

    //Parse CSV file
    $line_num = 0;

    while (! feof($handle)) {
        $cols = fgetcsv($handle);
        if ((++ $line_num) == 1) {
            continue;
        }

        // 2nd CSV col. (Model)
        $part_number = trim($cols[1]);
        $part_number = preg_replace("/\s+-\s+/", "-", $part_number);

        // 3rd CSV col. (Mfg)
        $make        = trim($cols[2]);

        // Creating search term
        $search_term = $make . " " . $part_number;

        // Check for duplicates
        if ($make && !in_array($search_term, $search_terms)) {
          $search_terms[] = $search_term;
        }


        if ($limit && ($line_num > $limit)) {
          break;
        }
    }

    // Writing to logs
    fwrite($this->log,"Total search terms: " . count($search_terms) .  "\r\n");
    return $search_terms;
  }

  /**
  *@desc    Build Google URLs which should be scraped
  *@param    array $search_terms
  *@return  array $urls
  */
  function get_result_urls($search_terms) {
      $urls = array();
      
      foreach ($search_terms as $search_term) {
        // Embedding source website
        $search_term .= " site:" . SITE;

        // Creating Google search query
        $url = str_replace("_QUERY", urlencode($search_term), GOOGLE_SEARCH_URL);
        $urls[$search_term] = $url;
      }

      return $urls;
  }

  /**
  *@desc    Parse Google results and save data to file
  *@param    string $url
  *@param    string $search_term
  *@return  boolean
  */
  function parse_results($url, $search_term) {
    $data = array();

    //Get page HTML
    $html = $this->get_html($url);

    if (! $html) {
      return false;
    }
    //Get best result link
    $link = $this->get_best_result_link($html);



    //CSV columns
    $data['Page Title']  = $link['Page Title'];
    $data['Query']       = $search_term;
    $data['Google URL']  = $url;
    $data[SITE.' URL']   = $link[SITE.' URL'];

    //Print extracted data
    if ($data['Page Title']) {
      echo "\tExtracted: Page Title: " . $data['Page Title'] . "\n";
      echo "\tExtracted: URL: "        . $data[SITE.' URL'] . "\n";
    } else {
      echo "\tNO RESULTS\n";
      //echo "\tHTML: " . substr($html, 0, 300) . "...\n";

      fwrite($this->log, "\tNo results\r\n"); //HTML: " . substr($html, 0, 300) . "...\r\n");
    }

    //Save extracted data to file
    fputcsv($this->csv, $data);
    return true;
  }

  /**
  *@desc    Select best result link
  *@param    string $html
  *@return  array $link ('Page Title' => '', 'PCWorld URL' => '')
  */
  function get_best_result_link(&$html) {

    //Best link
    $link = array(
        "Page Title"  => '',
        SITE." URL" => ''
      );

    //All links
    $links = array(
        "Page Title"  => array(),
        SITE." URL" => array()
      );

    $regex = array(
        "Page Title"  => '"titleNoFormatting":"([^"]+)"',
        SITE." URL" => '"url":"(http[^"]+)"'
      );

    if (! ($containers = $this->get_matches('"titleNoFormatting".*?"url":.*?","', $html))) {

      return $link;

    }

    //Parse results
    foreach ($containers as $container) {
      foreach ($regex as $key => $val) {
        $match = $this->get_match($val, $container);
        $match = strip_tags($match);
        $match = str_replace('\\', '', $match);
        $match = trim($match);
        $links[$key][] = $match;
      }
    }

    //No links
    if (! count($links['Page Title'])) {
      return $link;
    //One link
    } else if (count($links['Page Title']) == 1) {
      $link['Page Title']  = $links['Page Title'][0];
      $link[SITE.' URL'] = $links[SITE.' URL'][0];
    //Multiple links
    } else {

      //Select best link

      $best_link_candidates = array();

      for ($i = 0; $i < count($links['Page Title']); $i ++) {

        if(SITE == 'pcworld.com'){
            //Product page
            if ($this->get_match('pcworld.com/product/\d+/', $links[SITE.' URL'][$i])) {
              $best_link_candidates[0] = $i;
              break;
            //Details page
            } else if ($this->get_match('pcworld.com/product/pg/\d+/detail', $links[SITE.' URL'][$i])) {
              if (! isset($best_link_candidates[2])) {
                $best_link_candidates[2] = $i;
              }
            }

            //Specs page
            if ($this->get_match('\bspecs\b', $links['Page Title'][$i])) {
              if (! isset($best_link_candidates[1])) {
                $best_link_candidates[1] = $i;
              }
            }
        }
        else if(SITE == 'trustedreviews.com'){
            //Product page
            // if ($this->get_match('trustedreviews.com/product/\d+/', $links[SITE.' URL'][$i])) {
            //   $best_link_candidates[0] = $i;
            //   break;
            // //Details page
            // } else if ($this->get_match('trustedreviews.com/product/pg/\d+/detail', $links[SITE.' URL'][$i])) {
            //   if (! isset($best_link_candidates[2])) {
            //     $best_link_candidates[2] = $i;
            //   }
            // }

            //Specs page
            if ($this->get_match('\review\b', $links[SITE.' URL'][$i])) {
              if (! isset($best_link_candidates[1])) {
                $best_link_candidates[1] = $i;
              }
            }
        }
      }

      //No good links, return the first one
      if (! $best_link_candidates) {
          $link['Page Title']  = $links['Page Title'][0];
          $link[SITE.' URL'] = $links[SITE.' URL'][0];
      //Get the best link
      } else {
        for ($i = 0; $i < 3; $i ++) {
            if (isset($best_link_candidates[$i])) {
                $link['Page Title']  = $links['Page Title'][$best_link_candidates[$i]];
                $link[SITE.' URL'] = $links[SITE.' URL'][$best_link_candidates[$i]];
                break;
            } 
        }
      }
    }
    return $link;
  }

  /**
  *@desc    Creates CSV directory and file
  *@param    string $file
  *@param    string $dir
  *@return  file pointer resource
  */
  function create_csv_file($file, $dir) {
    //Create CSV directory
    if (! is_dir($dir) && ! @mkdir($dir, 0777, true)) {
      die("Error: Could not create CSV directory " . $dir . "\n");
    }

    //Create CSV file
    if (! $handle = @fopen($dir . "/" . $file, "w")) {
      die("Error: Could not create CSV file " . $dir . "/" . $file . "\n");
    }
    return $handle;
  }

  /**
  *@desc    Regex matching
  *@param    $regex - regex
  *@param    $html  - HTML
  *@return  string $match
  */
  function get_match($regex, &$html) {
    if (! $regex || ! $html) {
      return "";
    }

    preg_match('|' . $regex . '|smi', $html, $match);

    if (isset($match[1])) {
      return $match[1];
    } else if (isset($match[0])) {
      return $match[0];
    }
    return "";
  }

  /**
  *@desc    Regex matching
  *@param    $regex - regex
  *@param    $html  - HTML
  *@return  array $matches
  */
  function get_matches($regex, &$html) {
    if (! $regex || ! $html) {
      return "";
    }

    preg_match_all('|' . $regex . '|smi', $html, $matches);

    if (isset($matches[1])) {
      return $matches[1];
    } else if (isset($matches[0])) {
      return $matches[0];
    }
    return array();
  }

  /**
  *@desc    Fetches HTML
  *@param    $url - URL
  *@return  string $html
  */
  function get_html($url) {
    $html = "";
    if (isset($this->visited_urls[$url])) {
      return $html;
    }
    $this->print_status($url);
    
    $html = $this->curl->get_html($url);
    $http_response = $this->curl->get_http_response_code();
    
    if ($http_response != '200') {
      echo "\tHTTP Response: " . $http_response . "\n";
      fwrite($this->log, "\tHTTP Response: " . $http_response . "\r\n");
      $this->http_errors ++;

      if ($this->http_errors >= 10) {
        fwrite($this->log, "[BREAK]\r\n");
        die("Too many HTTP errors. Breaking...\n");
      }
      $html = "";
    }
    $this->visited_urls[$url] = 1;
    return $html;
  }

  /**
  *@desc    Prints crawling status
  *@param    $url - Current URL
  *@return  void
  */
  function print_status($url) {
    $this->total_urls_visited ++;
    $time_sec = time() - $this->start_time;
    $time     = ( ($time_sec < 3600) ? gmdate("i:s", $time_sec) : gmdate("H:i:s", $time_sec) );
    fwrite($this->log, $this->total_urls_visited . ". " . $url . "\t(" . $time . ")\r\n");
    if (strlen($url) > 150) {
      $url = substr($url, 0, 100) . "...<more>..." . substr($url, strlen($url) - 50);
    };
    echo "\n" . ($this->total_urls_visited) . ". " . $url . " (" . $time . ")\n";
  }

  /**
  *@desc    Sleeps between requests
  *@return  void
  */
  function _sleep() {
    $sleep_time = rand(($this->min_sleep_time * 1000000), ($this->max_sleep_time * 1000000));
    echo "\tSleeping for " . number_format(($sleep_time / 1000000), 2) . " sec\n";
    usleep($sleep_time);
  }

}

?>
