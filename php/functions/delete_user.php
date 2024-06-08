<?php
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// De gebruikers-ID ophalen
$user_id = $connection->real_escape_string($_POST['user_id']);

// De bestandsnaam van de afbeelding ophalen
$sql = "SELECT profile_picture FROM users WHERE id = $user_id";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $file_path = $row['profile_picture'];

    // De gebruiker uit de database verwijderen
    $sql = "DELETE FROM users WHERE id = $user_id";

    if ($connection->query($sql) === TRUE) {
        // Het bestand verwijderen
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        echo "Gebruiker en bijbehorende afbeelding succesvol verwijderd.";
    } else {
        echo "Fout: " . $sql . "<br>" . $connection->error;
    }
} else {
    echo "Geen gebruiker gevonden met ID: $user_id";
}

$connection->close();
?>