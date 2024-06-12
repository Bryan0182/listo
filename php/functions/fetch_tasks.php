<?php
session_start();
include 'database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Gebruiker niet ingelogd.']);
    exit();
}

$user_id = $_SESSION['user_id'];

$input = json_decode(file_get_contents('php://input'), true);
$category = $input['category'];

if ($category == 'Alle taken') {
    $sql = "SELECT * FROM tasks WHERE user_id = ? AND is_completed = 0";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM tasks WHERE user_id = ? AND category = ? AND is_completed = 0";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("is", $user_id, $category);
    $stmt->execute();
    $result = $stmt->get_result();
}

$tasks = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($tasks);
?>
