<?php
session_start();
date_default_timezone_set("Asia/Bangkok");
$serverName = "192.168.2.135";
$connectionInfo = array( 
    "Database" => "DB_SCK",
    "UID" => "sa",  
    "PWD" => "And142857"   
);
$conn = sqlsrv_connect( $serverName, $connectionInfo );
if( $conn === false ) {
    die( print_r( sqlsrv_errors(), true));
}


?>