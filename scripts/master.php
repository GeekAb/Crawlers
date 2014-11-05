<?php 
require_once("config/default_config.php");

includeMyFiles('master');

print_r(PROCESS_CONF);
exit;
class masterProcess { 

	private $result = NULL; 
   	private $connx = NULL; 
   	private $numRows = 0; 

   	private $childs = array();

	public function __construct(array $arguments = array()) {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                $this->{$property} = $argument;
            }
        }
    }

    // Starting up the process
    public function init() { 
        // $this->forkProcess('test',array('test.php'));
        // $this->forkProcess('test',array('test2.php'));
        $schedules = $this->getSchedules();

        foreach ($schedules as $schedule) {

            if($schedule['process_name'] == 'bestbuy')
                $this->fetchBestbuyData($schedule);

            if($schedule['process_name'] == 'input_file_generator')
                $this->productUpdateProcess($schedule);

            if($schedule['process_name'] == 'amazon')
                $this->fetchAmazonData($schedule);

            if($schedule['process_name'] == 'cnet')
                $this->fetchCnetData($schedule);
        }
    }

    public function fetchBestbuyData($schedule)
    {
        $parameters = array();
        $childs = array();

        if($schedule['child'] != 0)
            $childs = $this->getChild($schedule['child']);
        
        if(!is_null($schedule['param_from_table'])){
            $parameters = $this->getTableParameters($schedule['param_from_table']);
        }

        foreach ($parameters as $param) {

            $this->forkProcess($schedule['process_name'],array($schedule['process_name'].'/'.$schedule['process_file'],$param['name']));
        }

        // Check and wait till products are fetched
        if($schedule['blocker'] == 1)
        {
            $this->checkProcessExecution($this->childs);
        }

        return 1;
    }

    public function fetchAmazonData($schedule)
    {
        $parameters = array();
        $childs = array();

        if($schedule['child'] != 0)
            $childs = $this->getChild($schedule['child']);
        
        if(!is_null($schedule['param_from_table'])){
            $parameters = $this->getTableParameters($schedule['param_from_table']);
        }

        foreach ($parameters as $param) {

            $this->forkProcess($schedule['process_name'],array($schedule['process_name'].'/'.$schedule['process_file'],$param['name']));
        }

        // Check and wait till products are fetched
        if($schedule['blocker'] == 1)
        {
            $this->checkProcessExecution($this->childs);
        }

        return 1;
    }

    public function fetchCnetData($schedule)
    {
        $parameters = array();
        $childs = array();

        if($schedule['child'] != 0)
            $childs = $this->getChild($schedule['child']);
        
        if(!is_null($schedule['param_from_table'])){
            $parameters = $this->getTableParameters($schedule['param_from_table']);
        }

        foreach ($parameters as $param) {

            $this->forkProcess($schedule['process_name'],array($schedule['process_name'].'/'.$schedule['process_file'],$param['name']));
        }

        // Check and wait till products are fetched
        if($schedule['blocker'] == 1)
        {
            $this->checkProcessExecution($this->childs);
        }

        return 1;
    }

    // Function used for full product updation
    public function productUpdateProcess($schedule)
    {
        $datatype = array('all','new');

        $parameters = array();
        $childs = array();

        if($schedule['child'] != 0)
            $childs = $this->getChild($schedule['child']);
        
        if(!is_null($schedule['param_from_table'])){
            $parameters = $this->getTableParameters($schedule['param_from_table']);
        }

        foreach ($parameters as $param) {

            foreach ($datatype as $type) {

                $this->forkProcess($schedule['process_name'],array($schedule['process_name'].'/'.$schedule['process_file'],$param['name'],$param['table_name'],$type));

                $inputFiles[] = 'input_'.$param['name'].'_'.$type.'.csv';
            }    
        }

        // Check and wait till input files are generated
        $this->checkProcessExecution($this->childs);
        
        foreach ($childs as $child) {

            if(!is_null($child['param_from_table'])){

                $parameters = $this->getTableParameters($child['param_from_table']);

            }

            foreach ($inputFiles as $file) {
                foreach ($parameters as $param) {
                    $this->forkProcess('google_search',array($schedule['process_name'].'/google_search.php',$file,$param['url']),'pcworld');
                }

            }
        }

        exit;
        
        // Check and wait till input files are generated
        $this->checkProcessExecution($this->childs);

        // Run
        echo "done";
    }

    public function getChild($id)
    {
        $con = iconnect();

        $data = array();
        $count = 0;

        $query = 'SELECT *';
        $query .= ' FROM process_scheduler';
        $query .= ' WHERE id='.$id;

        $result = $con->query($query) or trigger_error($con->error."[$query]");

        // fetch results
        while($row = $result->fetch_assoc())
        {
            $data[] = $row;
        }

        return $data;
    }

    public function getSchedules()
    {
        $con = iconnect();

        $data = array();
        $count = 0;

        $query = 'SELECT *';
        $query .= ' FROM process_scheduler';
        $query .= ' WHERE start_time IS NOT NULL';
        $query .= ' order by start_time';

        $result = $con->query($query) or trigger_error($con->error."[$query]");

        // fetch results
        while($row = $result->fetch_assoc())
        {
            $data[$count++] = $row;
        }

        return $data;
    }

    public function getTableParameters($table,$where = '')
    {
        $con = iconnect();

        $data = array();

        $query = 'SELECT *';
        $query .= ' FROM '.$table;
        if($where != '')
            $query .= $where;

        $result = $con->query($query) or trigger_error($con->error."[$query]");

        // fetch results
        while($row = $result->fetch_assoc())
        {
            $data[] = $row;
        }

        return $data;
    }

    public function checkProcessExecution($childs)
    {
    	while(count($childs) > 0) {
		    foreach($childs as $key => $pid) {
		        $res = pcntl_waitpid($pid, $status, WNOHANG);
		        
		        // If the process has already exited
		        if($res == -1 || $res > 0)
		            unset($childs[$key]);
		    }
		    
		    sleep(1);
		}
    }

    // Function will check if anything need to be done or not.
    // Return a list of processes need to start
    public function processStatusChecker()
    {
        
    }

    // Function will update process status in Database
    public function updateProcessStatus($processName, $status)
    {
    	
    }

    // Function will initiate another process
    public function forkProcess($processName,$parameters,$otherProcess='')
    {
    	$pid = pcntl_fork();

    	$this->childs[] = $pid;

    	switch ($pid) {
			case 0:
				$cmd = "/usr/bin/php";
			   	pcntl_exec($cmd, $parameters);

                $this->checkProcessExecution(array($pid));

                // $this->forkProcess($otherProcess,array($otherProcess,'input.csv','pcworld.com'),'pcworld_scraper.php');

			   	exit(0);
			 default:
			   	break;
		}
    }
} 

$masterObj = new masterProcess();

$masterObj->init();