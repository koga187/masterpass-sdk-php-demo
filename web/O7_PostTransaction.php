<?php

require_once (dirname(__DIR__)) . '/src/checkout/controller/MasterPassController.php';

use MasterpassDemo\src\checkout\controller\MasterPassController;
use MasterpassDemo\src\checkout\controller\MasterPassHelper;
use MasterCardCoreSDK\Exception\SDKErrorResponseException;

session_start();
$sad = unserialize($_SESSION['sad']);

$errorMessage = null;
$controller = new MasterPassController($sad);

try {

    $sad = $controller->postTransaction();
    $transaction = $sad->postTransactionResponse->MerchantTransaction;
    
} catch (SDKErrorResponseException $e) {

    $errorMessage = MasterPassHelper::formatError($e);
}

$_SESSION['sad'] = serialize($sad);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MasterCard OAuth Tester Step 6: Complete!</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="Content/Site.css">
    </head>
    <body class="postCheckout">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>MasterCard OAuth Tester (PHP)</h1>
                </div>
                <div id="logindisplay">&nbsp;</div>
            </div>
            <div id="main">
                <h1>Step 6 - Complete: Transaction Posted</h1>
                <?php
                if ($errorMessage != null) {

                    echo '<h2>Error</h2>
		<div class = "error">
		<p>
		The following error occurred while trying to get the Request Token from the MasterCard API.
		</p>
		<p>
<pre>
<code>' .
                    $errorMessage .
                    '</code>
</pre>
				</p></div>';
                }
                ?>
                <p>Final step! Log the transaction to MasterCard's services.</p>
                <fieldset>
                    <legend>Sent:</legend>
                    <table>
                        <tr>
                            <th>
                                Authorization Header 
                            </th>
                            <td>
                                <code><?php //echo $controller->service->authHeader;   ?></code>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Signature Base String 
                            </th>
                            <td>
                                <code><?php //echo $controller->service->signatureBaseString;   ?></code>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Sent Body  
                            </th>
                            <td>
                                <pre>
<code>
                                        <?php //echo MasterPassHelper::formatXML($sad->postTransactionRequest); ?>
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
                            <th>Transaction URL</th>
                            <td><?php echo $sad->postbackUrl; ?></td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                        <legend>Received</legend>
                        <table>
                            <tbody>
                                <tr>
                                    <th>
                                        <label> Transaction Id</label>
                                    </th>
                                    <td>
                                        <?php echo $transaction->TransactionId; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>ConsumerKey:</th>
                                    <td>
                                        <?php echo $transaction->ConsumerKey; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Currency:</th>
                                    <td>
                                        <?php echo $transaction->Currency; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>OrderAmount:</th>
                                    <td>
                                        <?php echo $transaction->OrderAmount; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>TransactionStatus:</th>
                                    <td>
                                        <?php echo $transaction->TransactionStatus; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                <form action="./" method="get">
                    <input value="Click To Start Over" type="submit">
                </form>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>

