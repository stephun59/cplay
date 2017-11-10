<?php
	require_once 'src/app.php';

	setcookie( 'autoauth', "", time() - 1, null, null, false, true  );
	session_destroy();
	header("location: list.php");
