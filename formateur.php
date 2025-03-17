<?php
require_once "conn.php";  // Connexion à la base de données
include_once "session_start.php";

// Vérification si l'utilisateur est connecté et a le rôle de "formateur"
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
    header("Location: login.php");  // Redirection vers la page de connexion si non connecté ou rôle invalide
    exit;
}
$formateur_id = $_SESSION['user_id'];

if (!$formateur_id) {
    header("Location: login.php");
    exit();
}

try {
    // Récupérer les filières et groupes associés au formateur
    $queryFilieres = "SELECT DISTINCT fi.filiere_id, fi.description 
                      FROM filiere fi 
                      JOIN formateur f ON f.filiere_id = fi.filiere_id
                      WHERE f.user_id = :id";
    $stmtFilieres = $conn->prepare($queryFilieres);
    $stmtFilieres->bindParam(':id', $formateur_id, PDO::PARAM_INT);
    $stmtFilieres->execute();
    $filieres = $stmtFilieres->fetchAll(PDO::FETCH_ASSOC);
    $stmtFilieres->closeCursor();

    $queryGroupes = "SELECT DISTINCT c.group_id, c.nom 
                     FROM class c 
                     JOIN formateur fo ON fo.group_id = c.group_id 
                     WHERE fo.user_id = :id";
    $stmtGroupes = $conn->prepare($queryGroupes);
    $stmtGroupes->bindParam(':id', $formateur_id, PDO::PARAM_INT);
    $stmtGroupes->execute();
    $groupes = $stmtGroupes->fetchAll(PDO::FETCH_ASSOC);
    $stmtGroupes->closeCursor();
} catch (PDOException $e) {
    die("Erreur lors de l'exécution des requêtes : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Formateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead {
            background-color: #0d6efd;
            color: white;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <?php include_once 'navForm.php'; ?>

    <!-- Contenu principal -->
    <div class="container mt-5">
        <h1 class="mb-4 fw-bold border-bottom pb-2">Tableau de Bord</h1>

        <!-- Formulaire de recherche -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Rechercher des Stagiaires</h4>
            </div>
            <div class="card-body">
                <form action="" method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label for="filiere_id" class="form-label">Filière</label>
                        <select name="filiere_id" id="filiere_id" class="form-select" required>
                            <option value="" disabled selected>-- Choisir une filière --</option>
                            <?php foreach ($filieres as $filiere): ?>
                                <option value="<?= htmlspecialchars($filiere['filiere_id']) ?>">
                                    <?= htmlspecialchars($filiere['description']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="group_id" class="form-label">Groupe</label>
                        <select name="group_id" id="group_id" class="form-select" required>
                            <option value="" disabled selected>-- Choisir un groupe --</option>
                            <?php foreach ($groupes as $groupe): ?>
                                <option value="<?= htmlspecialchars($groupe['group_id']) ?>">
                                    <?= htmlspecialchars($groupe['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Affichage des résultats -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Résultats de la Recherche</h4>
                </div>
                <div class="card-body">
                    <?php
                    $filiere_id = $_POST['filiere_id'];
                    $group_id = $_POST['group_id'];

                    try {
                        $stmt = $conn->prepare("CALL GetStagiairesByFiliereAndGroup(:filiere_id, :group_id)");
                        $stmt->bindParam(':filiere_id', $filiere_id, PDO::PARAM_STR);
                        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_STR);
                        $stmt->execute();
                        $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($stagiaires)) {
                            echo "<table class='table table-striped'>";
                            echo "<thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Groupe</th>
                                        <th>Filière</th>
                                    </tr>
                                  </thead><tbody>";
                            foreach ($stagiaires as $stagiaire) {
                                echo "<tr>
                                        <td>{$stagiaire['ID']}</td>
                                        <td>{$stagiaire['Nom']}</td>
                                        <td>{$stagiaire['Email']}</td>
                                        <td>{$stagiaire['Groupe']}</td>
                                        <td>{$stagiaire['Filiere']}</td>
                                      </tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='alert alert-warning'>Aucun stagiaire trouvé pour ces critères.</div>";
                        }
                    } catch (PDOException $e) {
                        echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cartes de statistiques -->
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users me-2"></i>Stagiaires par Groupe</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT c.nom AS Group_name, COUNT(s.stg_id) AS total_stagiaires
                                        FROM formateur fo
                                        JOIN filiere f ON fo.filiere_id = f.filiere_id
                                        JOIN class c ON c.group_id = fo.group_id
                                        JOIN stagiaire s ON s.filiere_id = f.filiere_id AND s.group_id = c.group_id
                                        WHERE fo.user_id = :id_formateur
                                        GROUP BY c.group_id";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':id_formateur', $formateur_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($results) {
                                    echo "<ul class='list-unstyled'>";
                                    foreach ($results as $row) {
                                        echo "<li>{$row['Group_name']}: <strong>{$row['total_stagiaires']}</strong></li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "Aucun stagiaire trouvé.";
                                }
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users me-2"></i>Stagiaires par Filière</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT f.description, COUNT(s.stg_id) AS total_stagiaires
                                        FROM formateur fo
                                        JOIN filiere f ON fo.filiere_id = f.filiere_id
                                        JOIN stagiaire s ON s.filiere_id = f.filiere_id
                                        WHERE fo.user_id = :id_formateur
                                        GROUP BY f.description";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':id_formateur', $formateur_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($results) {
                                    echo "<ul class='list-unstyled'>";
                                    foreach ($results as $row) {
                                        echo "<li>{$row['description']}: <strong>{$row['total_stagiaires']}</strong></li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "Aucun stagiaire trouvé.";
                                }
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-chalkboard-teacher me-2"></i>Formateurs Inscrits</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT COUNT(*) AS total_formateurs FROM users WHERE role = 'formateur'";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $result['total_formateurs'];
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-user-shield me-2"></i>Admins Inscrits</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo $result['total_admins'];
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users me-2"></i>Stagiaires par Groupe</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT group_id As group_id, course_name As course_name
                                        FROM formateur  where user_id =:user_id
                                       
                                        ";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':user_id', $formateur_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($results) {
                                    echo "<ul class='list-unstyled'>";
                                    foreach ($results as $row) {
                                        echo "<li>{$row['group_id']}: <strong>{$row['course_name']}</strong></li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "Aucun stagiaire trouvé.";
                                }
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>