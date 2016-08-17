<?php
	$break = explode('/', $_SERVER['REQUEST_URI']);
?>
<html>
	<body>
		<h3>Private Key is managed by the merchant and corresponding public key is stored on MasterPass side. Key exchange happens on MasterCard developer zone (https://developer.mastercard.com). Every call to MasterPass has to be signed by the merchant’s private key.</h3>
		<img
			src="/<?php echo $break[1] ?>/Web/images/tooltips/KeystorePassword.png"
			alt="Checkout" style="border: 0px currentColor;" />
	</body>
</html>