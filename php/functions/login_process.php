<?php
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// De gegevens van het formulier ophalen
$username = $connection->real_escape_string($_POST['username']);
$password = $connection->real_escape_string($_POST['password']);

// Zoek de gebruiker in de database
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // De gebruiker bestaat, controleer het wachtwoord
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Het wachtwoord is correct, start de sessie
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Sla de gebruikersgegevens op in de sessie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redirect naar de homepage
        // Redirect naar de inlogpagina met een succesbericht
        $_SESSION['success_message'] = "Je bent succesvol ingelogd.";
        header("Location: /dashboard");
        exit();
    } else {
        // Het wachtwoord is incorrect
        $_SESSION['error_message'] = "Fout: Het ingevoerde wachtwoord is incorrect.";
        header("Location: ../../login.php");
        exit();
    }
} else {
    // De gebruiker bestaat niet
    $_SESSION['error_message'] = "Fout: De ingevoerde gebruikersnaam bestaat niet.";
    header("Location: ../../login.php");
    exit();
}

$connection->close();
?>