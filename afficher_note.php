<?php
// Connexion à la base de données
include_once 'conn.php';
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    echo  "<script type='text/javascript'>alert('Opération réussie!');</script>";;

    // Insérer un script JavaScript pour rediriger après un délai
    echo "<script>
        setTimeout(function() {
            window.location.href = '" . strtok($_SERVER['REQUEST_URI'], '?') . "';
        }, 1000); // Rediriger après 1 secondes
    </script>";
}

// Gestion des actions du formulaire
if (isset($_POST['valider'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $filiere_id = $_POST['filiere_id'];
        $group_id = $_POST['group_id'];

        try {
            $stmt = $conn->prepare("CALL FiltrerNotesParGroupeFiliere(:group_id, :filiere_id)");
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_STR);
            $stmt->bindParam(':filiere_id', $filiere_id, PDO::PARAM_STR);
            $stmt->execute();
            $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

        } catch (PDOException $e) {
            die("Erreur lors de l'exécution de la procédure : " . $e->getMessage());
        }
    }
}

if (isset($_POST['rechercher'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_stagiaire = $_POST['id_stagiaire'];
        $groupe_id_in = $_POST['id_groupe_in'];

        try {
            $stmt = $conn->prepare("CALL AfficherNotesParStagiaire(:id_stagiaire, :id_groupe_in)");
            $stmt->bindParam(':id_stagiaire', $id_stagiaire, PDO::PARAM_INT);
            $stmt->bindParam(':id_groupe_in', $groupe_id_in, PDO::PARAM_STR);
            $stmt->execute();

            $notesParId = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            die("Erreur lors de l'exécution de la procédure : " . $e->getMessage());
        }
    }
    
}
include_once "session_start.php";
$id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur depuis la session
echo $id;
try {
    // Requête pour récupérer les filières depuis la table `filiere` en joignant avec la table `formateur`
    $queryFilieres = "SELECT DISTINCT fi.filiere_id, fi.description 
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
} catch (PDOException $e) {
    die("Erreur lors de l'exécution des requêtes : " . $e->getMessage());
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrer les Notes</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #f8f9fa; /* Light gray background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add shadow to navbar */
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-link {
            font-size: 1.1rem;
            color: #fff !important; /* White text for nav links */
        }

        .nav-link.active {
            font-weight: bold;
        }

        .form-label {
            font-weight: 500;
            color: #333;
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

        .alert {
            border-radius: 5px;
        }

        .action-buttons a {
            text-decoration: none;
            margin: 0 5px;
        }

        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<?php include_once 'navForm.php'; ?>
<div class="container mt-5">
    <!-- Navbar -->
    

    <!-- Main Content -->
    <div class="row mt-5 pt-4">
        <div class="col-12">
            <!-- Filter Form -->
            <form action="" method="POST" class="row g-3">
                <div class="col-md-6">
                    <label for="filiere_id" class="form-label">Choisissez la filière</label>
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
                <div class="col-12">
                    <button type="submit" name="valider" class="btn btn-primary">Rechercher</button>
                </div>
            </form>

            <!-- Display Notes -->
            <?php if (isset($notes) && !empty($notes)): ?>
                <table class="table table-bordered mt-5">
                    <thead>
                    <tr>
                        <th>ID Note</th>
                        <th>Nom Stagiaire</th>
                        <th>Contrôle 1</th>
                        <th>Contrôle 2</th>
                        <th>Contrôle 3</th>
                        <th>Contrôle 4</th>
                        <th>EFM</th>
                        <th>Coef</th>
                        <th>Moyenne</th>
                        <th>Nom du Cours</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notes as $note): ?>
                        <tr>
                            <td><?= htmlspecialchars($note['id_note']) ?></td>
                            <td><?= htmlspecialchars($note['nom_stagiaire']) ?></td>
                            <td><?= htmlspecialchars($note['controle1']) ?></td>
                            <td><?= htmlspecialchars($note['controle2']) ?></td>
                            <td><?= htmlspecialchars($note['controle3']) ?></td>
                            <td><?= htmlspecialchars($note['controle4']) ?></td>
                            <td><?= htmlspecialchars($note['EFM']) ?></td>
                            <td><?= htmlspecialchars($note['coef']) ?></td>
                            <td><?= htmlspecialchars($note['moyenne']) ?></td>
                            <td><?= htmlspecialchars($note['course_name']) ?></td>
                            <td class="action-buttons">
                                <a href="modifier_note.php?id=<?= $note['id_note'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="supprimer_note.php?id=<?= $note['id_note'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (isset($notes)): ?>
                <div class="alert alert-warning mt-5">Aucune note trouvée pour les critères sélectionnés.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search by ID Form -->
    <div class="row mt-5">
        <div class="col-12">
            <form action="" method="POST" class="row g-3">
                <div class="col-md-6">
                    <label for="id_stagiaire" class="form-label">Rechercher par ID</label>
                    <input type="number" class="form-control" name="id_stagiaire" placeholder="Entrer un ID" required>
                </div>
                <div class="col-md-6">
                    <label for="id_groupe_in" class="form-label">ID Groupe</label>
                    <select name="id_groupe_in" id="group_id" class="form-select" required>
                        <option value="" disabled selected>-- Sélectionnez un groupe --</option>
                        <?php foreach ($groupes as $groupe): ?>
                            <option value="<?= htmlspecialchars($groupe['group_id']) ?>">
                                <?= htmlspecialchars($groupe['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" name="rechercher" class="btn btn-success">Rechercher</button>
                </div>
            </form>

            <!-- Display Notes by ID -->
            <?php if (isset($notesParId) && !empty($notesParId)): ?>
                <table class="table table-bordered mt-5">
                    <thead>
                    <tr>
                        <th>ID Note</th>
                        <th>Nom Stagiaire</th>
                        <th>Contrôle 1</th>
                        <th>Contrôle 2</th>
                        <th>Contrôle 3</th>
                        <th>Contrôle 4</th>
                        <th>EFM</th>
                        <th>Coef</th>
                        <th>Moyenne</th>
                        <th>Nom du Cours</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($notesParId as $note): ?>
                        <tr>
                            <td><?= htmlspecialchars($note['id']) ?></td>
                            <td><?= htmlspecialchars($note['nom_stagiaire']) ?></td>
                            <td><?= htmlspecialchars($note['controle1']) ?></td>
                            <td><?= htmlspecialchars($note['controle2']) ?></td>
                            <td><?= htmlspecialchars($note['controle3']) ?></td>
                            <td><?= htmlspecialchars($note['controle4']) ?></td>
                            <td><?= htmlspecialchars($note['efm']) ?></td>
                            <td><?= htmlspecialchars($note['coef']) ?></td>
                            <td><?= htmlspecialchars($note['moyenne']) ?></td>
                            <td><?= htmlspecialchars($note['course_name']) ?></td>
                            <td class="action-buttons">
                                <a href="modifier_note.php?id=<?= $note['id'] ?>" class="btn btn-sm btn-primary">Modifier</a>
                                <a href="supprimer_note.php?id=<?= $note['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (isset($notesParId)): ?>
                <div class="alert alert-warning mt-5">Aucune note trouvée pour les critères sélectionnés.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>