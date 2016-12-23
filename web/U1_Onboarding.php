<?php
session_start();

require_once (dirname(__DIR__)) . '/src/onboard/controller/OnboardController.php';
include_once (dirname(__DIR__)) . '/src/onboard/controller/MasterPassData.php';

$sad = new MasterPassData();
$_SESSION['sad'] = serialize($sad);

$controller = new OnboardController($sad);
$controller->processParameters($_POST);

$errorMessage = null;
try {

    $sad = $controller->postMerchantValidate();

    print_r($sad);
    exit;

    //$sad = $controller->postMerchantUpload();
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
                    <legend>Sent</legend>
                    <table>
                        <tr>
                            <th>
                                Authorization Header

                            </th>
                            <td>                      
                                <code><?php echo null //$controller->service->authHeader;  ?></code>

                            </td>
                        </tr> 
                        <tr>
                            <th>
                                Signature Base String 
                            </th>
                            <td class="formatUrl">
                                <hr>
                                <code><?php echo null //$controller->service->signatureBaseString;  ?></code>
                            </td>
                        </tr>  
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Sent to:</legend>          		
                    <table>                     
                        <tr>
                            <th>Request Token URL</th>
                            <td><?php echo $sad->requestUrl; ?></td>
                        </tr>

                    </table>  
                </fieldset>

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
            </div>
            <div id="footer">
            </div>
        </div>
    </body>


</html>
