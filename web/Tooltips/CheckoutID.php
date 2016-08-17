<?php
	$break = explode('/', $_SERVER['REQUEST_URI']);
?>
<html>
	<body>
		<h3>Checkout identifier is also generated once the checkout project is approved. It's the same for sandbox and production environment. </h3>
		<img
			src="/<?php echo $break[1] ?>/Web/images/tooltips/Checkout_Identifier_1.png"
			alt="Checkout" style="border: 0px currentColor;" />
		<img
			src="/<?php echo $break[1] ?>/Web/images/tooltips/Checkout_Identifier_2.png"
			alt="Checkout" style="border: 0px currentColor;" />
	</body>
</html>