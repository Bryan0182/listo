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
$username = $_POST['username'];
$email = $_POST['email'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];

// Profielfoto uploaden als er een bestand is geselecteerd
if (!empty($_FILES['profile_picture']['name'])) {
    $profile_picture = $_FILES['profile_picture'];
    $target_dir = "assets/uploads/";
    $target_file = $target_dir . basename($profile_picture["name"]);

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
        // Profielfoto pad opslaan
        $profile_picture_path = $target_file;
        // Update query inclusief profielfoto met een prepared statement
        $stmt = $connection->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, profile_picture = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $username, $email, $first_name, $last_name, $profile_picture_path, $user_id);
        $stmt->execute();
    } else {
        echo "Er is een fout opgetreden bij het uploaden van je bestand.";
        exit();
    }
} else {
    // Update query zonder profielfoto met een prepared statement
    $stmt = $connection->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $email, $first_name, $last_name, $user_id);
    $stmt->execute();
}

// Controleer of de update succesvol was
if ($stmt->affected_rows === 1) {
    // Succesbericht opslaan in sessie
    $_SESSION['success_message'] = "Profiel succesvol bijgewerkt.";
    header("Location: /profiel");
    exit();
} else {
    echo "Fout: " . $connection->error;
}

$stmt->close();
$connection->close();
?>