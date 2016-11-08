
<?php

	require("../../../config.php");

	//alustan sessiooni
	session_start();

	//ühendus
	$database = "if16_sandra_2";
	$mysqli = new mysqli($serverHost, $serverUsername, $serverPassword, $database);

		require("../class/Helper.class.php");
		$Helper = new Helper();
?>
