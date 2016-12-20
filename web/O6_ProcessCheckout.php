<?php
require_once (dirname(__DIR__)) . '/src/checkout/controller/MasterPassController.php';

session_start();
$sad = unserialize($_SESSION['sad']);

$errorMessage = $checkoutObject = null;
$controller = new MasterPassController($sad);

try {

    $sad = $controller->getCheckoutData();
    $checkoutObject = $sad->checkoutData;
} catch (SDKErrorResponseException $e) {

    $errorMessage = MasterPassHelper::formatError($e);
}

$_SESSION['sad'] = serialize($sad);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <title>MasterPass Standard Checkout Flow</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="Content/Site.css">
    </head>
    <body class="postCheckout">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>MasterPass Standard Checkout Flow</h1>
                </div>
                <div id="logindisplay">&nbsp;</div>

            </div>
            <div id="main">
                <h1>Retrieved Checkout XML</h1>
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
			</p>
			</div>';
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
                                <code><?php // echo $controller->service->signatureBaseString;     ?></code>
                            </td>
                        </tr>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Sent To:</legend>
                    <table>
                        <tr>
                            <th>
                                Checkout Resource URL 
                            </th>
                            <td>
                                <?php echo $sad->checkoutResourceUrl; ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Received:</legend>
                    <table>
                        <tr>
                            <th>
                                Checkout XML 
                            </th>
                            <td>
                                <pre>
<code>
                                        <?php //echo MasterPassHelper::formatXML($sad->checkoutData);  ?>
</code>
                                </pre>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <h2>Sample Form</h2>
                <div>
                    <form method="POST" action="O7_PostTransaction.php">
                        <p><input value="Post Transaction To MasterCard" type="submit"></p>
                    </form>
                    <h2>Results</h2>
                    <p>Following are the results returned after retrieving Shipping Address &amp; Credit Card information from the Wallet.</p>
                    <fieldset>
                        <legend>General Information</legend>
                        <table>
                            <tbody>
                                <tr>
                                    <th><label for="TransactionId"> Transaction Id:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->TransactionId; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>Card Information</legend>
                        <table>
                            <tbody>
                                <tr>
                                    <th><label for="Card_CardHolderName"> Cardholder Name:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->Card->CardHolderName; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="Card_AccountNumber"> Account Number:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->Card->AccountNumber; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Billing Address:</th>
                                    <td>
                                        <?php echo $checkoutObject->Card->Address->Line1; ?>
                                        <br> 
                                        <?php echo $checkoutObject->Card->Address->Line2; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <?php echo $checkoutObject->Card->Address->City, ' ', $checkoutObject->Card->Address->CountrySubdivision, ' ', $checkoutObject->Card->Address->PostalCode; ?><br>
                                        <?php echo $checkoutObject->Card->Address->Country; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="Card_ExpiryDate"> Expiration Date:</label>
                                    </th>
                                    <td>
                                        <?php if ($checkoutObject->Card) echo $checkoutObject->Card->ExpiryMonth . "/" . $checkoutObject->Card->ExpiryYear; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="Card_BrandId">Brand Id:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->Card->BrandId; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="Card_BrandName">Brand Name:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->Card->BrandName; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>Contact Information</legend>
                        <table>
                            <tbody>
                                <tr>
                                    <th>Name:</th>
                                    <td>
                                        <?php echo $checkoutObject->Contact->FirstName, ' ', $checkoutObject->Contact->LastName; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Gender:</th>
                                    <td><?php echo $checkoutObject->Contact->Gender; ?></td>
                                </tr>	
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td><?php if ($checkoutObject->Contact->DateOfBirth) echo $checkoutObject->Contact->DateOfBirth->Month, '/', $checkoutObject->Contact->DateOfBirth->Day, '/', $checkoutObject->Contact->DateOfBirth->Year; ?></td>
                                </tr>
                                <tr>
                                    <th>National ID:</th>
                                    <td>
                                        <?php echo $checkoutObject->Contact->NationalID; ?>
                                    </td>
                                </tr>			
                                <tr>
                                    <th>Country:</th>
                                    <td>
                                        <?php echo $checkoutObject->Contact->Country; ?>
                                    </td>
                                </tr>																									
                                <tr>
                                    <th><label for="Contact_PhoneNumber"> Phone Number:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->Contact->PhoneNumber; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="Contact_EmailAddress"> Email Address:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->Contact->EmailAddress; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>Shipping Address</legend>
                        <table>
                            <tbody>
                                <tr>
                                    <th><label for="ShippingAddress_RecipientName"> Recipient Name:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->ShippingAddress->RecipientName; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="ShippingAddress_RecipientPhoneNumber"> Recipient
                                            Phone Number:</label>
                                    </th>
                                    <td>
                                        <?php echo $checkoutObject->ShippingAddress->RecipientPhoneNumber; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>
                                        <?php echo $checkoutObject->ShippingAddress->Line1; ?> 
                                        <br>
                                        <?php echo $checkoutObject->ShippingAddress->Line2; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <?php echo $checkoutObject->ShippingAddress->City, ' ', $checkoutObject->ShippingAddress->CountrySubdivision, ' ', $checkoutObject->ShippingAddress->PostalCode; ?><br>
                                        <?php echo $checkoutObject->ShippingAddress->Country; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                    <?php if ($checkoutObject->RewardProgram instanceof RewardProgram): ?>
                        <fieldset>
                            <legend>Rewards Program</legend>
                            <table>
                                <tbody>
                                    <tr>
                                        <th><label for="RewardProgram_RewardNumber">Reward Number:</label>
                                        </th>
                                        <td><?php echo $checkoutObject->RewardProgram->RewardNumber; ?></td>
                                    </tr>
                                    <tr>
                                        <th><label for="RewardProgram_RewardsId">Rewards Id:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->RewardProgram->RewardId; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="RewardProgram_RewardName">Reward Name:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->RewardProgram->RewardName; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="RewardProgram_ExpiryMonth">Expiry Month:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->RewardProgram->ExpiryMonth; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="RewardProgram_ExpiryYear">Expiry Year:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->RewardProgram->ExpiryYear ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    <?php endif; ?>
                    <?php if ($checkoutObject->AuthenticationOptions instanceof AuthenticationOptions): ?>
                        <fieldset>
                            <legend>Advanced Authentication (3DS)</legend>
                            <table>
                                <tbody>
                                    <tr>
                                        <th><label for="AuthenticateMethod">Authenticate Method:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->AuthenticateMethod; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="CardEnrollmentMethod">Card Enrollment Method:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->CardEnrollmentMethod; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="CAvv">CAvv:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->CAvv; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="eciFlag">EciFlag:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->EciFlag; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="MasterCardAssignedID">Master Card Assigned Id</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->MasterCardAssignedID; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="paResStatus">PaResStatus:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->PaResStatus; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="SCEnrollmentStatus">SCEnrollmentStatus:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->SCEnrollmentStatus; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="signatureVerification">SignatureVerification:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->SignatureVerification; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="xid">Xid:</label>
                                        </th>
                                        <td>
                                            <?php echo $checkoutObject->AuthenticationOptions->Xid; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    <?php endif; ?>
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>