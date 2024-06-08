<?php
$pagetitle="404 - Pagina niet gevonden";
include 'php/template-parts/head.php';
include 'php/template-parts/header.php';

// Start de sessie als deze nog niet is gestart
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h2>404 - Pagina niet gevonden</h2>
            <div class="alert alert-danger mt-4 mb-4">Oeps! De pagina die je zoekt kon niet worden gevonden.</div>
            <a href="/" class="btn btn-primary">Terug naar de startpagina</a>
        </div>
    </div>
</div>

<?php
include 'php/template-parts/footer.php';
?>
