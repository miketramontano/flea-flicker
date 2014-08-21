<?php

function strclean($str) { 
	return preg_replace('/[^0-9a-zA-Z\s\-\.\']/', '', substr($str, 0, 50)); 
}

session_start();

if (isset($_POST['add'])) {
	if (!is_array($_SESSION['stash'])) { $_SESSION['stash'] = array(); }
	$_SESSION['stash'][strclean($_POST['add'])] = 1;
	exit;
} else if (isset($_POST['remove'])) {
	unset($_SESSION['stash'][strclean($_POST['remove'])]);
	exit;
} else if (isset($_POST['clear'])) {
	$_SESSION['stash'] = array();
	exit;
}

?>