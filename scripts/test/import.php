<?php

// assuming that your script file is located in magmi/integration/scripts/myscript.php,
// include "magmi_defs.php" and "magmi_datapump.php" first (note: there are two folders "inc" in different subfolders).
require_once("../../magmi/inc/magmi_defs.php");
require_once("../../magmi/integration/inc/magmi_datapump.php");

$dp=Magmi_DataPumpFactory::getDataPumpInstance("productimport");


$dp->beginImportSession("default","create");

// Here we define a single "simple" item, with name, sku,price,attribute_set,store,description
$testitem=array("name"=>"test","sku"=>"testsku","price"=>"10.00","attribute_set"=>"Default","store"=>"admin","description"=>"ingested with Datapump API");

$dp->ingest($testitem);

$dp->endImportSession();

?>