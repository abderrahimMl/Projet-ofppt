<?php
require_once "conn.php"; // Connexion à la base de données
include_once "session_start.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Récupérer le nombre des demandes faites dans les dernières 48 heures
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM demmande_attiss 
    WHERE date > DATE_SUB(NOW(), INTERVAL 48 HOUR)
");
$stmt->execute();
$count = $stmt->fetchColumn();  // Récupérer le nombre de demandes

// Afficher le nombre dans le badge
echo "<span class='badge bg-danger'>$count</span>";


?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <a class="navbar-brand text-light fw-bold fs-3" href="admin.php">OFPPT</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active text-dark fs-4" href="espace_stg.php">Espace stagiaire</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fs-4" href="espace_form.php">Espace Formateur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fs-4" href="#">Contact</a>
                </li>
            </ul>
            <!-- Icône de notification -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
        <a class="nav-link text-dark fs-4" href="demande_attestation.php">
            <i class="fas fa-bell"></i> <!-- Icône de notification -->
            <?php
            // Affichage du badge avec le nombre de demandes des 48 dernières heures
            echo "<span class='badge bg-danger'>$count</span>";
            ?>
        </a>
    </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fs-4" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Voir Plus
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="ajouterUsers.php">Ajouter Users</a></li>
                        <li><a class="dropdown-item" href="ajouterGroupe.php">Ajouter Groupes</a></li>
                        <li><a class="dropdown-item" href="ajouterCours.php">Ajouter Cours</a></li>
                        <li><a class="dropdown-item" href="ajouterFiliere.php">Ajouter Filieres</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fs-4" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Autre
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="cours.php">Cours</a></li>
                        <li><a class="dropdown-item" href="filiere.php">Filieres</a></li>
                        <li><a class="dropdown-item" href="afficherForm.php">Formateur</a></li>
                        <li><a class="dropdown-item" href="afficherdepartement.php">Departements</a></li>
                        <li><a class="dropdown-item" href="aficherInfoista.php">aficherInfoista</a></li>
                        <a href="logout.php">Se déconnecter</a>

                    </ul>
                </li>
            </ul>
        </div>
    </nav>