<?php
require_once realpath("vendor/autoload.php");

use SpeqtaTest\ConfigFileReader;
use SpeqtaTest\ConfigFormatter;

$config_file="config.txt";
$config_file_reader=new ConfigFileReader($config_file);  //new instance from file reader
$config_formatter =new ConfigFormatter($config_file_reader); //dependency injection
$result =$config_formatter->getConfigAsArray();
###### var dump final result ######
echo "<pre>";
var_dump($result);
exit();

?>