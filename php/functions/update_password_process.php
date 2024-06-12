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
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Controleer of het nieuwe wachtwoord en het bevestigde wachtwoord overeenkomen
if ($new_password !== $confirm_password) {
    $_SESSION['error_message'] = "Het nieuwe wachtwoord en het bevestigde wachtwoord komen niet overeen.";
    header("Location: /profiel/wachtwoord");
    exit();
}

// Haal het huidige wachtwoord van de gebruiker op uit de database met een prepared statement
$stmt = $connection->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hashed_current_password = $row['password'];

    // Controleer of het ingevoerde huidige wachtwoord correct is
    if (password_verify($current_password, $hashed_current_password)) {
        // Versleutel het nieuwe wachtwoord
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update query om het nieuwe wachtwoord op te slaan met een prepared statement
        $update_stmt = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_new_password, $user_id);
        $update_stmt->execute();

        // Controleer of de update succesvol was
        if ($update_stmt->affected_rows === 1) {
            // Succesbericht opslaan in sessie
            $_SESSION['success_message'] = "Wachtwoord succesvol bijgewerkt.";
            header("Location: /profiel");
            exit();
        } else {
            echo "Fout: " . $connection->error;
        }
    } else {
        $_SESSION['error_message'] = "Het huidige wachtwoord is onjuist.";
        header("Location: /profiel/wachtwoord");
        exit();
    }
} else {
    echo "Gebruiker niet gevonden.";
}

$stmt->close();
$connection->close();
?>