<?php
session_start();

require_once (dirname(__DIR__)) . '/src/onboard/controller/OnboardController.php';
require_once (dirname(__DIR__)) . '/src/onboard/controller/MasterPassData.php';

$sad = new MasterPassData();
$_SESSION['sad'] = serialize($sad);

$controller = new OnboardController($sad);
$controller->processParameters($_POST);

$errorMessage = null;
try {

    $sad = $controller->postMerchantValidate();
    if ($sad->validateResponse->ValidatedMerchant->ErrorText === 'Successful') {
        $sad = $controller->postMerchantUpload();
        
        print_r($sad);
    }

    //
} catch (Exception $e) {
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
    </head>
    <body class="standard">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>Merchant Onboarding</h1>
                </div>
                <div id="logindisplay">
                    &nbsp;
                </div>
            </div>
            <div id="main">
                <h1>
                    Response Received
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
                <fieldset>
                    <legend>Sent to:</legend>          		
                    <table>                     
                        <tr>
                            <th>OpenFeed URL</th>
                            <td><?php echo $sad->openFeedUrl; ?></td>
                        </tr>

                    </table>  
                </fieldset>

                <fieldset>
                    <legend>Validation Received</legend>  
                    <table>                     
                        <tr>
                            <th>Message</th>
                            <td><?php echo $sad->validateResponse->ValidatedMerchant->ErrorText; ?></td>
                        </tr>
                        <tr>
                            <th>MerchantId</th>
                            <td><?php echo $sad->validateResponse->ValidatedMerchant->MerchantId; ?></td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Upload Received</legend>  
                    <table>                     
                        <tr>
                            <th>Batch ID</th>
                            <td><?php echo $sad->openFeedResponse->Summary->BatchId; ?></td>
                        </tr>
                        <tr>
                            <th>SuccessCount</th>
                            <td><?php echo $sad->openFeedResponse->Summary->SuccessCount; ?></td>
                        </tr>
                        <tr>
                            <th>FailureCount</th>
                            <td><?php echo $sad->openFeedResponse->Summary->FailureCount; ?></td>
                        </tr>

                        <?php if (isset($sad->openFeedResponse->MerchantResponseRecord->ErrorText)): ?>
                            <tr>
                                <th>ErrorTex</th>
                                <td><?php echo $sad->openFeedResponse->MerchantResponseRecord->ErrorText; ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Checkout ID</th>
                                <td><?php echo $sad->openFeedResponse->MerchantResponseRecord->CheckoutBrand->CheckoutIdentifier; ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </fieldset>
            </div>
            <div id="footer">
            </div>
        </div>
    </body>


</html>
