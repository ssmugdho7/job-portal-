<?php
$host = 'sql100.infinityfree.com';
$dbname = 'if0_39897880_jobPortal';
$username = 'if0_39897880';
$password = 'Halabrazil23';

$conn = mysqli_connect($host, $username, $password, $dbname);

if($conn){
    // echo("Database is connected Successfully!");
}
else{
    die("Something Went wrong".mysqli_error);
}


?>