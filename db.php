<?php
session_start();
date_default_timezone_set("Asia/Bangkok");
$serverName = "localhost";
$connectionInfo = array( "Database"=>"DB_SCK");
$conn = sqlsrv_connect( $serverName, $connectionInfo );
if( $conn === false ) {
    die( print_r( sqlsrv_errors(), true));
}


?>