<?php
// Inclusief het bestand dat verbinding maakt met de database
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// De gegevens van het formulier ophalen
$username = $connection->real_escape_string($_POST['username']);
$password = $connection->real_escape_string($_POST['password']);
$email = $connection->real_escape_string($_POST['email']);

// Het wachtwoord hashen
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// De profielfoto uploaden en het pad opslaan
$profile_picture = $_FILES['profile_picture'];
$target_dir = "assets/uploads/";
$target_file = $target_dir . basename($profile_picture["name"]);

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
    echo "Het bestand ". basename($profile_picture["name"]). " is geüpload.";
} else {
    echo "Er is een fout opgetreden bij het uploaden van je bestand.";
}

$sql = "INSERT INTO users (username, password, email, profile_picture, created_at) VALUES ('$username', '$hashed_password', '$email', '$target_file', NOW())";

if ($connection->query($sql) === TRUE) {
    // Start de sessie als deze nog niet is gestart
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Sla het succesbericht op in een sessievariabele
    $_SESSION['success_message'] = "Gebruiker succesvol aangemaakt. Je kunt nu inloggen.";

    // Redirect naar de loginpagina
    header("Location: /login.php");
    exit();
} else {
    echo "Fout: " . $sql . "<br>" . $connection->error;
}

$connection->close();