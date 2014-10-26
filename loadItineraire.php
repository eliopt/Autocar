<?php
session_start();
if(preg_match('#\([0-9\.]+, [0-9\.]+\)#', $_POST['de']) AND preg_match('#\([0-9\.]+, [0-9\.]+\)#', $_POST['a'])) {
	$de = preg_replace('#[\(\) ]#', '', $_POST['de']);
	$de = preg_split('#,#', $de);
	$_SESSION['de'] = $de;
	$a = preg_replace('#[\(\) ]#', '', $_POST['a']);
	$a = preg_split('#,#', $a);
	$_SESSION['a'] = $a;
	echo 'Success';
} else {
	echo 'Error';
}
?>