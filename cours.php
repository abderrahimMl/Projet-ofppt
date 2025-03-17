<?php
require_once "conn.php"; // Connexion à la base de données
include_once "session_start.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Afficher le message si présent
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    echo  "<script type='text/javascript'>alert('Opération réussie!');</script>";;

    // Insérer un script JavaScript pour rediriger après un délai
    echo "<script>
        setTimeout(function() {
            window.location.href = '" . strtok($_SERVER['REQUEST_URI'], '?') . "';
        }, 1000); // Rediriger après 1 secondes
    </script>";
}
// Fonction pour afficher tous les stagiaires

    try {
        // Appel de la procédure stockée pour récupérer tous les stagiaires
        $stmt = $conn->prepare("SELECT * FROM course");
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les stagiaires dans un tableau
        if (!empty($courses)) {
            echo " <div class='row m-5'> <div class='mt-3'></div>
            </div><h2>Liste des cours</h2><table  class='table table-striped table-bordered mt-3'>
                    <thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>nombre_heure</th>
                            <th>description</th>
                            <th>filiere_id</th>
                            <th>prof_id</th>
                            <th>coefficient</th>
                            <th>nivo_scolaire</th>
                            <th>created_at</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($courses as $cour) {
                echo "<tr>
                        <td>" . htmlspecialchars($cour['course_id']) . "</td>
                        <td>" . htmlspecialchars($cour['course_name']) . "</td>
                        <td>" . htmlspecialchars($cour['nombre_heure']) . "</td>
                        <td>" . htmlspecialchars($cour['description']) . "</td>
                        <td>" . htmlspecialchars($cour['filiere_id']) . "</td>
                        <td>" . htmlspecialchars($cour['prof_id']) . "</td>
                        <td>" . htmlspecialchars($cour['coefficient']) . "</td>
                        <td>" . htmlspecialchars($cour['nivo_scolaire']) . "</td>
                        <td>" . htmlspecialchars($cour['created_at']) . "</td>
                        <td>" ."<a href='modifierCours.php?id=" . urlencode(htmlspecialchars($cour['course_id'])) . "' class='btn btn-warning btn-sm'>Modifier</a>" .
                        "<a href='suppCours.php?id=" . urlencode(htmlspecialchars($cour['course_id'])) . "' class='btn btn-danger btn-sm'>Supprimer</a> ";
                        "</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Aucun cours trouvé.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur lors de l'exécution de la requête : " . $e->getMessage() . "</p>";
    }


// Fonction pour rechercher des stagiaires par groupe
function rechercherStagiairesParGroupe($course_name)
{
    global $conn;

    try {
        // Appel de la procédure stockée pour récupérer les stagiaires par groupe
        $stmt = $conn->prepare("select * from course where course_id=:course_id");
        $stmt->bindParam(':course_id', $course_name, PDO::PARAM_STR);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les stagiaires dans un tableau
        if (!empty($courses)) {
            echo "<table class='table table-striped table-bordered mt-3'>
                    <thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>nombre_heure</th>
                            <th>description</th>
                            <th>filiere_id</th>
                            <th>prof_id</th>
                            <th>coefficient</th>
                            <th>nivo_scolaire</th>
                            <th>created_at</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($courses as $cour) {
                echo "<tr>
                       <td>" . htmlspecialchars($cour['course_id']) . "</td>
                        <td>" . htmlspecialchars($cour['course_name']) . "</td>
                        <td>" . htmlspecialchars($cour['nombre_heure']) . "</td>
                        <td>" . htmlspecialchars($cour['description']) . "</td>
                        <td>" . htmlspecialchars($cour['filiere_id']) . "</td>
                        <td>" . htmlspecialchars($cour['prof_id']) . "</td>
                        <td>" . htmlspecialchars($cour['coefficient']) . "</td>
                        <td>" . htmlspecialchars($cour['nivo_scolaire']) . "</td>
                        <td>" . htmlspecialchars($cour['created_at']) . "</td>
                        <td>" ."<a href='modifierCours.php?id=" . urlencode(htmlspecialchars($cour['course_id'])) . "' class='btn btn-warning btn-sm'>Modifier</a>" .
                        "<a href='suppCours.php?id=" . urlencode(htmlspecialchars($cour['course_id'])) . "' class='btn btn-danger btn-sm'>Supprimer</a> ";
                        "</td>
                        </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Aucun course trouvé pour ce nom.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur lors de l'exécution de la requête : " . $e->getMessage() . "</p>";
    }
}
// recuperer tout les group

 $queryGroupes = "SELECT c.course_id, c.course_name FROM course c  "; 
    $stmtGroupes = $conn->prepare($queryGroupes);
    $stmtGroupes->execute();
    $groupes = $stmtGroupes->fetchAll(PDO::FETCH_ASSOC);

    // Libérer le curseur après avoir utilisé la requête
    $stmtGroupes->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stagiaires - OFPPT</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body class="bg-light">
<?php include_once 'navadmin.php'?>

    <div class="container-fluid p-fixed">
        <!-- Barre de navigation -->
        
        <div class="row mt-5"></div>

        <!-- Affichage des stagiaires -->
        <div class="mt-3">
            <h2>Liste des course</h2>
           
        </div>

        <!-- Formulaire de recherche de stagiaires par groupe -->
        <div class="row mt-3">
            <div class="col-12">
                <h2 class="text-primary">Rechercher des Cours par Nom</h2>
                <form method="POST" class="mb-4">
                    <div class="mb-3 input-group">
                        <label for="course_name" class="form-label">Nom du Groupe :</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="" disabled selected>-- Sélectionnez un course --</option>
                            <?php foreach ($groupes as $groupe): ?>
                                <option value="<?= htmlspecialchars($groupe['course_id']) ?>">
                                    <?= htmlspecialchars($groupe['course_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>                        <button type="submit" class="btn input-group-text btn-outline-primary">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Résultats de la recherche -->
        <div class="row">
            <div class="col-12">
                <?php
                // Recherche des stagiaires par groupe si le formulaire est soumis
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
                    $group_name = strtoupper($_POST['course_id']); // Convertir le nom du groupe en majuscule
                    rechercherStagiairesParGroupe($group_name);
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.js" crossorigin="anonymous"></script>
</body>
</html>
