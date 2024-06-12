<?php
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

if (isset($_POST['taskId'])) {
    $task_id = $_POST['taskId'];
    $completed = isset($_POST['completed']) ? 1 : 0;

    $stmt = $connection->prepare("UPDATE tasks SET is_completed = ? WHERE id = ?");
    $stmt->bind_param("ii", $completed, $task_id);
    $stmt->execute();

    if ($stmt->affected_rows === 1) {
        echo json_encode(array('success' => true));
        exit();
    } else {
        echo json_encode(array('success' => false, 'error' => $connection->error));
        exit();
    }
} else {
    echo json_encode(array('success' => false, 'error' => 'Geen taak-ID meegegeven.'));
    exit();
}

$stmt->close();
$connection->close();
?>