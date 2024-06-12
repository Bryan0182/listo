<?php
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// De gebruikers-ID ophalen
$user_id = $_POST['user_id'];

// De bestandsnaam van de afbeelding ophalen met een prepared statement
$stmt = $connection->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $file_path = $row['profile_picture'];

    // De gebruiker uit de database verwijderen met een prepared statement
    $delete_stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->bind_param("i", $user_id);
    $delete_stmt->execute();

    if ($delete_stmt->affected_rows === 1) {
        // Het bestand verwijderen
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        echo "Gebruiker en bijbehorende afbeelding succesvol verwijderd.";
    } else {
        echo "Fout: " . $connection->error;
    }
} else {
    echo "Geen gebruiker gevonden met ID: $user_id";
}

$stmt->close();
$connection->close();
?>