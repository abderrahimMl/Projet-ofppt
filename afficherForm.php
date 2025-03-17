<?php
require_once "conn.php"; // Inclure la connexion à la base de données
require_once 'alerssuccess.php';
// Suppression d'un formateur
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $sql = "DELETE FROM formateur WHERE prof_id = :delete_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: afficherForm.php?message=success");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupérer les données d'un formateur spécifique pour modification
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM formateur WHERE prof_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $formateur = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mise à jour du formateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $prof_id = $_POST['prof_id'];
    $nom = $_POST['nom'];
    $departement = $_POST['departement'];
 
    $filiere_id = $_POST['filiere_id'];

    try {
        $sql = "UPDATE formateur
                SET nom = :nom,
                    departement = :departement,
                   
                    filiere_id = :filiere_id
                WHERE prof_id = :prof_id";
        $stmt = $conn->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':prof_id', $prof_id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':departement', $departement);
        
        $stmt->bindParam(':filiere_id', $filiere_id);

        // Exécuter la mise à jour
        $stmt->execute();
        header("Location: afficherForm.php?message=seccess");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupérer tous les formateurs
$sql = "SELECT f.*,c.course_name FROM formateur f join course c on f.prof_id=c.prof_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$formateur_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Formateurs</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body>
<?php include_once 'navadmin.php'?>

<div class="container mt-5">
    <h2>Gestion des Formateurs</h2>

    <!-- Formulaire de modification d'un formateur -->
    <?php if (isset($formateur)): ?>
        <h3>Modifier le Formateur</h3>
        <form method="POST">
            <input type="hidden" name="prof_id" value="<?= htmlspecialchars($formateur['prof_id']) ?>">

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($formateur['nom']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="departement" class="form-label">Département</label>
                <input type="text" class="form-control" name="departement" value="<?= htmlspecialchars($formateur['departement']) ?>" required>
            </div>

            

            <div class="mb-3">
                <label for="filiere_id" class="form-label">ID Filière</label>
                <input type="text" class="form-control" name="filiere_id" value="<?= htmlspecialchars($formateur['filiere_id']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">ID Utilisateur</label>
                <input type="text" class="form-control" name="user_id" value="<?= htmlspecialchars($formateur['user_id']) ?>" readonly>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
        </form>
    <?php endif; ?>

    <!-- Liste des formateurs -->
    <h3 class="mt-5">Liste des Formateurs</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Département</th>
                <th>Cours</th>
                
                <th>ID Filière</th>
                <th>ID Groupe</th>
                <th>ID Utilisateur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formateur_list as $formateur): ?>
                <tr>
                    <td><?= htmlspecialchars($formateur['prof_id']) ?></td>
                    <td><?= htmlspecialchars($formateur['nom']) ?></td>
                    <td><?= htmlspecialchars($formateur['departement']) ?></td>
                    <td><?= htmlspecialchars($formateur['course_name']) ?></td>
                    <td><?= htmlspecialchars($formateur['filiere_id']) ?></td>
                    <td><?= htmlspecialchars($formateur['group_id']) ?></td>
                    <td><?= htmlspecialchars($formateur['user_id']) ?></td>
                    <td>
                        <a href="?id=<?= $formateur['prof_id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="?delete_id=<?= $formateur['prof_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../sql/bootstrap-5.0.2-dist/bootstrap.bundle.min.js"></script>
</body>
</html>
