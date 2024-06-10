<?php
include 'php/functions/database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
$pagetitle = "Profiel";
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
                        <a href="/profiel" class="nav-link active">Profiel</a>
                    </li>
                    <li class="nav-item">
                        <a href="/profiel/update" class="nav-link">Profiel bewerken</a>
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
                        <?php if (!empty($profile_picture) && $profile_picture != '') : ?>
                            <img src="/<?php echo $profile_picture; ?>" alt="" width="32" height="32"
                                 class="rounded-circle me-2">
                        <?php else : ?>
                            <img src="../../assets/images/user-solid.svg" alt="" width="32" height="32"
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
            <h1>Mijn profiel</h1>
            <p>Hier kunt u uw profielinformatie bekijken en bewerken.</p>
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';

                unset($_SESSION['success_message']);
            }

            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';

                unset($_SESSION['error_message']);
            }
            ?>
            <img src="/<?php echo $profile_picture; ?>" alt="Profielfoto <?php echo $username; ?>" width="100" height="100" class="mb-4">
            <p>Gebruikersnaam: <?php echo $username; ?></p>
            <p>E-mailadres: <?php echo $email; ?></p>
            <p>Voornaam: <?php echo $first_name; ?></p>
            <p>Achternaam: <?php echo $last_name; ?></p>
        </div>
    </div>
</div>

<?php include 'php/template-parts/footer.php'; ?>
