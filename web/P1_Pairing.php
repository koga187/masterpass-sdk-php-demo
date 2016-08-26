<?php

require_once (dirname(__DIR__)) . '/src/controller/MasterPassController.php';

session_start();
$sad = unserialize($_SESSION['sad']);
$controller = new MasterPassController($sad);

if(!isset($_GET['checkout'])) {
	$sad = $controller->processParameters($_POST);
	$callback = $sad->callbackUrl;
} else {
	$callback = $sad->connectedCallbackUrl;
}

$errorMessage = null;
if(isset($_GET["error"])) {
	$errorMessage = ' ';
}

try {
	$sad = $controller->getPairingToken();
	
} catch (SDKErrorResponseException $e) {

    $errorMessage = MasterPassHelper::formatError($e);
}

$_SESSION['sad'] = serialize($sad);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
    <title>
    	MasterPass Pairing Flow
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="Content/Site.css">
</head>
<body class="pairing">
	
    <div class="page">
        <div id="header">
            <div id="title">
                <h1>MasterPass Pairing Flow</h1>
            </div>
            <div id="logindisplay">
                &nbsp;
            </div>
            
        </div>
        <div id="main">
            <h1>
                Pairing Token Received
            </h1>
<?php       
	if ( $errorMessage != null ){ 
		
	echo '<h2>Error</h2>
	<div class = "error">
		<p>
		    The following error occurred while trying to get the Request Token from the MasterCard API.
		</p>
		<p>		
<pre>
<code>',$errorMessage,
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
							<code><?php //echo $controller->service->authHeader; ?></code>
						
                        </td>
                    </tr> 
	              	<tr>
                        <th>
                            Signature Base String 
                        </th>
                        <td>
                        	<hr>
                            <code><?php //echo $controller->service->signatureBaseString; ?></code>
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
                            <?php echo $sad->requestUrl; ?>
                        </td>
                    </tr>
                    
                 </table>  
            </fieldset>
            
            <fieldset>
            	<legend>Received</legend>  
                   <table>                     
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
                            Pairing Callback Path 
                        </th>
                        <td>
                            <?php echo $sad->pairingCallbackPath; ?>
                        </td>
                    </tr>
                 </table>
            </fieldset>
            
            <form id="merchantInit" action="P2_MerchantInit.php" method="POST">
				<input type="hidden" name="oauth_token" id="oauth_token" value="<?php echo $sad->requestToken ?>">
				<input type="hidden" name="RedirectUrl" id="RedirectUrl" value="<?php echo $sad->requestTokenResponse->redirectUrl ?>">
	    		<input value="Merchant Initialization" type="submit">
			</form>
        </div>
        <div id="footer">
        </div>
    </div>
</body>
</html>