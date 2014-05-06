<?php
	session_destroy();  # Nuke it
	unset($_SESSION);   # Delete its contents
	session_start();    # Create a new session
	session_regenerate_id(TRUE);  # Ensure it has a new id
	$token = makeToken();
	
	header("Location: index.php");
	die();
?>