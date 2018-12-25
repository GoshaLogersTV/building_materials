<?php 
require 'static/scripts/dbManager.php';
header('Content-Type: application/json');

$pdo = _dbConnect();

if($_GET['get']=='providers'){
	$stmt = $pdo->query('SELECT id, name FROM providers');
	$json = json_encode($stmt->fetchAll());
	echo preg_replace( "/:(\d+)/", ':"$1"', $json);
}

$pdo = null;
$stmt = null;

?>