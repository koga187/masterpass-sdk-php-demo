<?php

namespace MasterpassDemo\src\checkout\controller;

require_once 'MasterPassData.php';
require_once 'MasterPassHelper.php';
require_once dirname(dirname(__DIR__)) . '/checkout/services/MasterPassService.php';

use MasterpassDemo\src\checkout\services\MasterPassService;

class MasterPassController
{

    public $service;
    public $appData;

    // constant tax and shipping values for the checkout flow
    const TAX = 3.48;
    const SHIPPING = 8.95;
    const SHOPPING_CART_XML = "resources/shoppingCart.xml";
    const MERCHANT_INIT_XML = "resources/merchantInit.xml";
    const MERCHANT_TRANSACTION_XML = "resources/merchantTransaction.xml";

    /**
     * Constructor for MasterPassController
     * @param MasterPassData $masterPassData
     */
    public function __construct($masterPassData)
    {
        $consumerKey = $masterPassData->consumerKey;
        $privateKey = $this->getPrivateKey($masterPassData);
        $originUrl = $masterPassData->callbackDomain;
        $this->service = new MasterPassService($consumerKey, $privateKey, $originUrl);
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
     *  Method to parse and set POST data sent from the index page
     *  
     *  @param POST object
     *  
     *  @return String : string to append to a URL 
     */
    public function parsePostData($_POST_DATA)
    {

        if ($_POST_DATA != null) {
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

            if (isset($_POST_DATA['authenticationCheckBox']) && $_POST_DATA['authenticationCheckBox'] == "on") {
                $this->appData->authLevelBasic = true;
            } else {
                $this->appData->authLevelBasic = false;
            }

            $redirectParameters = MasterPassService::ACCEPTABLE_CARDS . Connector::EQUALS . $this->appData->acceptableCards
                    . Connector::AMP . MasterPassService::VERSION . Connector::EQUALS . $this->appData->xmlVersion
                    . Connector::AMP . MasterPassService::SUPPRESS_SHIPPING_ADDRESS . Connector::EQUALS . $this->appData->shippingSuppression
                    . Connector::AMP . MasterPassService::AUTH_LEVEL . Connector::EQUALS . ($this->appData->authLevelBasic ? "true" : "false")
                    . Connector::AMP . MasterPassService::ACCEPT_REWARDS_PROGRAM . Connector::EQUALS . $this->appData->rewardsProgram;
            return $redirectParameters;
        }
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

    public function setPostbackParameter($_POST_DATA)
    {
        if ($_POST_DATA) {
            $postbackParameter = '';
            if (isset($_POST["postbackVersionDropdown"])) {
                $postbackParameter = '&postbackVersionDropdown=' . $_POST["postbackVersionDropdown"];
            }
            $this->appData->callbackUrl = $this->appData->callbackUrl . "?profileName=" . $profileName . $postbackParameter;
        }
        return $this->appData;
    }

    public function setCallbackParameters($_GET_DATA)
    {
        $this->appData->requestToken = isset($_GET_DATA[MasterPassService::OAUTH_TOKEN]) ? $_GET_DATA[MasterPassService::OAUTH_TOKEN] : NULL;
        $this->appData->requestVerifier = isset($_GET_DATA[MasterPassService::OAUTH_VERIFIER]) ? $_GET_DATA[MasterPassService::OAUTH_VERIFIER] : NULL;
        $this->appData->checkoutResourceUrl = isset($_GET_DATA[MasterPassService::CHECKOUT_RESOURCE_URL]) ? $_GET_DATA[MasterPassService::CHECKOUT_RESOURCE_URL] : NULL;
        return $this->appData;
    }

    public function setPairingDataTypes($dataTypes)
    {
        $this->appData->pairingDataTypes = $dataTypes;
        return $this->appData;
    }

    public function setPrecheckoutCardId($cardId)
    {
        $this->appData->preCheckoutCardId = $cardId;
        return $this->appData;
    }

    public function setPrecheckoutShippingId($shippingId)
    {
        $this->appData->preCheckoutShippingAddressId = $shippingId;
        return $this->appData;
    }

    public function setPairingToken($pairingToken)
    {
        if ($pairingToken != NULL) {
            $this->appData->pairingToken = $pairingToken;
        }
        return $this->appData;
    }

    public function setPairingVerifier($pairingVerifier)
    {
        if ($pairingVerifier != NULL) {
            $this->appData->pairingVerifier = $pairingVerifier;
        }
        return $this->appData;
    }

    /**
     * Update the domain of the Image URL's to the callback domain listed in the config.ini file.
     *
     * @param $shoppingCartData
     * @param $callbackdomain
     *
     * @return XML object
     */
    private function updateImageURL($shoppingCartData)
    {
        $break = explode('/', $_SERVER['REQUEST_URI']);

        foreach ($shoppingCartData->ShoppingCart->ShoppingCartItem as $item) {
            $item->ImageURL = str_ireplace("http://projectabc.com", $this->appData->callbackDomain, $item->ImageURL);
        }

        return $shoppingCartData;
    }

    /**
     * Get request token
     * 
     * @return MasterpassData
     */
    public function getRequestToken()
    {
        $requestTokenResponse = $this->service->getRequestToken($this->appData->callbackUrl);
        $this->appData->requestToken = $requestTokenResponse->OauthToken;
        $this->appData->requestTokenResponse = $requestTokenResponse;

        return $this->appData;
    }

    /**
     * Get pairing token
     * 
     * @return MasterpassData
     */
    public function getPairingToken()
    {
        $pairingTokenResponse = $this->service->getRequestToken($this->appData->callbackUrl);
        $this->appData->pairingTokenResponse = $pairingTokenResponse;
        $this->appData->pairingToken = $pairingTokenResponse->OauthToken;
        $this->appData->requestToken = $pairingTokenResponse->OauthToken;

        return $this->appData;
    }

    /**
     * Get long access token
     * 
     * @return MasterpassData
     */
    public function getLongAccessToken()
    {
        $longAccessTokenResponse = $this->service->getAccessToken($this->appData->pairingToken, $this->appData->pairingVerifier);
        $this->appData->longAccessTokenResponse = $longAccessTokenResponse;
        $this->appData->longAccessToken = is_null($longAccessTokenResponse) ? "" : $longAccessTokenResponse->OauthToken;
        $this->appData->oAuthSecret = is_null($longAccessTokenResponse) ? "" : $longAccessTokenResponse->OauthTokenSecret;

        return $this->appData;
    }

    public function getAccessToken()
    {
        $accessTokenResponse = $this->service->getAccessToken($this->appData->requestToken, $this->appData->requestVerifier);
        $this->appData->accessTokenResponse = $accessTokenResponse;
        $this->appData->accessToken = $accessTokenResponse->OauthToken;

        return $this->appData;
    }

    public function postShoppingCart()
    {
        $requestToken = $this->appData->requestToken;

        $request = new ShoppingCartRequest(
                array(
            'ShoppingCart' => new ShoppingCart(
                    array(
                'Subtotal' => 74996,
                'CurrencyCode' => 'USD',
                'ShoppingCartItem' => array(
                    new ShoppingCartItem(
                            array(
                        'ImageURL' => 'https://somemerchant.com/images/xbox.jpg',
                        'Value' => 29999,
                        'Description' => 'XBox 360',
                        'Quantity' => 1
                            )
                    ),
                    new ShoppingCartItem(
                            array(
                        'ImageURL' => 'https://somemerchant.com/images/CellPhone.jpg',
                        'Value' => 4999,
                        'Description' => 'Cell Phone',
                        'Quantity' => 1
                            )
                    ),
                    new ShoppingCartItem(
                            array(
                        'ImageURL' => 'https://somemerchant.com/images/monitor.jpg',
                        'Value' => 24999,
                        'Description' => '27 Monitoritor',
                        'Quantity' => 1
                            )
                    ),
                    new ShoppingCartItem(
                            array(
                        'ImageURL' => 'https://somemerchant.com/images/garmin.jpg',
                        'Value' => 14999,
                        'Description' => 'Garmin PS',
                        'Quantity' => 1
                            )
                    )
                ),
                    )
            ),
            'OAuthToken' => $requestToken
                )
        );

        $this->appData->shoppingCartResponse = $this->service->postShoppingCartData($request);

        return $this->appData;
    }

    /**
     * Method to post the Merchant Initialization XML to MasterCards services. The XML is parsed from the shoppingCart.xml file.
     *
     * @param data
     *
     * @return Command bean with the Shopping Cart response set.
     *
     * @throws Exception
     */
    public function postMerchantInit()
    {
        $request = new MerchantInitializationRequest([
            'OriginUrl' => $this->appData->originUrl,
            'OAuthToken' => $this->appData->requestToken
        ]);

        $this->appData->merchantInitResponse = $this->service->postMerchantInitData($request);

        return $this->appData;
    }

    public function postOpenFeed()
    {

        /**
         * multipload
         * https://api.mastercard.com/masterpasspsp/v6/onboarding/merchants/33090337
         */
        //$newUrl = sprintf("%s/%s", $this->appData->openFeedUrl, $this->appData->openFeedId);

        /**
         * single upload
         * https://api.mastercard.com//masterpasspsp/v6/checkoutproject/33090337/file
         */
        $newUrl = sprintf("%s/%d/file", $this->appData->openFeedSingleUrl, $this->appData->openFeedId);

        //$newUrl = 'https://api.mastercard.com/masterpasspsp/v6/checkoutproject/115334150/file/validation';

        $this->appData->openFeedProjectUrl = $newUrl;

        $message = utf8_encode($this->appData->openFeedMessage);
        $this->appData->openFeedRequest = $message;

        $this->appData->openFeedResponse = $this->service->postOpenFeed($newUrl, $message);

        return $this->appData;
    }

    public function postPreCheckoutData($longAccessToken)
    {
        # Create an instance of PrecheckoutDataRequest
        $dataRequest = new PrecheckoutDataRequest([
            'PairingDataTypes' => new PairingDataTypes([
                'PairingDataType' => [
                    new PairingDataType(['Type' => 'CARD']),
                    new PairingDataType(['Type' => 'ADDRESS']),
                    new PairingDataType(['Type' => 'REWARD_PROGRAM']),
                    new PairingDataType(['Type' => 'PROFILE'])
                ],
                    ])
        ]);
        
        $preCheckoutResponse = $this->service->getPreCheckoutData($dataRequest, $longAccessToken);
        $this->appData->preCheckoutResponse = $preCheckoutResponse;

        if ($preCheckoutResponse instanceof PrecheckoutDataResponse) {

//            $this->appData->preCheckoutCardId = $preCheckoutResponse->PrecheckoutData->Cards->Card->CardId;
//            $this->appData->preCheckoutShippingAddressId = $preCheckoutResponse->PrecheckoutData->ShippingAddresses->ShippingAddress->AddressId;
//            $this->appData->preCheckoutWalletId = (string) $preCheckoutResponse->PrecheckoutData->WalletId;
            $this->appData->longAccessToken = (string) $preCheckoutResponse->LongAccessToken;
            $this->appData->preCheckoutTransactionId = (string) $preCheckoutResponse->PrecheckoutData->PrecheckoutTransactionId;
            $this->appData->walletName = (string) $preCheckoutResponse->PrecheckoutData->WalletName;
            $this->appData->consumerWalletId = (string) $preCheckoutResponse->PrecheckoutData->ConsumerWalletId;
        }

        return $this->appData;
    }

    public function getCheckoutData()
    {
        $checkoutId = null;
        if (preg_match("/\/(\d+)$/", $this->appData->checkoutResourceUrl, $matches)) {
            $checkoutId = $matches[1];
        }

        $checkoutData = $this->service->getCheckoutData($checkoutId, $this->appData->accessToken);
        $this->appData->checkoutData = $checkoutData;
        $this->appData->transactionId = $checkoutId;

        return $this->appData;
    }

    public function postTransaction()
    {
        # Create an instance of MerchantTransactions
        $request = new MerchantTransactions([
            'MerchantTransactions' => new MerchantTransaction([
                'TransactionId' => $this->appData->transactionId,
                'PurchaseDate' => date(DATE_ATOM),
                'ExpressCheckoutIndicator' => false,
                'ApprovalCode' => MasterPassService::APPROVAL_CODE,
                'TransactionStatus' => 'Success',
                'OrderAmount' => 76239,
                'Currency' => 'USD',
                'ConsumerKey' => $this->service->getConsumerKey(),
                    ])
        ]);

        $this->appData->postTransactionResponse = $this->service->postTransaction($request);

        return $this->appData;
    }

    public function allHtmlEncode($str)
    {

        if (empty($str)) {
            return $str;
        } else {
            // get rid of existing entities else double-escape
            $str = html_entity_decode(stripslashes($str), ENT_QUOTES, Connector::UTF_8);
            $ar = preg_split('/(?<!^)(?!$)/u', $str);  // return array of every multi-byte character
            $str2 = '';
            foreach ($ar as $c) {
                $o = ord($c);
                if ((strlen($c) > 127) || /* multi-byte [unicode] */
                        ($o > 127)) /* Encodes everything above ascii 127 */ {
                    // convert to numeric entity
                    $c = mb_encode_numericentity($c, array(0x0, 0xffff, 0, 0xffff), Connector::UTF_8);
                }
                $str2 .= $c;
            }
            return $str2;
        }
    }

}
?>


