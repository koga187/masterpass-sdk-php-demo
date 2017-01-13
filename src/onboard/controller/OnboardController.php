<?php

require_once 'MasterPassData.php';
require_once 'MasterPassHelper.php';
require_once dirname(dirname(__DIR__)) . '/onboard/services/OnboardService.php';

/**
 * Description of OnboardController
 *
 * @author dc-user
 */
class OnboardController
{

    public $service;
    public $appData;

    /**
     * Constructor for MasterPassController
     * @param MasterPassData $masterPassData
     */
    public function __construct($masterPassData)
    {
        $consumerKey = $masterPassData->consumerKey;
        $privateKey = $this->getPrivateKey($masterPassData);
        $this->service = new OnboardService($consumerKey, $privateKey, $masterPassData->openFeedId);
        $this->appData = $masterPassData;
    }

    /**
     * Method to retrieve the private key from the p12 file
     *
     * @return Private key string
     */
    private function getPrivateKey($masterPassData)
    {
        $thispath = dirname(__DIR__) . "/../../" . $masterPassData->keystorePath;
        $path = realpath($thispath);
        $keystore = array();
        $pkcs12 = file_get_contents($path);
        trim(openssl_pkcs12_read($pkcs12, $keystore, $masterPassData->keystorePassword));

        return $keystore['pkey'];
    }

    /**
     * Post merchant upload
     * 
     * @return MasterPassData 
     */
    public function postMerchantUpload()
    {
        // clear the data
        $merchantDelete = $this->deleteMerchantUpload();
        $this->service->postMerchantUpload($merchantDelete);
        
        $merchantUpload = $this->createMerchantUpload();
        $this->appData->openFeedResponse = $this->service->postMerchantUpload($merchantUpload);
        $this->appData->openFeedRequest = $merchantUpload;

        return $this->appData;
    }

    public function postMerchantValidate()
    {
        $merchantUpload = $this->createMerchantUpload();

        $this->appData->validateResponse = $this->service->postMerchantValidate($merchantUpload);
        $this->appData->validateRequest = $merchantUpload;

        return $this->appData;
    }

    private function createMerchantUpload($SPMerchantId = 'SPMerch58401')
    {
        # Create an instance of MerchantUpload
        return new MerchantUpload(array(
            'Merchant' => new Merchant(array(
                'Action' => 'C',
                'SPMerchantId' => $SPMerchantId,
                'CheckoutBrand' => new CheckoutBrand(array(
                    'Name' => 'SPMerch58401',
                    'SandboxUrl' => 'https://SPMerch58401.com',
                    'LogoUrl' => 'http://www.mastercard.us/_globalAssets/img/nav/navl_logo_mastemasterca.png',
                    'ProductionUrl' => 'https://SPMerch58401.com',
                    'DisplayName' => 'SPMerch58401'
                )),
                'Profile' => new Profile(array(
                    'DoingBusAs' => 'SPMerch58401',
                    'Name' => 'SPMerch58401',
                    'Emails' => new Emails(array(
                        'EmailAddress' => 'email@masterpass.com'
                            )),
                    'Phone' => new Phone(array(
                        'Number' => 3734517671,
                        'CountryCode' => 1
                            )),
                    'Url' => 'https://SPMerch58401.com',
                    'Address' => new Address(array(
                        'Line1' => '898 SPMerch58401',
                        'PostalCode' => 78090,
                        'Country' => 'US',
                        'City' => 'SPMerch58401'
                    )),
                    'BusinessCategory' => 'test',
                    'FedTaxId' => 211624440
                )),
                'AuthOption' => array(
                    new AuthOption(array(
                        'CardBrand' => 'VISA',
                        'Type' => 'ALL_TRANSACTIONS'
                    )),
                    new AuthOption(array(
                        'CardBrand' => 'MASTER_CARD',
                        'Type' => 'ALL_TRANSACTIONS'
                    )),
                ),
                'MerchantAcquirer' => array(
                    new MerchantAcquirer(array(
                        'Acquirer' => new Acquirer(array(
                            'Id' => 540452,
                            'Name' => 'CSOB',
                            'AssignedMerchantId' => 'ACQMC113'
                        )),
                        'MerchantAcquirerBrand' => new MerchantAcquirerBrand(array(
                            'CardBrand' => 'MASTER_CARD',
                            'Currency' => 'EUR'
                        ))
                    )),
                    new MerchantAcquirer(array(
                        'Acquirer' => new Acquirer(array(
                            'Id' => 491011,
                            'Name' => 'CSOB',
                            'AssignedMerchantId' => 'ACQVS114',
                            'Password' => 'CSob1214'
                        )),
                        'MerchantAcquirerBrand' => new MerchantAcquirerBrand(array(
                            'CardBrand' => 'VISA',
                            'Currency' => 'EUR'
                        ))
                    ))
                )
            ))
        ));
    }
    
    private function deleteMerchantUpload($SPMerchantId = 'SPMerch58401')
    {
        # Create an instance of MerchantUpload
        return new MerchantUpload(array(
            'Merchant' => new Merchant(array(
                'Action' => 'D',
                'SPMerchantId' => $SPMerchantId
            ))
        ));
    }
    

    public function processParameters($_POST_DATA)
    {
        if ($_POST_DATA) {
            $acceptedCardsString = "";

            if (isset($_POST_DATA['acceptedCardsCheckbox'])) {
                foreach ($_POST_DATA['acceptedCardsCheckbox'] as $value) {
                    $acceptedCardsString .= $value . ",";
                }
            }

            if (isset($_POST_DATA['privateLabelText'])) {
                $acceptedCardsString = $acceptedCardsString . $_POST_DATA['privateLabelText'];
            } else {
                $acceptedCardsString = substr($acceptedCardsString, 0, strlen($acceptedCardsString) - 1);
            }

            $this->appData->acceptableCards = $acceptedCardsString;
            $this->appData->xmlVersion = isset($_POST_DATA['xmlVersionDropdown']) ? $_POST_DATA['xmlVersionDropdown'] : "";
            $this->appData->shippingSuppression = isset($_POST_DATA['shippingSuppressionDropdown']) ? $_POST_DATA['shippingSuppressionDropdown'] : "";
            $this->appData->rewardsProgram = isset($_POST_DATA['rewardsDropdown']) ? $_POST_DATA['rewardsDropdown'] : "";
            $this->appData->shippingProfile = isset($_POST_DATA['shippingProfileDropdown']) ? $_POST_DATA['shippingProfileDropdown'] : "";
            $this->appData->iframeCall = isset($_POST_DATA['iframeDropdown']) ? (bool) $_POST_DATA['iframeDropdown'] : null;


            if (isset($_POST_DATA['authenticationCheckBox']) && $_POST_DATA['authenticationCheckBox'] == "on") {
                $this->appData->authLevelBasic = true;
            } else {
                $this->appData->authLevelBasic = false;
            }
        }

        return $this->appData;
    }

}
