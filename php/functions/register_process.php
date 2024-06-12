<?php
// Inclusief het bestand dat verbinding maakt met de database
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// De gegevens van het formulier ophalen
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];

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

// Controleer of de gebruikersnaam al bestaat met een prepared statement
$stmt = $connection->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Gebruikersnaam bestaat al, stuur gebruiker terug naar registratieformulier met melding
    session_start();
    $_SESSION['error_message'] = "De gebruikersnaam is al in gebruik.";
    header("Location: /registreren");
    exit();
}

// Controleer of het e-mailadres al bestaat met een prepared statement
$stmt = $connection->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // E-mailadres bestaat al, stuur gebruiker terug naar registratieformulier met melding
    session_start();
    $_SESSION['error_message'] = "Het e-mailadres is al in gebruik.";
    header("Location: /registreren");
    exit();
}

// Voer de query uit om de nieuwe gebruiker toe te voegen aan de database met een prepared statement
$stmt = $connection->prepare("INSERT INTO users (username, password, email, first_name, last_name, profile_picture, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("ssssss", $username, $hashed_password, $email, $first_name, $last_name, $target_file);
$stmt->execute();

if ($stmt->affected_rows === 1) {
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
    echo "Fout: " . $connection->error;
}

$stmt->close();
$connection->close();
?>