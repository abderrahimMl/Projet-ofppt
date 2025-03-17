<?php
require_once "conn.php"; // Connexion à la base de données
include_once "session_start.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

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
function afficherStagiaires()
{
    global $conn; // Connexion à la base de données

    try {
        // Appel de la procédure stockée pour récupérer tous les stagiaires
        $stmt = $conn->prepare("CALL GetAllStagiaires()");
        $stmt->execute();
        $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les stagiaires dans un tableau
        if (!empty($stagiaires)) {
            echo "<table class='table text-center table-striped table-bordered mt-3'>
                    <thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Cin</th>
                            <th>Nom</th>
                            <th>Groupe</th>
                            <th>Email</th>
                            <th>Filière</th>
                            <th>Nivo_scolaire</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($stagiaires as $stagiaire) {
                echo "<tr>
    <td>" . htmlspecialchars($stagiaire['id']) . "</td>
    <td>" . htmlspecialchars($stagiaire['Cin']) . "</td>
    <td>" . htmlspecialchars($stagiaire['nom']) . "</td>
    <td>" . htmlspecialchars($stagiaire['group_name']) . "</td>
    <td>" . htmlspecialchars($stagiaire['email']) . "</td>
    <td>" . htmlspecialchars($stagiaire['filiere']) . "</td>
    <td>" . htmlspecialchars($stagiaire['nivo_scolaire']) . "</td>
    <td>" . "<a href='modifierStagiaire.php?id=" . urlencode(htmlspecialchars($stagiaire['id'])) . "' class='btn btn-warning btn-sm'>Modifier</a> " 
          . "<a href='suppStagiaire.php?id=" . urlencode(htmlspecialchars($stagiaire['id'])) . "' class='btn btn-danger btn-sm' 
             onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce stagiaire ? Cette action est irréversible.')\">Supprimer</a>"
    . "</td>
</tr>";

            }
            echo "</tbody></table>";
        } else {
            echo "<p>Aucun stagiaire trouvé.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur lors de l'exécution de la requête : " . $e->getMessage() . "</p>";
    }
}

// Fonction pour rechercher des stagiaires par groupe
function rechercherStagiairesParGroupe($group_name)
{
    global $conn;

    try {
        // Appel de la procédure stockée pour récupérer les stagiaires par groupe
        $stmt = $conn->prepare("CALL GetStagiairesByGroup(:group_name)");
        $stmt->bindParam(':group_name', $group_name, PDO::PARAM_STR);
        $stmt->execute();
        $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les stagiaires dans un tableau
        if (!empty($stagiaires)) {
            echo "<table class='table text-center table-striped table-bordered mt-3'>
                    <thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Cin</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Filière</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($stagiaires as $stagiaire) {
                echo "<tr>
                        <td>" . htmlspecialchars($stagiaire['stagiaire_id']) . "</td>
                        <td>" . htmlspecialchars($stagiaire['Cin']) . "</td>
                        <td>" . htmlspecialchars($stagiaire['stagiaire_nom']) . "</td>
                        <td>" . htmlspecialchars($stagiaire['user_email']) . "</td>
                        <td>" . htmlspecialchars($stagiaire['filiere_id']) . "</td>
                        <td>" ."<a href='modifierStagiaire.php?id=" . urlencode(htmlspecialchars($stagiaire['stagiaire_id'])) .
                         "' class='btn btn-warning btn-sm'>Modifier</a>" . "<a href='suppStagiaire.php?id=" . urlencode(htmlspecialchars($stagiaire['stagiaire_id'])) . "' class='btn btn-danger btn-sm' 
             onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce stagiaire ? Cette action est irréversible.')\">Supprimer</a>"
    . "</td>
                        </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Aucun stagiaire trouvé pour ce groupe.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur lors de l'exécution de la requête : " . $e->getMessage() . "</p>";
    }
}
// recuperer tout les group

 $queryGroupes = "SELECT c.group_id, c.nom FROM class c  "; 
    $stmtGroupes = $conn->prepare($queryGroupes);
    $stmtGroupes->execute();
    $groupes = $stmtGroupes->fetchAll(PDO::FETCH_ASSOC);

    // Libérer le curseur après avoir utilisé la requête
    $stmtGroupes->closeCursor();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Stagiaires</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #f8f9fa; /* Light gray background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add shadow to navbar */
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .btn-primary {
            background: #6a11cb;
            border: none;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #2575fc;
        }

        .btn-outline-primary {
            border-width: 2px;
            font-weight: bold;
        }

        .btn-outline-primary:hover {
            color: white !important;
        }

        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background: linear-gradient(135deg, #6a11cb, #2575fc); /* Gradient background */
            color: white;
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }

        h2 {
            color: #333;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .input-group {
            max-width: 500px;
            margin: auto;
        }

        .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
        }

        .form-select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }
    </style>
</head>
<body>
<?php include_once 'nav_espaceStgagire.php'?>

    <div class="container-fluid p-4">
        
        <div class="row mt-5"></div>
        <!-- Affichage des stagiaires -->
        <div class="mt-3">
            <h2 class="text-center">Liste des Stagiaires</h2>
            <form method="POST" class="text-center">
                <button type="submit" name="afficher" class="btn btn-primary btn-lg">Afficher les Stagiaires</button>
            </form>

            <div class="mt-3">
                <?php
                // Appeler la fonction afficherStagiaires() si le bouton est cliqué
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['afficher'])) {
                    afficherStagiaires();
                }
                ?>
            </div>
        </div>

        <!-- Formulaire de recherche de stagiaires par groupe -->
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="text-center text-primary">Rechercher des Stagiaires par Groupe</h2>
                <form method="POST" class="mb-4">
                    <div class="mb-3 input-group">
                        <label for="group_name" class="form-label">Nom du Groupe :</label>
                        <select name="group_name" id="group_name" class="form-select" required>
                            <option value="" disabled selected>-- Sélectionnez un groupe --</option>
                            <?php foreach ($groupes as $groupe): ?>
                                <option value="<?= htmlspecialchars($groupe['group_id']) ?>">
                                    <?= htmlspecialchars($groupe['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-outline-primary">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Résultats de la recherche -->
        <div class="row">
            <div class="col-12">
                <?php
                // Recherche des stagiaires par groupe si le formulaire est soumis
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'])) {
                    $group_name = strtoupper($_POST['group_name']); // Convertir le nom du groupe en majuscule
                    rechercherStagiairesParGroupe($group_name);
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>