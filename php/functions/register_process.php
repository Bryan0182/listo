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
$first_name = $connection->real_escape_string($_POST['first_name']);
$last_name = $connection->real_escape_string($_POST['last_name']);

// Het wachtwoord hashen
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Controleer of er een bestand is geüpload
$profile_picture = $_FILES['profile_picture'];
$target_dir = "assets/uploads/";
$target_file = '';

if ($profile_picture['error'] == UPLOAD_ERR_OK) {
    $target_file = $target_dir . basename($profile_picture["name"]);

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
        echo "Het bestand ". basename($profile_picture["name"]). " is geüpload.";
    } else {
        echo "Er is een fout opgetreden bij het uploaden van je bestand.";
        exit();
    }
}

// Als er geen bestand is geüpload, gebruik een lege string of een standaardwaarde
if ($target_file == '') {
    $target_file = '';
}

// Controleer of de gebruikersnaam al bestaat
$sql = "SELECT id FROM users WHERE username = '$username'";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Gebruikersnaam bestaat al, stuur gebruiker terug naar registratieformulier met melding
    session_start();
    $_SESSION['error_message'] = "De gebruikersnaam is al in gebruik.";
    header("Location: /registreren");
    exit();
}

// Controleer of het e-mailadres al bestaat
$sql = "SELECT id FROM users WHERE email = '$email'";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // E-mailadres bestaat al, stuur gebruiker terug naar registratieformulier met melding
    session_start();
    $_SESSION['error_message'] = "Het e-mailadres is al in gebruik.";
    header("Location: /registreren");
    exit();
}

// Voer de query uit om de nieuwe gebruiker toe te voegen aan de database
$sql = "INSERT INTO users (username, password, email, first_name, last_name, profile_picture, created_at) VALUES ('$username', '$hashed_password', '$email', '$first_name', '$last_name', '$target_file', NOW())";

if ($connection->query($sql) === TRUE) {
    // Start de sessie als deze nog niet is gestart
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Sla het succesbericht op in een sessievariabele
    $_SESSION['success_message'] = "Gebruiker succesvol aangemaakt. Je kunt nu inloggen.";

    // Redirect naar de loginpagina
    header("Location: /inloggen");
    exit();
} else {
    echo "Fout: " . $sql . "<br>" . $connection->error;
}

$connection->close();
?>
