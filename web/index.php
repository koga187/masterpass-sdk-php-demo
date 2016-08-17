<?php

include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../vendor/masterpass/mpasscoresdk/MasterCardCoreSDK.phar';
include_once __DIR__ . '/../vendor/masterpass/masterpassmerchantsdk/MasterCardMasterPassMerchant.phar';

MasterCardApiConfig::$consumerKey = "YOUR_CONSUMER_KEY";
MasterCardApiConfig::$privateKey = "YOUR_PRIVATE_KEY";
MasterCardApiConfig::setSandBox(true); // For sandbox environment else
MasterCardApiConfig::setSandBox(false); // For production environment 
// Calling Service Api
$RequestTokenResponse = RequestTokenApi::create("http://localhost");

