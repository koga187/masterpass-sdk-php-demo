<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';
require_once dirname(__DIR__) . '/../vendor/masterpass/mpasscoresdk/MasterCardCoreSDK.phar';
require_once dirname(__DIR__) . '/../vendor/masterpass/masterpassmerchantsdk/MasterCardMasterPassMerchant.phar';

Logger::configure(dirname(__DIR__) .'/services/config.php');

class MasterPassService
{

    public $originUrl;
    protected $consumerKey;
    private $privateKey;

    public function __construct($consumerKey, $privateKey, $originUrl)
    {
        $this->originUrl = $originUrl;
        $this->consumerKey = $consumerKey;
        $this->privateKey = $privateKey;

        MasterCardApiConfig::$consumerKey = $consumerKey;
        MasterCardApiConfig::$privateKey = $privateKey;
        MasterCardApiConfig::setSandBox(true); // For sandbox environment else
    }

    public function getConsumerKey()
    {
        return $this->consumerKey;
    }
    
    /**
     * SDK:
     * Get the user's request token and store it in the current user session.
     * @param $requestUrl
     * @param $callbackUrl
     * @return RequestTokenResponse
     */
    public function getRequestToken($callbackUrl)
    {   
        return RequestTokenApi::create($callbackUrl);
    }

    /**
     * This method posts the Shopping Cart data to MasterCard services
     * and is used to display the shopping cart in the wallet site.
     * 
     * @param ShoppingCartRequest $request
     * 
     * @return ShoppingCartResponse
     */
    public function postShoppingCartData(ShoppingCartRequest $request)
    {
        return ShoppingcartApi::create($request);
    }
    
    /**
     * Merchant initialization
     * 
     * @param MerchantInitializationRequest $merchantInitializationRequest
     * 
     * @return MerchantInitializationResponse
     */
    public function postMerchantInitData(MerchantInitializationRequest $merchantInitializationRequest)
    {   
        #Call merchant initialization service api
        return MerchantinitializationApi::create($merchantInitializationRequest);
    }
    
    /**
     * 
     * SDK:
     * This method captures the Checkout Resource URL and Request Token Verifier
     * and uses these to request the Access Token.
     * @param $requestToken
     * @param $verifier
     * @return Output is Access Token
     */
    public function getAccessToken($accessUrl, $requestToken, $verifier)
    {
        $params = array(
            MasterPassService::OAUTH_VERIFIER => $verifier,
            MasterPassService::OAUTH_TOKEN => $requestToken
        );

        $return = new AccessTokenResponse();
        $response = $this->doRequest($params, $accessUrl, Connector::POST, null);
        $responseObject = $this->parseConnectionResponse($response);

        $return->accessToken = isset($responseObject[MasterPassService::OAUTH_TOKEN]) ? $responseObject[MasterPassService::OAUTH_TOKEN] : "";
        $return->oAuthSecret = isset($responseObject[MasterPassService::OAUTH_TOKEN]) ? $responseObject[MasterPassService::OAUTH_TOKEN_SECRET] : "";
        return $return;
    }

    public function postOpenFeed($openFeedUrl, $openFeedXml)
    {
        $response = $this->doRequest(array(), $openFeedUrl, Connector::POST, $openFeedXml);

        return $response;
    }

    /**
     * This method submits the receipt transaction list to MasterCard as a final step
     * in the Wallet process.
     * @param $merchantTransactions
     * @return Output is the response from MasterCard services
     */
    public function PostCheckoutTransaction($postbackurl, $merchantTransactions)
    {
        $params = array(
            Connector::OAUTH_BODY_HASH => $this->generateBodyHash($merchantTransactions)
        );

        $response = $this->doRequest($params, $postbackurl, Connector::POST, $merchantTransactions);

        return $response;
    }

    public function getPreCheckoutData($preCheckoutUrl, $preCheckoutXml, $accessToken)
    {
        $params = array(
            MasterPassService::OAUTH_TOKEN => $accessToken
        );
        $response = $this->doRequest($params, $preCheckoutUrl, Connector::POST, $preCheckoutXml);
        return $response;
    }
    
    

    /**
     * SDK:
     * Assuming that all due diligence is done and assuming the presence of an established session,
     * successful reception of non-empty request token, and absence of any unanticipated
     * exceptions have been successfully verified, you are ready to go to the authorization
     * link hosted by MasterCard.
     * @param $acceptableCards
     * @param $checkoutProjectId
     * @param $xmlVersion
     * @param $shippingSuppression
     * @param $rewardsProgram
     * @param $authLevelBasic
     * @param $shippingLocationProfile
     * @param $walletSelector
     *
     * @return string - URL to redirect the user to the MasterPass wallet site
     */
    private function GetConsumerSignInUrl($acceptableCards, $checkoutProjectId, $xmlVersion, $shippingSuppression, $rewardsProgram, $authLevelBasic, $shippingLocationProfile, $walletSelector)
    {
        $baseAuthUrl = $this->requestTokenInfo->authorizeUrl;

        $xmlVersion = strtolower($xmlVersion);

        // Use v1 if xmlVersion does not match correct patern
        if (!preg_match(MasterPassService::XML_VERSION_REGEX, $xmlVersion)) {
            $xmlVersion = MasterPassService::DEFAULT_XMLVERSION;
        }

        $token = $this->requestTokenInfo->requestToken;
        if ($token == null || $token == Connector::EMPTY_STRING) {
            throw new Exception(Connector::EMPTY_REQUEST_TOKEN_ERROR_MESSAGE);
        }

        if ($baseAuthUrl == null || $baseAuthUrl == Connector::EMPTY_STRING) {
            throw new Exception(Connector::INVALID_AUTH_URL);
        }

        // construct the Redirect URL
        $finalAuthUrl = $baseAuthUrl .
            $this->getParamString(MasterPassService::ACCEPTABLE_CARDS, $acceptableCards, true) .
            $this->getParamString(MasterPassService::CHECKOUT_IDENTIFIER, $checkoutProjectId) .
            $this->getParamString(MasterPassService::OAUTH_TOKEN, $token) .
            $this->getParamString(MasterPassService::VERSION, $xmlVersion);

        // If xmlVersion is v1 (default version), then shipping suppression, rewardsprogram and auth_level are not used
        if (strcasecmp($xmlVersion, MasterPassService::DEFAULT_XMLVERSION) != Connector::V1) {

            if ($shippingSuppression == 'true') {
                $finalAuthUrl = $finalAuthUrl . $this->getParamString(MasterPassService::SUPPRESS_SHIPPING_ADDRESS, $shippingSuppression);
            }

            if ((int) substr($xmlVersion, 1) >= 4 && $rewardsProgram == 'true') {
                $finalAuthUrl = $finalAuthUrl . $this->getParamString(MasterPassService::ACCEPT_REWARDS_PROGRAM, $rewardsProgram);
            }

            if ($authLevelBasic) {
                $finalAuthUrl = $finalAuthUrl . $this->getParamString(MasterPassService::AUTH_LEVEL, MasterPassService::BASIC);
            }

            if ((int) substr($xmlVersion, 1) >= 4 && $shippingLocationProfile != null && !empty($shippingLocationProfile)) {
                $finalAuthUrl = $finalAuthUrl . $this->getParamString(MasterPassService::SHIPPING_LOCATION_PROFILE, $shippingLocationProfile);
            }

            if ((int) substr($xmlVersion, 1) >= 5 && $walletSelector == 'true') {
                $finalAuthUrl = $finalAuthUrl . $this->getParamString(MasterPassService::WALLET_SELECTOR, $walletSelector);
            }
        }
        return $finalAuthUrl;
    }

    /**
     * SDK:
     * Method to create the URL with GET Parameters
     *
     * @param $key
     * @param $value
     * @param $firstParam
     *
     * @return string
     */
    private function getParamString($key, $value, $firstParam = false)
    {
        $paramString = Connector::EMPTY_STRING;

        if ($firstParam) {
            $paramString .= Connector::QUESTION;
        } else {
            $paramString .= Connector::AMP;
        }
        $paramString .= $key . Connector::EQUALS . $value;

        return $paramString;
    }

}

?>
