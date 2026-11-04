<?php
$host = "aws-0-ap-southeast-2.pooler.supabase.com"; 
$port = "6543";                                 
$dbname = "postgres";                           
$user = "postgres.nsnaewdbpzdhwpasrqqf";         
$password = "kimgeonwoo01";       

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
