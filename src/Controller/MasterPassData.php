<?php

class MasterPassData
{
	
	// URLs
	public $requestUrl;
	public $shoppingCartUrl;
	public $accessUrl;
	public $postbackUrl;
	public $preCheckoutUrl;
	public $merchantInitUrl;
	public $lightboxUrl;
	public $checkoutResourceUrl;
	
	public $pairingCallbackUrl;
	public $pairingCallbackPath;
	public $cartCallbackUrl;
	public $cartCallbackPath;
	public $connectedCallbackUrl;
	public $connectedCallbackPath;
	
	public $callbackUrl;
	public $appBaseUrl;
	public $contextPath;
	
	public $consumerKey;
	public $checkoutIdentifier;
	public $keystorePassword;
	public $realm;
	public $keystorePath;
	public $callbackDomain;
	public $callbackPath;
	public $originUrl;
	public $secondOriginUrl;

	public $acceptableCards;
	public $xmlVersion;
	public $shippingSuppression;
	public $shippingProfile;
	public $iframeCall;
	public $rewardsProgram;
	public $walletSelector;
	public $profileName;
	public $shippingLocationProfile;
	public $authLevelBasic;
	public $flow;
	public $oAuthSecret;
	public $accessToken;
	public $accessTokenResponse;
	public $longAccessToken;
	public $longAccessTokenResponse;
	public $requestToken;
	public $requestVerifier;
	public $requestTokenResponse;
	public $pairingToken;
	public $pairingTokenResponse;
	public $pairingVerifier;
	public $shoppingCartRequest;
	public $shoppingCartResponse;
	public $merchantInitRequest;
	public $merchantInitResponse;
	public $checkoutData;
	public $transactionId;
	public $walletName;
	public $consumerWalletId;
	
	// precheckout
	public $preCheckoutRequest;
	public $preCheckoutResponse;
	public $preCheckoutTransactionId;
	public $preCheckoutCardId;
	public $preCheckoutShippingAddressId;
	public $preCheckoutWalletId;
	
	public $pairingDataTypes;
	
	public $postTransactionRequest;
	public $postTransactionResponse;
	
	// open feed
	public $openFeedUrl;
	public $openFeedSingleUrl;
	public $openFeedProjectUrl = null;
	public $openFeedId = null;
	public $openFeedMessage = null;
	public $openFeedRequest = null;
	public $openFeedResponse = null;
	
	public $tax = 348;
	public $shipping = 895;
	
	//const variables used for configs
	const RESOURCES_PATH = "resources/";
	const PROFILE_PATH = "profiles/";
// 	const DEFAULT_PROFILE = "Stage-Profile";
//	const DEFAULT_PROFILE = "MTF-Profile";
 	const DEFAULT_PROFILE = "Production-Profile";
 	//const DEFAULT_PROFILE = "Sandbox-Profile";
	//const DEFAULT_PROFILE = "Open-Feed";
 	//const DEFAULT_PROFILE = "3ds";
	const CONFIG_SUFFIX = ".ini";
	const PERIOD = '.';
	
	public function __construct()
	{
		// The constructor can accept one parameter - the path to the config file
		if(func_num_args() == 1)
		{
			$parameters = func_get_args();
			$profileConfigFile = $parameters[0];
		}
		// If the one parameter is not provided, this is the default path
		else
		{
			$profileConfigFile = dirname(__DIR__) . "/../web/" .
								MasterPassData::RESOURCES_PATH . 
								MasterPassData::PROFILE_PATH . 
								MasterPassData::DEFAULT_PROFILE . 
								MasterPassData::CONFIG_SUFFIX;
		}
		
		// Parsing the config.ini file
		$settings = parse_ini_file($profileConfigFile);

		// Setting up the callback path
		$break = explode('/', $_SERVER['REQUEST_URI']);
		$this->callbackPath = $break[1].$settings['callbackpath'];
		$this->pairingCallbackPath = $break[1].$settings['pairingcallbackpath'];
		$this->cartCallbackPath = $break[1].$settings['cartcallbackpath'];
		$this->connectedCallbackPath = $break[1].$settings['connectedcallbackpath'];
		
		$this->requestUrl = $settings['requesturl'];
		$this->shoppingCartUrl = $settings['shoppingcarturl'];
		$this->accessUrl = $settings['accessurl'];
		$this->postbackUrl = $settings['postbackurl'];
		
		$this->preCheckoutUrl = $settings['precheckouturl'];
		$this->merchantInitUrl = $settings['merchantiniturl'];
		
		$this->openFeedUrl = $settings['openfeedurl'];
		$this->openFeedSingleUrl = $settings['openfeedsingleurl'];
		
		$this->consumerKey = $settings['consumerkey'];
		$this->checkoutIdentifier = $settings['checkoutidentifier'];
		$this->keystorePassword = $settings['keystorepassword'];
		$this->keystorePath = $settings['keystorepath'];
		$this->callbackDomain = $settings['callbackdomain'];
		$this->originUrl = $settings['callbackdomain'];
		$this->secondOriginUrl = $settings['callbackdomain'].$settings['secondoriginpath'];
		$this->allowedLoyaltyPrograms = $settings['allowedloyaltyprograms'];
		
		$this->callbackUrl = $this->callbackDomain.$this->callbackPath;
		$this->pairingCallbackUrl = $this->callbackDomain.$this->pairingCallbackPath;
		$this->cartCallbackUrl = $this->callbackDomain.$this->cartCallbackPath;
		$this->connectedCallbackUrl = $this->callbackDomain.$this->connectedCallbackPath;
		
		$this->lightboxUrl = $settings['lightboxurl'];
		
	}
	
}
