<?php
session_start();

include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    // De gebruiker is niet ingelogd, redirect naar de loginpagina
    header("Location: /inloggen");
    exit();
}

// Haal de gebruikersgegevens op uit de sessie
$user_id = $_SESSION['user_id'];

// De gegevens van het formulier ophalen
$current_password = $connection->real_escape_string($_POST['current_password']);
$new_password = $connection->real_escape_string($_POST['new_password']);
$confirm_password = $connection->real_escape_string($_POST['confirm_password']);

// Controleer of het nieuwe wachtwoord en het bevestigde wachtwoord overeenkomen
if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "Het nieuwe wachtwoord en het bevestigde wachtwoord komen niet overeen.";
    header("Location: /profiel/wachtwoord");
    exit();
}

// Haal het huidige wachtwoord van de gebruiker op uit de database
$sql = "SELECT password FROM users WHERE id = $user_id";
$result = $connection->query($sql);

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hashed_current_password = $row['password'];

    // Controleer of het ingevoerde huidige wachtwoord correct is
    if (password_verify($current_password, $hashed_current_password)) {
        // Versleutel het nieuwe wachtwoord
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update query om het nieuwe wachtwoord op te slaan
        $update_sql = "UPDATE users SET password = '$hashed_new_password' WHERE id = $user_id";

        // Voer de update query uit
        if ($connection->query($update_sql) === TRUE) {
            // Succesbericht opslaan in sessie
            $_SESSION['success_message'] = "Wachtwoord succesvol bijgewerkt.";
            header("Location: /profiel");
            exit();
        } else {
            echo "Fout: " . $update_sql . "<br>" . $connection->error;
        }
    } else {
        $_SESSION['error_message'] = "Het huidige wachtwoord is onjuist.";
        header("Location: /profiel/wachtwoord");
        exit();
    }
} else {
    echo "Gebruiker niet gevonden.";
}

$connection->close();
?>
