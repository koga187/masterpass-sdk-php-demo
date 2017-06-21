<?php
session_start();

require_once (dirname(__DIR__)) . '/src/checkout/controller/MasterPassController.php';

use MasterpassDemo\src\checkout\controller\MasterPassController;
use MasterpassDemo\src\checkout\controller\MasterPassHelper;
use MasterCardCoreSDK\Exception\SDKErrorResponseException;


$sad = unserialize($_SESSION['sad']);
$controller = new MasterPassController($sad);
$controller->processParameters($_POST);

$errorMessage = null;
try {

    $sad = $controller->getRequestToken();
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

    </head>
    <body class="standard">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>
                        MasterPass Standard Flow</h1>
                </div>
                <div id="logindisplay">
                    &nbsp;
                </div>
            </div>
            <div id="main">
                <h1>
                    Request Token Received
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
                    Use the following Request Token to call subsequent MasterPass services.

                </p>

                <fieldset>
                    <legend>Received</legend>  
                    <table>                     
                        <tr>
                            <th>Request Token</th>
                            <td><?php echo $sad->requestToken; ?></td>
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


                <form action="O2_ShoppingCart.php" method="POST">
                    <input value="Shopping Cart" type="submit">
                </form>



            </div>
            <div id="footer">
            </div>
        </div>
    </body>


</html>
