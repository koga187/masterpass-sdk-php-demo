<?php
require_once (dirname(__DIR__)) . '/src/controller/MasterPassController.php';

session_start();
$sad = unserialize($_SESSION['sad']);

$errorMessage = null;
$controller = new MasterPassController($sad);

$checkout = false;
if(isset($_GET['checkout'])) {
	$checkout = $_GET['checkout'] === "true";
} 

try {

    $sad = $controller->postMerchantInit();
} catch (SDKErrorResponseException $e) {

    $errorMessage = MasterPassHelper::formatError($e);
}

$_SESSION['sad'] = serialize($sad);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <title>MasterPass Pairing Flow</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="Content/Site.css">
        <script type="text/javascript" src="Scripts/jquery-1.5.1.js"></script>
        <script type="text/javascript" src="Scripts/common.js"></script>
        <script type="text/javascript" src="Scripts/tooltips/commonToolTips.js"></script>
        <script type="text/javascript" src="Scripts/tooltips/jquery-1.3.2.min.js"></script> <!-- Needed for tooltips only -->
        <script type="text/javascript" src="Scripts/tooltips/jquery.qtip-1.0.0-rc3.min.js"></script>
        <script type="text/javascript" src="<?php echo $sad->lightboxUrl ?>"></script>		

    </head>
    <body class="pairing">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>MasterPass Pairing Flow </h1>
                </div>
                <div id="logindisplay">
                    &nbsp;
                </div>
            </div>
            <div id="main">
                <h1>
                    Merchant Initialization Received
                </h1>
                <?php
                if ($errorMessage != null) {

                    echo '<h2>Error</h2>
	<div class = "error">
		<p>
		    The following error occurred while trying to get the Request Token from the MasterCard API.
		</p>
		<p>		
<pre>
<code>', $errorMessage,
                    '</code>
</pre>
		</p></div>';
                }
                ?>              
                <p>
                    This step sends the Merchants URL to MasterCard services for lighbox security.
                </p>

                <fieldset>
                    <legend>Sent:</legend>
                    <table>
                        <tr>
                            <th>
                                Authorization Header 
                            </th>
                            <td>                      
                                <code><?php //echo $controller->service->authHeader;  ?></code>
                            </td>
                        </tr> 
                        <tr>
                            <th>
                                Signature Base String 
                            </th>
                            <td>
                                <hr>
                                <code><?php //echo $controller->service->signatureBaseString;  ?></code>
                            </td>
                        </tr>  
                        <tr>
                            <th>
                                Merchant Initialization XML 
                            </th>
                            <td>
                                <pre><code><?php //echo MasterPassHelper::formatXML($sad->merchantInitRequest);  ?></code></pre>                            
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Sent To:</legend>
                    <table>                     
                        <tr>
                            <th>
                                Merchant Init URL 
                            </th>
                            <td>
                                <?php echo $sad->merchantInitUrl; ?>
                            </td>
                        </tr>

                    </table>  
                </fieldset>
                <fieldset>
                    <legend>Received:</legend>
                    <table>                     
                        <tr>
                        <tr>
                            <th>OAuthToken</th>
                            <td><?php echo $sad->merchantInitResponse->OAuthToken; ?></td>
                        </tr>
                    </table>
                </fieldset>      
                <h1>Configure Pairing Options</h1>
                <p>Select Data Types to be Paired with MasterPass</p>
                <fieldset>
                    <legend>Pairing Configuration</legend>
                    <form id="pairConfiguration" >
                        <table>
                            <tr><th>Pairing Data Types</th></tr>
                            <tr>

                                <td><input type="checkbox" onclick="handleUpdatePairConfiguration()" id="creditCardPairing" value="creditCard">Credit Card</td>
                                <td><input type="checkbox" onclick="handleUpdatePairConfiguration()" id="profilePairing" value="profile">Profile</td>
                                <td><input type="checkbox" onclick="handleUpdatePairConfiguration()" id="addressPairing" value="address">Address</td>
                                <td><input type="checkbox" onclick="handleUpdatePairConfiguration()" id="rewardsPairing" value="rewards">Rewards</td>
                            </tr>
                        </table>
                    </form>
                    <br>
                    <div id="buttonsDiv">

                        <?php if ($checkout): ?>
                            <div id="checkoutButtonDiv" onClick="handleConnectWithMasterPass()">
                                <a href="#">
                                    <img src="https://www.mastercard.com/mc_us/wallet/img/en/US/mcpp_wllt_btn_chk_147x034px.png" alt="Buy with MasterPass">
                                </a>
                            </div>
                            <div style="padding-bottom: 20px">
                                <a href="http://www.mastercard.com/mc_us/wallet/learnmore/en" target="_blank">Learn More</a>
                            </div>

                        <?php else: ?>
                            <div id="connectButtonDiv" onClick="handleConnectWithMasterPass()">
                                <a href="#">
                                    <img src="https://www.mastercard.com/mc_us/wallet/img/en/US/mp_connect_with_button_034px.png" alt="Connect with MasterPass">
                                </a>
                            </div>
                            <div style="padding-bottom: 20px">
                                <a href="http://www.mastercard.com/mc_us/wallet/learnmore/en" target="_blank">Learn More</a>
                            </div>
                        <?php endif; ?>


                    </div>
                    <div id="sampleCodeDiv">
                        <fieldset>
                            <legend>Javascript</legend>
                            <pre>
<code id="sampleCode">
</code>
                            </pre>
                        </fieldset>
                    </div>
                </fieldset>
                <script type="text/javascript" language="Javascript">
                    $(document).ready(function () {
                        $("#buttonsDiv").css("display", "none");
                        setCheckout();
                    });
                    var checkout = false;
                    var config = null;
                    var data = null;
                    var callbackPath = "<?php echo $sad->pairingCallbackUrl ?>";
                    var showRewards = <?php echo $sad->rewardsProgram ?> == true;
                    var locationParams = getJsonFromUrl();
                    var setCheckout = function () {
                        if (locationParams.checkout) {
                            console.log("checkout is true");
                            callbackPath = "<?php echo $sad->callbackUrl ?>"
                            checkout = true
                        }
                    }

                    function getJsonFromUrl() {
                        var query = location.search.substr(1);
                        var result = {};
                        query.split("&").forEach(function (part) {
                            var item = part.split("=");
                            result[item[0]] = decodeURIComponent(item[1]);
                        });
                        return result;
                    }


                    function handleConnectWithMasterPass() {
                        console.log("checkout: " + checkout);
                        console.log("data:");
                        console.log(data);
                        console.log(data.pairingDataTypes);
                        console.log(callbackPath)
                        console.log("request token: " + data.requestToken)
                        if (checkout) {
                            if (showRewards) {
                                MasterPass.client.checkout({
                                    "callbackUrl": callbackPath,
                                    "pairingRequestToken": "${data.pairingToken}",
                                    "requestToken": data.requestToken,
                                    "requestedDataTypes": data.pairingDataTypes,
                                    "merchantCheckoutId": "<?php echo $sad->checkoutIdentifier ?>",
                                    "allowedCardTypes": ["<?php echo $sad->acceptableCards ?>"],
                                    "suppressShippingAddressEnable": data.shippingSuppression,
                                    "loyaltyEnabled": "<?php echo $sad->rewardsProgram ?>",
                                    "allowedLoyaltyPrograms": ["<?php echo $sad->allowedLoyaltyPrograms ?>"],
                                    "requestPairing": true,
                                    "requestBasicCheckout": "<?php echo $sad->authLevelBasic ?>",
                                    "version": "v6"
                                });
                            } else {
                                MasterPass.client.checkout({
                                    "callbackUrl": callbackPath,
                                    "pairingRequestToken": "${data.pairingToken}",
                                    "requestToken": data.requestToken,
                                    "requestedDataTypes": data.pairingDataTypes,
                                    "merchantCheckoutId": "<?php echo $sad->checkoutIdentifier ?>",
                                    "allowedCardTypes": ["<?php echo $sad->acceptableCards ?>"],
                                    "suppressShippingAddressEnable": data.shippingSuppression,
                                    "loyaltyEnabled": "<?php echo $sad->rewardsProgram ?>",
                                    "requestPairing": true,
                                    "requestBasicCheckout": "<?php echo $sad->authLevelBasic ?>",
                                    "version": "v6"
                                });
                            }
                        } else {
                            MasterPass.client.connect({
                                "callbackUrl": callbackPath,
                                "pairingRequestToken": "<?php echo $sad->pairingToken ?>",
                                "requestedDataTypes": data.pairingDataTypes,
                                "merchantCheckoutId": "<?php echo $sad->checkoutIdentifier ?>",
                                "allowedCardTypes": ["<?php echo $sad->acceptableCards ?>"],
                                "suppressShippingAddressEnable": data.shippingSuppression,
                                "requestPairing": true,
                                "version": "v6"
                            });
                        }

                    }

                    function handleUpdatePairConfiguration() {
                        config = {
                            creditCardPairing: $('#creditCardPairing').is(':checked'),
                            profilePairing: $('#profilePairing').is(':checked'),
                            addressPairing: $('#addressPairing').is(':checked'),
                            rewardsPairing: $('#rewardsPairing').is(':checked')
                        };
                        console.log("callback path " + callbackPath);

                        var requestedDataTypes = [];

                        for (var prop in config) {
                            switch (prop) {
                                case "creditCardPairing":
                                    if (config[prop])
                                        requestedDataTypes.push("CARD");
                                    break;
                                case "profilePairing":
                                    if (config[prop])
                                        requestedDataTypes.push("PROFILE");
                                    break;
                                case "addressPairing":
                                    if (config[prop])
                                        requestedDataTypes.push("ADDRESS");
                                    break;
                                case "rewardsPairing":
                                    if (config[prop])
                                        requestedDataTypes.push("REWARD_PROGRAM");
                                    break;
                            }
                        }
                        $('#sampleCode').empty();
                        $('#sampleCodeDiv.legend').empty();
                        $('#pairConfiguration :input').attr("disabled", true);


                        $.post('PairingConfiguration.php', {dataTypes: requestedDataTypes.toString()}, function (dataString) {
                            if (dataString.length > 0)
                                data = eval('(' + dataString + ')');

                            console.log("data:");
                            console.log(data);

                            if (requestedDataTypes.length > 0) {
                                $("#buttonsDiv").css("display", "block");
                            } else {
                                $("#buttonsDiv").css("display", "none");
                            }
                            var sampleButtonString = "";
                            if (requestedDataTypes.length > 0) {
                                if (checkout) {

                                    if (showRewards) {
                                        sampleButtonString = 'MasterPass.client.checkout({\n\t"callbackUrl":' + callbackPath + ',\n\t"pairingRequestToken":"<?php echo $sad->pairingToken ?>",\n\t"requestedDataTypes":["' + requestedDataTypes.toString() + '"],\n\t"merchantCheckoutId":"<?php echo $sad->checkoutIdentifier ?>",\n\t"allowedCardTypes":["<?php echo $sad->acceptableCards ?>"],\n\t"suppressShippingAddressEnable": ' + data.shippingSuppression + ',\n\t"version":"v6",\n\t"loyaltyEnabled" : "<?php echo $sad->rewardsProgram ?>",\n\t"allowedLoyaltyPrograms" : "<?php echo $sad->allowedLoyaltyPrograms ?>",\n\t"requestBasicCheckout" : "<?php echo $sad->authLevelBasic ?>",\n\t"requestPairing":true \n});';
                                    } else {
                                        sampleButtonString = 'MasterPass.client.checkout({\n\t"callbackUrl":' + callbackPath + ',\n\t"pairingRequestToken":"<?php echo $sad->pairingToken ?>",\n\t"requestedDataTypes":["' + requestedDataTypes.toString() + '"],\n\t"merchantCheckoutId":"<?php echo $sad->checkoutIdentifier ?>",\n\t"allowedCardTypes":["<?php echo $sad->acceptableCards ?>"],\n\t"suppressShippingAddressEnable": ' + data.shippingSuppression + ',\n\t"version":"v6",\n\t"loyaltyEnabled" : "<?php echo $sad->rewardsProgram ?>",\n\t"requestBasicCheckout" : "<?php echo $sad->authLevelBasic ?>",\n\t"requestPairing":true \n});';
                                    }
                                } else {
                                    sampleButtonString = 'MasterPass.client.connect({\n\t"callbackUrl":' + callbackPath + ',\n\t"pairingRequestToken":"<?php echo $sad->pairingToken ?>",\n\t"requestedDataTypes":["' + requestedDataTypes.toString() + '"],\n\t"merchantCheckoutId":"<?php echo $sad->checkoutIdentifier ?>",\n\t"allowedCardTypes":["<?php echo $sad->acceptableCards ?>"],\n\t"suppressShippingAddressEnable": ' + data.shippingSuppression + ',\n\t"version":"v6",\n\t"requestPairing":true \n});';
                                }
                            }
                            $("#sampleCode").text(sampleButtonString);
                            $('#pairConfiguration :input').attr("disabled", false);
                        });

                    }


                </script> 



            </div>
            <div id="footer">
            </div>
        </div>


    </body>
</html>