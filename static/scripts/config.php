<?php
$defaultRowsPerPage = 50;
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$productsAllowed = array('name', 'price', 'provider_id', 'description');
$providerAllowed = array('name');
?>