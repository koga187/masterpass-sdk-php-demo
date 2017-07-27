<?php

require_once (dirname(__DIR__)) . '/src/checkout/controller/MasterPassController.php';

session_start();
$sad = unserialize($_SESSION['sad']);
$errorMessage = null;
$controller = new MasterPassController($sad);
$sad = $controller->setCallbackParameters($_GET);
$connect = false;
if (isset($_GET['connect'])) {
    $connect = $_GET['connect'] === "true";
}
/**
if (isset($_GET[MasterPassService::PAIRING_TOKEN]) && isset($_GET[MasterPassService::PAIRING_VERIFIER])) {
    $pairing = true;
    $sad = $controller->setPairingToken($_GET[MasterPassService::PAIRING_TOKEN]);
    $sad = $controller->setPairingVerifier($_GET[MasterPassService::PAIRING_VERIFIER]);

    $sad = $controller->getLongAccessToken();

    if (!empty($sad->longAccessToken)) {
        setcookie('longAccessToken', $sad->longAccessToken, time() + (60 * 60 * 24 * 7));
    }
}
*/
$errorMessage = null;

$_SESSION['sad'] = serialize($sad);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <title>Masterpass Standard Checkout Flow</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="Content/Site.css">
    </head>
    <body class="postCheckout">
        <div class="page">
            <div id="header">
                <div id="title">
                    <h1>
                        Masterpass Standard Checkout Flow </h1>
                </div>
                <div id="logindisplay">
                    &nbsp;
                </div>
            </div>
            <div id="main">
                <h1>
                    Received Callback from Wallet Site
                </h1>
                <p>
                    Data received from the Callback URL<br/>
                    Use the Request Token and the Verifier to get the Access Token. The Checkout Resource URL will be used to get the users shipping and/or billing infomation after the Access Token is Received.
                </p>

                <fieldset>
                    <legend>Data from the Callback URL</legend>
                    <table>
                        <tbody>
                            <tr>
                                <th>
                                    Request Token 
                                </th>
                                <td>
                                    <code><?php echo $sad->requestToken; ?></code>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Request Token Verifier 
                                </th>
                                <td>
<?php echo $sad->requestVerifier; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    Checkout Resource URL 
                                </th>
                                <td>
<?php echo $sad->checkoutResourceUrl; ?>
                                </td>
                            </tr>


                        </tbody>

                    </table>
                </fieldset>

<?php if ($connect): ?>

                    <h1>
                        Received Callback from Wallet Site
                    </h1>
                    <p>
                        Data received from the Callback URL<br/>
                        Use the Pairing Token and the Pairing Verifier to get the Long Access Token.
                    </p>

                    <fieldset>
                        <legend>Data from the Callback URL</legend>
                        <table>
                            <tbody>
                                <tr>
                                    <th>
                                        Pairing Token 
                                    </th>
                                    <td>
    <?php echo $sad->pairingToken; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Pairing Token Verifier 
                                    </th>
                                    <td>
    <?php echo $sad->pairingVerifier; ?>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </fieldset>

    <?php if ($sad->longAccessToken != null): ?>            
                        <h1>
                            Received Long Access Token
                        </h1>

                        <fieldset>
                            <legend>Sent</legend>
                            <table>
                                <tr>
                                    <th>
                                        Authorization Header 
                                    </th>
                                    <td>                     
                                        <code><?php echo $controller->service->authHeader; ?></code>
                                    </td>
                                </tr> 
                                <tr>
                                    <th>
                                        Signature Base String 
                                    </th>

                                    <td class="formatUrl">
                                        <hr>
                                        <code><?php echo $controller->service->signatureBaseString; ?></code>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </fieldset>
                        <fieldset>
                            <legend>Sent to:</legend>
                            <table>
                                <tr>
                                    <th>
                                        Access Token URL 
                                    </th>
                                    <td>
        <?php echo $sad->accessUrl; ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>

                        <fieldset>
                            <legend>Received:</legend>

                            <table>
                                <tr>
                                    <th>
                                        Access Token 
                                    </th>
                                    <td>
        <?php echo $sad->longAccessToken; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        Oauth Secret 
                                    </th>
                                    <td>
        <?php echo $sad->longAccessTokenResponse->oAuthSecret; ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>

    <?php else: ?>            
                        <h1 style="color:red">
                            Unable to retrieve Long Access Token. You have not successfully paired with Masterpass.
                        </h1>
    <?php endif; ?>            
                <?php endif; ?>

                <form method="POST" action="O5_GetAccessToken.php">
                    <p>
                        <input value="Retrieve Access Token" type="submit"/>
                    </p>
                </form>
            </div>
            <div id="footer">
            </div>
        </div>
    </body>
</html>