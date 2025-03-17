<?php
require_once "conn.php"; // Connexion à la base de données
 
include_once "session_start.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
    header("Location: login.php");
    exit();
}
$id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur depuis la session

// Préparation de la récupération des stagiaires en fonction de la filière et du groupe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['filiere_id']) && isset($_POST['group_id'])) {
        $filiere_id = $_POST['filiere_id'];
        $group_id = $_POST['group_id'];

        try {
            $stmt = $conn->prepare("CALL ajouter_note(:filiere_idn, :group_idn)");
            $stmt->execute([
                ':filiere_idn' => $filiere_id,
                ':group_idn' => $group_id
            ]);

            $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "Erreur : " . htmlspecialchars($e->getMessage());
            exit;
        }
    }
}
// Récupération des modules depuis la table course
try {
    $queryCourse = "SELECT c.course_name
                      FROM course c 
                      join filiere ff on c.filiere_id=ff.filiere_id 
                      join class cs on ff.filiere_id=cs.filiere_id 
                      JOIN formateur f ON f.group_id = cs.group_id  
                      WHERE f.user_id = :id and cs.group_id=:gr and ff.filiere_id=:ff"; // Utilisation de l'alias f pour la table formateur et liaison correcte de l'ID

    $stmtCourse = $conn->prepare($queryCourse);
    $stmtCourse->bindParam(':id', $id, PDO::PARAM_INT); // Lier l'ID de l'utilisateur à la requête
    $stmtCourse->bindParam(':gr', $group_id, PDO::PARAM_STR); // Lier l'ID de l'utilisateur à la requête
    $stmtCourse->bindParam(':ff', $filiere_id, PDO::PARAM_STR); // Lier l'ID de l'utilisateur à la requête
    $stmtCourse->execute(); 
    $Courses = $stmtCourse->fetchAll(PDO::FETCH_ASSOC); // Récupérer les données de filières

    $stmtCourse->closeCursor(); // Liste des modules
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des modules : " . $e->getMessage();
    exit;
}

try {
    // Requête pour récupérer les filières depuis la table `filiere` en joignant avec la table `formateur`
    $queryFilieres = "SELECT DISTINCT fi.filiere_id,  fi.description 
                      FROM filiere fi 
                      JOIN formateur f ON f.filiere_id = fi.filiere_id
                      WHERE f.user_id = :id"; // Utilisation de l'alias f pour la table formateur et liaison correcte de l'ID

    $stmtFilieres = $conn->prepare($queryFilieres);
    $stmtFilieres->bindParam(':id', $id, PDO::PARAM_INT); // Lier l'ID de l'utilisateur à la requête
    $stmtFilieres->execute(); 
    $filieres = $stmtFilieres->fetchAll(PDO::FETCH_ASSOC); // Récupérer les données de filières

    $stmtFilieres->closeCursor();

    // Requête pour récupérer les groupes depuis la table `class`, filtré par une condition, si nécessaire
    $queryGroupes = "SELECT DISTINCT c.group_id, c.nom FROM class c join  formateur fo on fo.group_id=c.group_id where fo.user_id=:id "; // Exemple de condition sur group_id
    $stmtGroupes = $conn->prepare($queryGroupes);
    $stmtGroupes->bindParam(':id', $id, PDO::PARAM_INT); // Lier l'ID de l'utilisateur à la requête
    $stmtGroupes->execute();
    $groupes = $stmtGroupes->fetchAll(PDO::FETCH_ASSOC);

    // Libérer le curseur après avoir utilisé la requête
    $stmtGroupes->closeCursor();

    // filitere les module
    $queryCourse = "SELECT c.course_name
                      FROM course c 
                      JOIN formateur f ON f.prof_id = c.prof_id
                      WHERE f.user_id = :id"; // Utilisation de l'alias f pour la table formateur et liaison correcte de l'ID

    $stmtCourse = $conn->prepare($queryCourse);
    $stmtCourse->bindParam(':id', $id, PDO::PARAM_INT); // Lier l'ID de l'utilisateur à la requête
    $stmtCourse->execute(); 
    $Courses = $stmtCourse->fetchAll(PDO::FETCH_ASSOC); // Récupérer les données de filières

    $stmtCourse->closeCursor();
} catch (PDOException $e) {
    die("Erreur lors de l'exécution des requêtes : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes des Stagiaires</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <!-- Custom CSS -->
    <style>
<style>
    body {
        background: #f8f9fa; /* Light gray background */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
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

    .card-header {
        background: linear-gradient(135deg, #6a11cb, #2575fc); /* Gradient background */
        color: white;
        border-radius: 10px 10px 0 0;
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

    .btn-primary {
        background: #6a11cb;
        border: none;
        transition: background 0.3s;
    }

    .btn-primary:hover {
        background: #2575fc;
    }

    .btn-success {
        background: #28a745;
        border: none;
        transition: background 0.3s;
    }

    .btn-success:hover {
        background: #218838;
    }

    .form-select, .form-control {
        border-radius: 5px;
        border: 1px solid #ced4da;
        padding: 10px;
    }

    .form-select:focus, .form-control:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
    }

    .alert {
        border-radius: 5px;
    }

    .text-center {
        text-align: center;
    }

    .mt-5 {
        margin-top: 3rem;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .shadow-lg {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>    </style>
</head>
<body>
<?php include_once 'navForm.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-12">
           
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header">
                    <h4>Recherche des Stagiaires</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST" class="row g-3">
                        <!-- Sélection de la filière -->
                        <div class="col-md-6">
                            <label for="filiere_id" class="form-label">Sélectionnez une filière</label>
                            <select name="filiere_id" id="filiere_id" class="form-select" required>
                                <option value="" disabled selected>-- Choisir une filière --</option>
                                <?php foreach ($filieres as $filiere): ?>
                                    <option value="<?= htmlspecialchars($filiere['filiere_id']) ?>">
                                        <?= htmlspecialchars($filiere['description']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Sélection du groupe -->
                        <div class="col-md-6">
                            <label for="group_id" class="form-label">Choisissez le groupe</label>
                            <select name="group_id" id="group_id" class="form-select" required>
                                <option value="" disabled selected>-- Sélectionnez un groupe --</option>
                                <?php foreach ($groupes as $groupe): ?>
                                    <option value="<?= htmlspecialchars($groupe['group_id']) ?>">
                                        <?= htmlspecialchars($groupe['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Bouton de recherche -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100">Rechercher</button>
                        </div>
                    </form>
                </div>
            </div>

            <h2 class="text-center mt-4">Notes des Stagiaires</h2>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Contrôle 1</th>
                    <th>Contrôle 2</th>
                    <th>Contrôle 3</th>
                    <th>Contrôle 4</th>
                    <th>EFM/Reg</th>
                    <th>Module</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($stagiaires)): ?>
                    <?php foreach ($stagiaires as $stagiaire): ?>
                        <tr>
                            <td><?= htmlspecialchars($stagiaire['ID']) ?></td>
                            <td id="stagiaire-name-<?= $stagiaire['ID'] ?>"><?= htmlspecialchars($stagiaire['Nom']) ?></td>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm"
                                            onclick="addOrUpdateNote(<?= $stagiaire['ID'] ?>, 'controle<?= $i ?>')">
                                        Add/Edit
                                    </button>
                                    <span id="controle<?= $i ?>-<?= $stagiaire['ID'] ?>">
                                        <?= htmlspecialchars($stagiaire["controle$i"] ?? '') ?>
                                    </span>
                                </td>
                            <?php endfor; ?>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm"
                                        onclick="addOrUpdateNote(<?= $stagiaire['ID'] ?>, 'efm')">
                                    Add/Edit
                                </button>
                                <span id="efm-<?= $stagiaire['ID'] ?>">
                                    <?= htmlspecialchars($stagiaire['efm'] ?? '') ?>
                                </span>
                            </td>
                            <td>
                                <select id="module-<?= $stagiaire['ID'] ?>" class="form-select" required>
                                    <option value="" disabled selected>--choisir--</option>
                                    <?php foreach ($Courses as $course): ?>
                                        <option value="<?= htmlspecialchars($course['course_name']) ?>">
                                            <?= htmlspecialchars($course['course_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Aucun stagiaire trouvé.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript pour gérer les actions Add/Edit -->
<script>
    function addOrUpdateNote(stagiaireId, noteType) {
        const value = prompt(`Entrez une valeur pour ${noteType} :`, "").trim();
        const moduleSelect = document.querySelector(`#module-${stagiaireId}`);
        const selectedModule = moduleSelect ? moduleSelect.value : "";
        const stagiaireName = document.querySelector(`#stagiaire-name-${stagiaireId}`).textContent.trim();

        if (!value || isNaN(value)) {
            alert("Veuillez entrer une valeur numérique valide.");
            return;
        }
        if (!moduleSelect || selectedModule === "") {
            alert("Veuillez sélectionner un module.");
            return;
        }

        fetch('process_note.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                id: stagiaireId,
                type: noteType,
                value: value,
                module: selectedModule,
                name: stagiaireName
            })
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "Succès") {
                document.getElementById(`${noteType}-${stagiaireId}`).innerText = value;
                alert("Note ajoutée/modifiée avec succès !");
            } else {
                alert("Erreur côté serveur : " + data);
            }
        })
        .catch(error => {
            console.error("Request failed", error);
            alert("Une erreur s'est produite lors de l'envoi de la requête.");
        });
    }
</script>
</body>
</html>