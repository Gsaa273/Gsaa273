<?php
require 'vendor/autoload.php'; // Include Composer dependencies
include_once 'CloudServices.php';

// Establish a database connection using the CloudServices class
try {
    $con = CloudServices::db_open();
}catch (Throwable $e) {
    die("Connection failed: ". $e->getMessage());
}
?>
