<?php
session_start(); // Start de sessie

// Verwijder alle sessievariabelen
$_SESSION = array();

// Vernietig de sessie
session_destroy();

// Redirect naar de inlogpagina of een andere gewenste pagina
header("Location: /inloggen");
exit();
?>
