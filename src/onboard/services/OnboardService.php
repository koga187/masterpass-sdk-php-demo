<?php

require_once dirname(__DIR__) . '/../../vendor/autoload.php';
require_once 'phar://' . dirname(__DIR__) . '/../../vendor/masterpass/mpasscoresdk/MasterCardCoreSDK.phar/index.php';
require_once 'phar://' . dirname(__DIR__) . '/../../web/MasterCardMerchantOnboarding.phar/index.php';

Logger::configure(dirname(__DIR__) . '/services/config.php');

class OnboardService
{

    public $openFeedId;
    protected $consumerKey;
    private $privateKey;

    public function __construct($consumerKey, $privateKey, $openFeedId)
    {
        $this->openFeedId = $openFeedId;
        $this->consumerKey = $consumerKey;
        $this->privateKey = $privateKey;

        MasterCardApiConfig::$consumerKey = $consumerKey;
        MasterCardApiConfig::$privateKey = $privateKey;
        MasterCardApiConfig::setSandBox(false); // For sandbox environment else
    }

    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    public function postMerchantUpload(MerchantUpload $merchantUpload)
    {
        try {

            return SingleMerchantUploadApi::create($this->openFeedId, $merchantUpload);
            
        } catch (SDKErrorResponseException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function postMerchantValidate(MerchantUpload $merchantUpload)
    {
        try {

            return SingleMerchantValidateApi::create($this->openFeedId, $merchantUpload);
            
        } catch (SDKValidationException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

}
