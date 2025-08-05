<?php

/* ------------------------------------------------
Afwerken van ajax-request om data op te slaan
ppib plugin NJi - version: 2.0
------------------------------------------------ */
//alert($_POST);
//$data['client_id'] =$_POST["client_id"];
//$data['werksoort'] =$_POST["werksoort"];
//echo json_encode($_POST);
//$plugin_root = plugin_dir_path(__FILE__) ;
include_once "bouwstenen-db_class.php";

$WpDb = new BsDb();
$aa = "insert into meten_response_data (client_id) values ('221315');";
write_log("query: ");
write_log($aa);

$WpDb->exesql($aa);
//echo json_encode($data);
//echo 'hallo daar';
//$requestData = $_POST;
//AIT_ajax($requestData);
write_log("POST:");
write_log(print_r($_POST,1));

function write_log($log_msg)
{
    $log_filename = "c:\logs";
    if (!file_exists($log_filename))
    {
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename.'/debug.log';
  file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
   
} 
?>
