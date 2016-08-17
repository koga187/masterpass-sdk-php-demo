<?php
	$break = explode('/', $_SERVER['REQUEST_URI']);
?>
<html>
	<body>
		<h3>Consumer key is generated once the checkout project is approved. Consumer key is different for sandbox and production environment. Merchant developer should use sandbox consumer key when calling MasterPass sandbox services and should use production consumer key when calling to MasterPass production services. </h3>
		<img
			src="/<?php echo $break[1] ?>/Web/images/tooltips/ConsumerKey.png"
			alt="Checkout" style="border: 0px currentColor;" />
	</body>
</html>