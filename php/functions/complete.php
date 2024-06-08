<?php
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

file_put_contents('/logs/post_data.log', print_r($_POST, true)); // Log de POST-data naar een bestand

// Controleer of er een ID is meegegeven via POST
if (isset($_POST['taskId'])) {
    $task_id = $connection->real_escape_string($_POST['taskId']);
    $completed = isset($_POST['completed']) ? 1 : 0; // Controleer of de taak als voltooid is gemarkeerd

    // Voer een updatequery uit om de taak als voltooid of niet voltooid te markeren
    $sql = "UPDATE tasks SET is_completed = $completed WHERE id = $task_id";
    if ($connection->query($sql) === TRUE) {
        // Stuur een succesbericht terug als de taak succesvol is bijgewerkt
        echo json_encode(array('success' => true));
        exit(); // Belangrijk om de rest van de code te stoppen na het doorsturen van de JSON-respons
    } else {
        // Stuur een foutbericht terug als er een fout optreedt tijdens het bijwerken van de taak
        echo json_encode(array('success' => false, 'error' => $connection->error));
        exit(); // Belangrijk om de rest van de code te stoppen na het doorsturen van de JSON-respons
    }
} else {
    // Stuur een foutbericht terug als er geen taak-ID is meegegeven
    echo json_encode(array('success' => false, 'error' => 'Geen taak-ID meegegeven.'));
    exit(); // Belangrijk om de rest van de code te stoppen na het doorsturen van de JSON-respons
}
?>