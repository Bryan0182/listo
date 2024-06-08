<?php
session_start();

include 'php/functions/database.php';

if (!isset($connection)) {
    echo "Fout: Databaseverbinding is niet ingesteld.";
    exit();
}

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    // De gebruiker is niet ingelogd, redirect naar de loginpagina
    header("Location: /login.php");
    exit();
}

// De gebruiker is ingelogd, haal de gebruikersgegevens op
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Haal de profielfoto van de gebruiker op uit de database
$sql = "SELECT profile_picture FROM users WHERE id = $user_id";
$result = $connection->query($sql);
$row = $result->fetch_assoc();
$profile_picture = $row['profile_picture'];

$currentCategory = isset($_GET['category']) ? $_GET['category'] : 'Alle taken';

if ($currentCategory == 'Alle taken') {
    $sqlTasks = "SELECT * FROM tasks WHERE is_completed = 0";
    $resultTasks = $connection->query($sqlTasks);
} else {
    $sqlTasks = "SELECT * FROM tasks WHERE category = ? AND is_completed = 0";
    $stmt = $connection->prepare($sqlTasks);
    $stmt->bind_param("s", $currentCategory);
    $stmt->execute();
    $resultTasks = $stmt->get_result();
}

// Query om categorieën op te halen
$sqlCategories = "SELECT DISTINCT category FROM tasks WHERE category IS NOT NULL";
$resultCategories = $connection->query($sqlCategories);
?>
<?php
$pagetitle = "Home";
include 'php/template-parts/head.php';
?>

<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 min-vh-100">
                <a href="/" class="d-flex align-items-center text-decoration-none logo">
                    <img src="../../assets/images/listo-logo-small.webp" width="40" height="40" alt="Listo Logo">
                    <span class="fs-5 d-none d-sm-inline">Listo</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                    <li class="nav-item">
                        <a href="?category=Alle taken"
                           class="nav-link nav-link-category <?php echo ($currentCategory == 'Alle taken') ? 'active' : ''; ?>"
                           data-category="Alle taken">
                            Alle taken
                        </a>
                    </li>
                    <?php
                    // Haal de gebruikers-ID op uit de sessie
                    $user_id = $_SESSION['user_id'];

                    // Query om de categorieën op te halen voor de ingelogde gebruiker
                    $sqlCategories = "SELECT DISTINCT category FROM tasks WHERE user_id = ?";
                    $stmtCategories = $connection->prepare($sqlCategories);
                    $stmtCategories->bind_param("i", $user_id);
                    $stmtCategories->execute();
                    $resultCategories = $stmtCategories->get_result();

                    if ($resultCategories->num_rows > 0): ?>
                        <?php while ($rowCategory = $resultCategories->fetch_assoc()): ?>
                            <li class="nav-item">
                                <a href="?category=<?php echo $rowCategory['category']; ?>"
                                   class="nav-link nav-link-category <?php echo ($currentCategory == $rowCategory['category']) ? 'active' : ''; ?>"
                                   data-category="<?php echo $rowCategory['category']; ?>">
                                    <?php echo $rowCategory['category']; ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <span class="nav-link nav-link-category">Geen categorieën gevonden</span>
                        </li>
                    <?php endif; ?>
                </ul>
                <hr>
                <div class="dropdown pb-3">
                    <a href="#"
                       class="d-flex align-items-center text-white text-decoration-none dropdown-toggle profile-dropdown"
                       id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($profile_picture) && $profile_picture != 'assets/uploads/') : ?>
                            <img src="<?php echo $profile_picture; ?>" alt="" width="32" height="32"
                                 class="rounded-circle me-2">
                        <?php else : ?>
                            <img src="../../assets/images/user-solid.svg" alt="" width="32" height="32"
                                 class="rounded-circle me-2">
                        <?php endif; ?>
                        <strong><?php echo $username; ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="/profiel">Profiel</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/php/functions/logout.php">Uitloggen</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Einde van de nieuwe sidebar -->

        <!-- Hier komt de rest van je huidige inhoud -->
        <div class="col-md-10 p-3">
            <div class="row">
                <div class="col">
                    <h1><?php echo $currentCategory; ?></h1>
                </div>
                <div class="col text-end">
                    <button id="openAddTaskModal" type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target
                            ="#addTaskModal">
                        Taak toevoegen
                    </button>
                    <button class="btn btn-primary" id="btnSwitch">
                        <img src="../../assets/images/circle-half-stroke-solid.svg" width="20px" height="20px"
                             alt="Icon"/>
                    </button>
                </div>
            </div>
            <ul class="list-group">
                <?php if ($resultTasks->num_rows > 0): ?>
                    <?php while ($rowTask = $resultTasks->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="<?php echo $rowTask['id']; ?>">
                                <label class="form-check-label" for="<?php echo $rowTask['id']; ?>">
                                    <strong><?php echo $rowTask['task']; ?></strong><br>
                                    <span><?php echo $rowTask['description']; ?></span><br>
                                    <small>Deadline: <?php try {
                                            echo (new DateTime($rowTask['deadline']))->format('d-m-Y');
                                        } catch (Exception $e) {
                                        } ?></small>
                                </label>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="list-group-item">Geen taken gevonden</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Nieuwe taak toevoegen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Titel</label>
                        <input type="text" class="form-control" id="taskTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Beschrijving</label>
                        <textarea class="form-control" id="taskDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskCategory" class="form-label">Categorie</label>
                        <select class="form-select" id="taskCategory" required>
                            <option value="">Selecteer een categorie</option>
                            <!-- Loop over de categorieën van de gebruiker -->
                            <?php
                            // Voeg hier de code toe om $userCategories op te halen
                            session_start(); // Start de sessie
                            include 'php/functions/database.php';

                            // Controleer of de gebruiker is ingelogd
                            if (!isset($_SESSION['user_id'])) {
                                // Gebruiker is niet ingelogd, toon een foutmelding of redirect naar de inlogpagina
                                exit("Gebruiker niet ingelogd.");
                            }

                            $user_id = $_SESSION['user_id']; // Haal het ingelogde gebruikers-ID op

                            // Query om unieke categorieën op te halen voor de ingelogde gebruiker
                            $sql = "SELECT DISTINCT category FROM tasks WHERE user_id = ?";
                            $stmt = $connection->prepare($sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            $userCategories = [];
                            while ($row = $result->fetch_assoc()) {
                                $userCategories[] = $row['category'];
                            }

                            // Loop over de categorieën van de gebruiker
                            foreach ($userCategories as $category): ?>
                                <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                            <?php endforeach; ?>
                            <!-- Einde van de loop -->
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="newCategoryCheckbox">
                        <label class="form-check-label" for="newCategoryCheckbox">Nieuwe categorie maken</label>
                    </div>
                    <div class="mb-3" id="newCategoryInput" style="display: none;">
                        <label for="newCategory" class="form-label">Nieuwe categorie</label>
                        <input type="text" class="form-control" id="newCategory">
                    </div>
                    <div class="mb-3">
                        <label for="taskDeadline" class="form-label">Deadline</label>
                        <input type="date" class="form-control" id="taskDeadline" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-primary" id="addTaskButton">Toevoegen</button>
            </div>
        </div>
    </div>
</div>

<?php include 'php/template-parts/footer.php'; ?>
