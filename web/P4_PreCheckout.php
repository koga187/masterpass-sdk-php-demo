<?php
require_once (dirname(__DIR__)) . '/src/checkout/controller/MasterPassController.php';

session_start();
$sad = unserialize($_SESSION['sad']);
$controller = new MasterPassController($sad);

$errorMessage = null;
if (isset($_GET["error"])) {
    $errorMessage = '';
}

$longAccessToken = $_COOKIE['longAccessToken'];

try {
    
    $sad = $controller->postPreCheckoutData($longAccessToken);

    setcookie('longAccessToken', $sad->longAccessToken, time() + (60 * 60 * 24 * 7));

    $sad = $controller->getRequestToken();
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
        <title>MasterPass Connect Pairing Checkout Flow</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="Content/Site.css">
        <script type="text/javascript" src="Scripts/jquery-1.5.1.js"></script>
        <script type="text/javascript" src="Scripts/common.js"></script>
        <script type="text/javascript" src="https://jquery-xml2json-plugin.googlecode.com/svn/trunk/jquery.xml2json.js"></script>  
        <script type="text/javascript" src="<?php echo $sad->lightboxUrl ?>"></script>
        <script type="text/javascript" src="https://sandbox.masterpass.com/lightbox/Switch/assets/js/MasterPass.omniture.js"></script>

    </head>
    <body class="pairing">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>MasterPass Connect Pairing Checkout Flow</h1>
                </div>
                <div id="logindisplay">&nbsp;</div>

            </div>
            <div id="main">
                <h1>Retrieved Pre Checkout XML</h1>
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
                <p>Once a Access Token is gained, request the user protected
                    resources (shipping and/or billing information)</p>
                <fieldset>
                    <legend>Sent</legend>
                    <table>
                        <tr>
                            <th>
                                Authorization Header 
                            </th>
                            <td>
                                <code><?php //echo $controller->service->authHeader;     ?></code>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Signature Base String 
                            </th>
                            <td>
                                <hr>
                                <code><?php //echo $controller->service->signatureBaseString;     ?></code>
                            </td>
                        </tr>
                        <tr>
                            <th> PreCheckout XML </th>
                            <td>
                                <pre>                        
<code>                        
                                        <?php //echo MasterPassHelper::formatXML($sad->preCheckoutRequest);  ?>
</code>
                                </pre>                            
                            </td>
                        </tr>  
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Sent To:</legend>
                    <table>                     
                        <tr>
                            <th> Pre Checkout URL </th>
                            <td>
                                <?php echo $sad->preCheckoutUrl;  ?>
                            </td>
                        </tr>

                    </table>  
                </fieldset>
                <fieldset>
                    <legend>Received:</legend>           

                    <table>                     
                        <tr>
                            <th>
                                Pre Checkout Response 
                            </th>
                            <td>
                                <pre>                        
<code>                        
                                        <?php //echo MasterPassHelper::formatXML($sad->preCheckoutResponse);  ?>
</code>
                                </pre>                           
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <h1>
                    Received Request Token
                </h1>

                <?php if ($errorMessage != null): ?>
                    <h2>Error</h2>
                    <div class = "error">
                        <p>
                            The following error occurred while trying to get the Request Token from the MasterCard API.
                        </p>
                        <p>
                        <pre>
                <code><?php echo $errorMessage ?></code>
                        </pre>
                        </p></div>
                <?php endif; ?>

                <p>
                    Use the following Request Token to call subsequent MasterPass services.
                </p>

                <fieldset>
                    <legend>Sent</legend>
                    <table>
                        <tr>
                            <th>
                                Authorization Header 
                            </th>
                            <td >                      
                                <code><?php //echo $controller->service->authHeader     ?></code>

                            </td>
                        </tr> 
                        <tr>
                            <th>
                                Signature Base String 
                            </th>
                            <td >
                                <hr>
                                <code><?php //echo $controller->service->signatureBaseString     ?></code>
                            </td>
                        </tr>  
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Sent to:</legend>          		
                    <table>                     
                        <tr>
                            <th>
                                Request Token URL  
                            </th>
                            <td>
                                <code><?php echo $sad->requestUrl ?></code>
                            </td>
                        </tr>

                    </table>  
                </fieldset>

                <fieldset>
                    <legend>Received</legend>  
                    <table>                     
                        <tr>
                            <th>
                                Request Token 
                            </th>
                            <td>
                                <?php echo $sad->requestToken ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Authorize URL</th>
                            <td><?php echo $sad->requestTokenResponse->XoauthRequestAuthUrl; ?></td>
                        </tr>
                        <tr>
                            <th>Expires in</th>
                            <td><?php echo $sad->requestTokenResponse->OauthExpiresIn; ?><?php if ($sad->requestTokenResponse->OauthExpiresIn != null) echo ' Seconds' ?></td>
                        </tr>
                        <tr>
                            <th>Oauth Secret</th>
                            <td><?php echo $sad->requestTokenResponse->OauthTokenSecret; ?></td>
                        </tr>
                    </table>

                </fieldset>
                <h1>
                    Shopping Cart Data Submitted
                </h1>

                <?php if ($errorMessage != null): ?>
                    <h2>Error</h2>
                    <div class = "error">
                        <p>
                            The following error occurred while trying to get the Request Token from the MasterCard API.
                        </p>
                        <p>
                        <pre>
                <code><?php echo $errorMessage ?></code>
                        </pre>
                        </p></div>
                <?php endif; ?>

                <p>
                    This step sends the Merchants shopping cart data to MasterCard services for display in the Wallet.
                </p>

                <fieldset>
                    <legend>Sent:</legend>
                    <table>
                        <tr>
                            <th>Authorization Header </th>
                            <td>                      
                                <code><?php //echo $controller->service->authHeader     ?></code>
                            </td>
                        </tr> 
                        <tr>
                            <th>Signature Base String </th>
                            <td>
                                <hr>
                                <code><?php //echo $controller->service->signatureBaseString     ?></code>
                            </td>
                        </tr>  
                        <tr>
                            <th>Shopping Cart XML </th>
                            <td>
                                <pre><code><?php //echo MasterPassHelper::formatXML($sad->shoppingCartRequest)    ?></code></pre>                            
                            </td>
                        </tr>  
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Sent To:</legend>
                    <table>                     
                        <tr>
                            <th>Shopping Cart URL </th>
                            <td><?php echo $sad->shoppingCartUrl ?></td>
                        </tr>

                    </table>  
                </fieldset>
                <fieldset>
                    <legend>Received:</legend>           

                    <table>                     
                        <tr>
                            <th>
                                Shopping Cart Response 
                            </th>
                            <td>
                                <pre>                        
<code>                       
                                        <?php //echo MasterPassHelper::formatXML($sad->shoppingCartResponse) ?>
</code>
                                </pre>                           
                            </td>
                        </tr>

                    </table>
                </fieldset>



                <fieldset>
                    <legend>Paired Data</legend>
                    <table id="pairedData"></table>
                </fieldset>
                <div id="checkoutButtonDiv" onClick="handleCheckoutWithMasterpass()">
                    <a href="#">
                        <img src="https://www.mastercard.com/mc_us/wallet/img/en/US/mcpp_wllt_btn_chk_147x034px.png" alt="Buy with MasterPass">
                    </a>
                </div>
                <div style="padding-bottom: 20px">
                    <a href="http://www.mastercard.com/mc_us/wallet/learnmore/en" target="_blank">Learn More</a>
                </div>
                <div id="buttonJavaScriptDiv">
                    <fieldset>
                        <legend>Button Javascript</legend>
                        <pre><code id="sampleCode"></code></pre>
            	</fieldset>
            </div>
			</div>
			
		</div>
			
			<script type="text/javascript" language="Javascript">

            var json = <?php echo json_encode($sad->preCheckoutResponse) ?>;
            var preCheckout = json.PrecheckoutData;
            var supressShipping = <?php echo $sad->shippingSuppression ?>;
            var showRewards = <?php echo $sad->rewardsProgram ?>;
            var addressId = $("#addressSelect option:selected").val();
            var cardId = $("#cardSelect option:selected").val();
            var rewardId = $("#rewardSelect option:selected").val();

            console.log(preCheckout);

            if (preCheckout.Contact != null)
                $("<tr><th>Profile:  </th></tr>)").append(generateContactSelect(preCheckout.Contact)).appendTo('#pairedData');
            
            if ((preCheckout.PrecheckoutCards != null) && (preCheckout.PrecheckoutCards.Card != null)) {
                $("<tr><th>Card:  </th></tr>)").append(generateCardSelect(preCheckout.PrecheckoutCards.Card)).appendTo("#pairedData");
            }
            
            
            if ((preCheckout.PrecheckoutShippingAddresses != null) && (preCheckout.PrecheckoutShippingAddresses.ShippingAddress != null)) {
                $("<tr><th>Address:  </th></tr>)").append(generateAddressSelect(preCheckout.PrecheckoutShippingAddresses.ShippingAddress)).appendTo('#pairedData');
            } else {
                supressShipping = true;
            }
            
            if (preCheckout.RewardPrograms != null) {
                $("<tr><th>Rewards:  </th></tr>)").append(generateContactSelect(preCheckout.Contact)).appendTo('#pairedData');
            }

            var precheckoutTransactionId = preCheckout.PrecheckoutTransactionId;

            function generateCardSelect(cards) {
                var cardSelect = $("<select id='cardSelect'/>").change(function () {
                    console.log("card changed");
                    updateCheckoutButton();
                });
                switch (true) {
                    case Object.prototype.toString.call(cards) == "[object Array]":
                        console.log("cards is array");
                        for (var card in cards) {
                            console.log(cards[card]);
                            generateCardOption(cards[card]).appendTo(cardSelect);
                        }
                        break;
                    case Object.prototype.toString.call(cards) == "[object Object]":
                        console.log("cards is object");
                        generateCardOption(cards).appendTo(cardSelect);
                        break;
                }
                return cardSelect;
            }

            function generateCardOption(card) {
                option = $('<option/>', {text: card.BrandName + " - " + card.LastFour}).val(card.CardId);
                return option;
            }

            function generateAddressSelect(addresses) {
                var addressSelect = $("<select id='addressSelect'/>").change(function () {
                    console.log("address changed");
                    updateCheckoutButton();
                });
                console.log(addresses);
                switch (true) {
                    case Object.prototype.toString.call(addresses) == "[object Array]":
                        console.log("Address is Array");
                        for (var address in addresses) {
                            generateAddressOption(addresses[address]).appendTo(addressSelect);
                        }
                        break;
                    case Object.prototype.toString.call(addresses) == "[object Object]":
                        console.log("Address is Object");
                        generateAddressOption(addresses).appendTo(addressSelect);
                        break;
                }
                return addressSelect;
            }

            function generateAddressOption(address) {
                console.log("Creating option: " + address.Line1);
                option = $('<option/>', {text: address.Country + " : " + address.Line1}).val(address.AddressId);
                return option;
            }

            function generateContactSelect(contact) {
                return $("<p>" + contact.FirstName + " " + contact.LastName + "</p>");
            }

            function generateRewardSelect(rewards) {
                var rewardsSelect = $("<select id='rewardsSelect'/>").change(function () {
                    console.log("reward changed");
                    updateCheckoutButton();
                });
                switch (true) {
                    case Object.prototype.toString.call(rewards) == "[object Array]":
                        for (var reward in rewards) {
                            generateRewardOption(reward).appendTo(rewardsSelect);
                        }
                        break;
                    case Object.prototype.toString.call(rewards) == "[object Object]":
                        generateRewardOption(rewards).appendTo(rewardsSelect);
                        break;
                }
                return rewardsSelect;
            }

            function generateRewardOption(reward) {
                option = $('<option/>', {text: reward.Name}).val(reward.RewardId);
                option.click(updateCheckoutButton);
                return option;
            }

            function handleCheckoutWithMasterpass() {
                console.log("handling checkout")
                addressId = $("#addressSelect option:selected").val();
                cardId = $("#cardSelect option:selected").val();
                rewardId = $("#rewardSelect option:selected").val();
                console.log("the selected address id is: " + addressId);
                console.log("the selected card id is: " + cardId);

                if (showRewards) {
                    MasterPass.client.checkout({
                        "requestToken": "<?php echo $sad->requestToken ?>",
                        "callbackUrl": "<?php echo $sad->callbackUrl ?>",
                        "merchantCheckoutId": "<?php echo $sad->checkoutIdentifier ?>",
                        "allowedCardTypes": ["<?php echo $sad->acceptableCards ?>"],
                        "cardId": cardId,
                        "shippingId": addressId,
                        "precheckoutTransactionId": precheckoutTransactionId,
                        "suppressShippingAddressEnable": supressShipping,
                        "requestBasicCheckout": "<?php echo $sad->authLevelBasic ?>",
                        "walletName": "<?php echo $sad->walletName ?>",
                        "consumerWalletId": "<?php echo $sad->consumerWalletId ?>",
                        "loyaltyEnabled": "<?php echo $sad->rewardsProgram ?>",
                        "allowedLoyaltyPrograms": ["<?php echo $sad->allowedLoyaltyPrograms ?>"],
                        "version": "v6"
                    }
                    );
                } else {
                    MasterPass.client.checkout({
                        "requestToken": "<?php echo $sad->requestToken ?>",
                        "callbackUrl": "<?php echo $sad->callbackUrl ?>",
                        "merchantCheckoutId": "<?php echo $sad->checkoutIdentifier ?>",
                        "allowedCardTypes": ["<?php echo $sad->acceptableCards ?>"],
                        "cardId": cardId,
                        "shippingId": addressId,
                        "precheckoutTransactionId": precheckoutTransactionId,
                        "suppressShippingAddressEnable": supressShipping,
                        "requestBasicCheckout": "<?php echo $sad->authLevelBasic ?>",
                        "walletName": "<?php echo $sad->walletName ?>",
                        "consumerWalletId": "<?php echo $sad->consumerWalletId ?>",
                        "loyaltyEnabled": "<?php echo $sad->rewardsProgram ?>",
                        "version": "v6"
                    });
                }

            }

            function updateCheckoutButton() {
                addressId = $("#addressSelect option:selected").val();
                cardId = $("#cardSelect option:selected").val();
                rewardId = $("#rewardSelect option:selected").val();
                console.log("the selected address id is: " + addressId);


                $("#checkoutButtonDiv").css({display: "block"});

                var sampleButtonString = "";
                if (showRewards) {
                    sampleButtonString = 'MasterPass.client.checkout({\n\t"element":"checkoutButtonDiv",\n\t"requestToken":"<?php echo $sad->requestToken ?>",\n\t"callbackUrl":"<?php echo $sad->callbackUrl ?>",\n\t"merchantCheckoutId":"<?php echo $sad->checkoutIdentifier ?>",\n\t"allowedCardTypes":["<?php echo $sad->acceptableCards ?>"],\n\t"cardId":' + cardId + ',\n\t"shippingId":' + addressId + ',\n\t"precheckoutTransactionId":' + precheckoutTransactionId + ',\n\t"suppressShippingAddressEnable": ' + supressShipping + ',\n\t"requestBasicCheckout" : "<?php echo $sad->authLevelBasic ?>",\n\t"walletName" : "<?php echo $sad->walletName ?>",\n\t"consumerWalletId" : "<?php echo $sad->consumerWalletId ?>",\n\t"loyaltyEnabled" : "<?php echo $sad->rewardsProgram ?>",\n\t"allowedLoyaltyPrograms" : "<?php echo $sad->allowedLoyaltyPrograms ?>",\n\t"version": "v6"\n});'
                } else {
                    sampleButtonString = 'MasterPass.client.checkout({\n\t"element":"checkoutButtonDiv",\n\t"requestToken":"<?php echo $sad->requestToken ?>",\n\t"callbackUrl":"<?php echo $sad->callbackUrl ?>",\n\t"merchantCheckoutId":"<?php echo $sad->checkoutIdentifier ?>",\n\t"allowedCardTypes":["<?php echo $sad->acceptableCards ?>"],\n\t"cardId":' + cardId + ',\n\t"shippingId":' + addressId + ',\n\t"precheckoutTransactionId":' + precheckoutTransactionId + ',\n\t"suppressShippingAddressEnable": ' + supressShipping + ',\n\t"requestBasicCheckout" : "<?php echo $sad->authLevelBasic ?>",\n\t"walletName" : "<?php echo $sad->walletName ?>",\n\t"consumerWalletId" : "<?php echo $sad->consumerWalletId ?>",\n\t"loyaltyEnabled" : "<?php echo $sad->rewardsProgram ?>",\n\t"version": "v6"\n});'
                }
                $("#sampleCode").text(sampleButtonString);


            }
            updateCheckoutButton();


</script>
</body>
</html>