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
$username = $connection->real_escape_string($_POST['username']);
$email = $connection->real_escape_string($_POST['email']);
$first_name = $connection->real_escape_string($_POST['first_name']);
$last_name = $connection->real_escape_string($_POST['last_name']);

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
        // Update query inclusief profielfoto
        $sql = "UPDATE users SET username = '$username', email = '$email', first_name = '$first_name', last_name = '$last_name', profile_picture = '$profile_picture_path' WHERE id = $user_id";
    } else {
        echo "Er is een fout opgetreden bij het uploaden van je bestand.";
        exit();
    }
} else {
    // Update query zonder profielfoto
    $sql = "UPDATE users SET username = '$username', email = '$email', first_name = '$first_name', last_name = '$last_name' WHERE id = $user_id";
}

// Voer de update query uit
if ($connection->query($sql) === TRUE) {
    // Succesbericht opslaan in sessie
    $_SESSION['success_message'] = "Profiel succesvol bijgewerkt.";
    header("Location: /profiel");
    exit();
} else {
    echo "Fout: " . $sql . "<br>" . $connection->error;
}

$connection->close();
?>
