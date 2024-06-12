<?php
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// Start de sessie als deze nog niet is gestart
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// De gegevens van het formulier ophalen
$username = $_POST['username'];
$password = $_POST['password'];

// Zoek de gebruiker in de database met een prepared statement
$stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // De gebruiker bestaat, controleer het wachtwoord
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Het wachtwoord is correct, start de sessie
        // Sla de gebruikersgegevens op in de sessie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Update de last_login tijd in de database
        $user_id = $user['id'];
        $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $update_stmt = $connection->prepare($update_sql);
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();

        // Redirect naar de homepage
        // Redirect naar de inlogpagina met een succesbericht
        $_SESSION['success_message'] = "Je bent succesvol ingelogd.";
        header("Location: /dashboard");
        exit();
    } else {
        // Het wachtwoord is incorrect
        $_SESSION['error_message'] = "Fout: Het ingevoerde wachtwoord is incorrect.";
        header("Location: /inloggen");
        exit();
    }
} else {
    // De gebruiker bestaat niet
    $_SESSION['error_message'] = "Fout: De ingevoerde gebruikersnaam bestaat niet.";
    header("Location: /inloggen");
    exit();
}

$stmt->close();
$connection->close();
?>