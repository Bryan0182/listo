<?php
$pagetitle = "Inloggen";
include 'php/template-parts/head.php';
include 'php/template-parts/header.php';

// Start de sessie als deze nog niet is gestart
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
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
                <h2 class="text-center">Inloggen</h2>
                <form action="/inloggen" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Gebruikersnaam</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Wachtwoord</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Inloggen</button>
                </form>
            </div>
        </div>
    </div>

<?php
include 'php/template-parts/footer.php';
?>