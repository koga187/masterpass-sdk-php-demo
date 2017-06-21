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

    $sad = $controller->getAccessToken();
    
} catch (SDKErrorResponseException $e) {

    $errorMessage = MasterPassHelper::formatError($e);
}

$_SESSION['sad'] = serialize($sad);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <title>
            Masterpass Standard Checkout Flow
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="Content/Site.css">
    </head>
    <body class="postCheckout">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>
                        Masterpass Standard Checkout Flow</h1>
                </div>
                <div id="logindisplay">
                    &nbsp;
                </div>

            </div>
            <div id="main">
                <h1>
                    Retrieved Access Token
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
                    Use the Request Token and Verifier retrieved in the previous step to request an Access Token.
                </p>


                <fieldset>
                    <legend>Received:</legend>

                    <table>
                        <tr>
                            <th>
                                Access Token 
                            </th>
                            <td>
<?php echo $sad->accessTokenResponse->OauthToken; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                Oauth Secret 
                            </th>
                            <td>
<?php echo $sad->accessTokenResponse->OauthTokenSecret; ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <form method="POST" action="O6_ProcessCheckout.php">
                    <p>
                        <input value="Retrieve Checkout Data" type="submit">
                    </p>
                </form>
            </div>
            <div id="footer">
            </div>
        </div>
    </body>
</html>
