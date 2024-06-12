<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['title']) && isset($data['description']) && isset($data['deadline'])) {
    $title = $data['title'];
    $description = $data['description'];
    $deadline = $data['deadline'];

    if (isset($data['category']) && !empty($data['category'])) {
        $category = $data['category'];
    } elseif (isset($data['newCategory']) && !empty($data['newCategory'])) {
        $newCategory = $data['newCategory'];

        $sqlNewCategory = "INSERT INTO categories (name, user_id) VALUES (?, ?)";
        $stmtNewCategory = $connection->prepare($sqlNewCategory);
        $stmtNewCategory->bind_param("si", $newCategory, $user_id);
        if (!$stmtNewCategory->execute()) {
            echo json_encode(['success' => false, 'error' => 'Fout bij het toevoegen van nieuwe categorie.']);
            exit();
        }

        $category = $stmtNewCategory->insert_id;
    } else {
        echo json_encode(['success' => false, 'error' => 'Geen categorie geselecteerd of ingevoerd.']);
        exit();
    }

    $sql = "INSERT INTO tasks (task, description, category, deadline, is_completed, user_id) VALUES (?, ?, ?, ?, 0, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssssi", $title, $description, $category, $deadline, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Fout bij het toevoegen van de taak.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Niet alle benodigde gegevens zijn ontvangen.']);
}

$connection->close();
?>
