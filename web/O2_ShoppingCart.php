<?php
require_once (dirname(__DIR__)) . '/src/checkout/controller/MasterPassController.php';

use MasterpassDemo\src\checkout\controller\MasterPassController;
use MasterpassDemo\src\checkout\controller\MasterPassHelper;
use MasterCardCoreSDK\Exception\SDKErrorResponseException;

session_start();
$sad = unserialize($_SESSION['sad']);
if ($sad->requestToken == null) {
    header("Location: ./");
}

$errorMessage = null;
$controller = new MasterPassController($sad);


try {

    $sad = $controller->postShoppingCart();
    $sad = $controller->postMerchantInit();
} catch (SDKErrorResponseException $e) {

    $errorMessage = MasterPassHelper::formatError($e);
}

$_SESSION['sad'] = serialize($sad);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <title>MasterPass Standard Flow</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="Content/Site.css">	
        <script type="text/javascript" src="Scripts/jquery-1.5.1.js"></script>
        <script type="text/javascript" src="Scripts/common.js"></script>
        <script type="text/javascript" src="Scripts/tooltips/commonToolTips.js"></script>
        <script type="text/javascript" src="Scripts/tooltips/jquery-1.3.2.min.js"></script> <!-- Needed for tooltips only -->
        <script type="text/javascript" src="Scripts/tooltips/jquery.qtip-1.0.0-rc3.min.js"></script>
        <script type="text/javascript" src="<?php echo $sad->lightboxUrl ?>"></script>	

    </head>
    <body class="standard">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>MasterPass Standard Flow</h1>
                </div>
                <div id="logindisplay">
                    &nbsp;
                </div>

            </div>
            <div id="main">
                <h1>Shopping Cart Data Submitted</h1>
                <?php if ($errorMessage != null): ?>
                    <h2>Error</h2>
                    <div class = "error">
                        <p>The following error occurred while trying to get the Request Token from the MasterCard API.</p>
                        <p><pre><code><?php echo $errorMessage; ?></code></pre></p>
                    </div>
                <?php endif; ?>
                <fieldset>
                    <legend>Cart Response</legend>
                    <table>                     
                        <tr>
                        <tr>
                            <th>OAuthToken</th>
                            <td><?php echo $sad->shoppingCartResponse->OAuthToken; ?></td>
                        </tr>
                    </table>
                </fieldset>
                <br />
                <h1>Merchant Initialization Received</h1>
                <fieldset>
                    <legend>Initialization Response</legend>
                    <table>                     
                        <tr>
                        <tr>
                            <th>OAuthToken</th>
                            <td><?php echo $sad->merchantInitResponse->OAuthToken; ?></td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Standard Checkout</legend>
                    <br/>
                    <div id="checkoutButtonDiv" onClick="handleBuyWithMasterPass()">
                        <a href="#">
                            <img src="https://www.mastercard.com/mc_us/wallet/img/en/US/mcpp_wllt_btn_chk_147x034px.png" alt="Buy with MasterPass">
                        </a>
                    </div>
                    <div style="padding-bottom: 20px">
                        <a href="http://www.mastercard.com/mc_us/wallet/learnmore/en" target="_blank">Learn More</a>
                    </div>
                    <div>
                        <fieldset>
                            <legend>Javascript</legend>
                            <pre><code id="sampleCode"></code></pre>
						</fieldset>
					</div>
				</fieldset>
               
                <script type="text/javascript">

                    $(document).ready(function () {
                        console.log("document ready");

                        var sampleCodeString = "";
                        sampleCodeString = 'MasterPass.client.checkout({\n\t"requestToken":<?php echo $sad->requestToken ?>,\n\t"callbackUrl":<?php echo $sad->callbackUrl ?>,\n\t"merchantCheckoutId":<?php echo $sad->checkoutIdentifier ?>,\n\t"allowedCardTypes":<?php echo $sad->acceptableCards ?>,\n\t"cancelCallback":<?php echo $sad->callbackDomain ?>,\n\t"suppressShippingAddressEnable":<?php echo $sad->shippingSuppression ?>,\n\t"loyaltyEnabled":<?php echo $sad->rewardsProgram ?>,\n\t"requestBasicCheckout" : "<?php echo $sad->authLevelBasic ?>",\n\t"version":"v6"\n});';

                        $("#sampleCode").text(sampleCodeString);
                    });

                    $('#pairingCheckout').click(function (event) {
                        $("#pairingCheckoutForm").attr("action", "P1_Pairing.php?checkout=true");
                        $("#pairingCheckoutForm").submit();
                    });

                    function handleBuyWithMasterPass() {

                        MasterPass.client.checkout({
                            "requestToken": "<?php echo $sad->requestToken ?>",
                            "callbackUrl": "<?php echo $sad->callbackUrl ?>",
                            "merchantCheckoutId": "<?php echo $sad->checkoutIdentifier ?>",
                            "allowedCardTypes": "<?php echo $sad->acceptableCards ?>",
                            "cancelCallback": "<?php echo $sad->callbackDomain ?>",
                            "suppressShippingAddressEnable": "<?php echo $sad->shippingSuppression ?>",
                            "loyaltyEnabled": "<?php echo $sad->rewardsProgram ?>",
                            "requestBasicCheckout": "<?php echo $sad->authLevelBasic ?>",
                            "version": "v6"
                        });

                    }


                </script>
                
            </div>
        </div>
    </body>
</html>
