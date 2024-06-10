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
    header("Location: /inloggen");
    exit();
}

// De gebruiker is ingelogd, haal de gebruikersgegevens op
$user_id = $_SESSION['user_id'];

$sql = "SELECT profile_picture FROM users WHERE id = $user_id";
$result = $connection->query($sql);
$row = $result->fetch_assoc();
$profile_picture = $row['profile_picture'];

// Haal de gebruikersgegevens op uit de database
$sql = "SELECT username, email, first_name, last_name FROM users WHERE id = $user_id";
$result = $connection->query($sql);
$row = $result->fetch_assoc();
$username = $row['username'];
$email = $row['email'];
$first_name = $row['first_name'];
$last_name = $row['last_name'];
?>
<?php
$pagetitle = "Update profiel";
include 'php/template-parts/head.php';
?>

<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 min-vh-100">
                <a href="/" class="d-flex align-items-center text-decoration-none logo">
                    <img src="/assets/images/listo-logo-small.webp" width="40" height="40" alt="Listo Logo">
                    <span class="fs-5 d-none d-sm-inline">Listo</span>
                </a>
                <hr>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                    <li class="nav-item">
                        <a href="/profiel" class="nav-link">Profiel</a>
                    </li>
                    <li class="nav-item">
                        <a href="" class="nav-link active">Profiel bewerken</a>
                    </li>
                    <li class="nav-item">
                        <a href="/profiel/wachtwoord" class="nav-link">Wachtwoord wijzigen</a>
                    </li>
                    <li class="nav-item">
                        <a href="/uitloggen" class="nav-link">Uitloggen</a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown pb-3">
                    <a href="#"
                       class="d-flex align-items-center text-white text-decoration-none dropdown-toggle profile-dropdown"
                       id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($profile_picture)) : ?>
                            <img src="/<?php echo $profile_picture; ?>" alt="" width="32" height="32"
                                 class="rounded-circle me-2">
                        <?php else : ?>
                            <img src="/assets/images/user-solid.svg" alt="" width="32" height="32"
                                 class="rounded-circle me-2">
                        <?php endif; ?>
                        <strong><?php echo $username; ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#">Profiel</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/uitloggen">Uitloggen</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Einde van de nieuwe sidebar -->

        <!-- Hier komt de rest van je huidige inhoud -->
        <div class="col-md-9 p-3">
            <h1>Profiel bijwerken</h1>
            <p>Hier kunt u uw profielinformatie bekijken en bewerken.</p>
            <form action="/profiel/update" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="username" class="form-label">Gebruikersnaam</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mailadres</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">Voornaam</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Achternaam</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Profielfoto</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    <?php if (!empty($profile_picture)) : ?>
                        <img src="/<?php echo $profile_picture; ?>" alt="Profielfoto <?php echo $username; ?>" width="100" height="100" class="mt-2">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Profiel bijwerken</button>
            </form>
        </div>
    </div>
</div>

<?php include 'php/template-parts/footer.php'; ?>
