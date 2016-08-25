<?php
require_once (dirname(__DIR__)) . '/src/controller/MasterPassController.php';

session_start();
$sad = unserialize($_SESSION['sad']);
if ($sad->requestToken == null) {
    header("Location: ./");
}

$errorMessage = null;
$controller = new MasterPassController($sad);


try {

    $sad = $controller->postShoppingCart();
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
                <h1>
                    Shopping Cart Data Submitted
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
                    This step sends the Merchants shopping cart data to MasterCard services for display in the Wallet.
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
                                Shopping Cart XML 
                            </th>
                            <td>
                                <pre>                        
<code>                        
<?php //echo MasterPassHelper::formatXML($sad->shoppingCartRequest);  ?>
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
                            <th>
                                Shopping Cart URL 
                            </th>
                            <td>
<?php echo $sad->shoppingCartUrl;  ?>
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
                            <td><?php echo $sad->shoppingCartResponse->OAuthToken; ?></td>
                        </tr>
                    </table>
                </fieldset>
                <form action="O3_MerchantInit.php" method="POST">
                    <input value="Merchant Initialization" type="submit">
                </form>
            </div>
        </div>
    </body>
</html>
