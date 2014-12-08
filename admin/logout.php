<?php
session_start();
unset($_SESSION["login"]);
unset($_SESSION["passwd"]);
header("Location: ./");
?>