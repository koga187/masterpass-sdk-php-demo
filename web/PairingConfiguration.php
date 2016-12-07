<?php

require_once (dirname(__DIR__)) . '/src/controller/MasterPassController.php';

session_start();
$sad = unserialize($_SESSION['sad']);
$controller = new MasterPassController($sad);

$sad = $controller->setPairingDataTypes(explode(",", $_POST['dataTypes']));

$_SESSION['sad'] = serialize($sad);

header('Content-Type: application/json');
echo json_encode($sad);
