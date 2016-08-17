<?php
	$break = explode('/', $_SERVER['REQUEST_URI']);
?>
<html>
	<body>
		<h3>Callback URL is the URL where MasterPass will redirect the consumer to after the user clicks on 'Proceed to Checkout' button on MasterPass site. Merchant's test and production website URLs are specified while creating a checkout project and the callback URL is used in the code while making MasterPass service call. </h3>
		<img
			src="/<?php echo $break[1] ?>/Web/images/tooltips/CallbackURL.png"
			alt="Checkout" style="border: 0px currentColor;" />
	</body>
</html>