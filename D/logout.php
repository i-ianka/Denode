<?php
session_start();

if(isset($_SESSION['ID_PERFUSU'])){
	unset($_SESSION['ID_PERFUSU']);
}

session_destroy();

print "<script>window.location='index.php';</script>";
?>