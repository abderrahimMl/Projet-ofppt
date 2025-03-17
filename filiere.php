<?php
require_once "conn.php"; // Inclure la connexion à la base de données

// Suppression d'une filière
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $sql = "DELETE FROM filiere WHERE filiere_id = :delete_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: gestion_filiere.php?message=deleted");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupérer les données d'une filière spécifique pour modification
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM filiere WHERE filiere_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $filiere = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mise à jour de la filière
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $filiere_id = $_POST['filiere_id'];
    $description = $_POST['description'];
    $nombre_max = $_POST['nombre_max'];
    $created_at = $_POST['created_at'];

    try {
        $sql = "UPDATE filiere
                SET description = :description, 
                    nombre_max = :nombre_max, 
                    created_at = :created_at
                WHERE filiere_id = :filiere_id";
        $stmt = $conn->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':filiere_id', $filiere_id);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':nombre_max', $nombre_max);
        $stmt->bindParam(':created_at', $created_at);

        // Exécuter la mise à jour
        $stmt->execute();
        header("Location: filiere.php?message=updated");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupérer toutes les filières
$sql = "SELECT * FROM filiere";
$stmt = $conn->prepare($sql);
$stmt->execute();
$filiere_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Filières</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body>
<?php include_once 'navadmin.php'?>


<div class="container mt-5">
    <h2>Gestion des Filières</h2>

    <!-- Formulaire de modification d'une filière -->
    <?php if (isset($filiere)): ?>
        <h3>Modifier la Filière</h3>
        <form method="POST">
            <input type="hidden" name="filiere_id" value="<?= htmlspecialchars($filiere['filiere_id']) ?>">

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" value="<?= htmlspecialchars($filiere['description']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="nombre_max" class="form-label">Nombre Max d'Étudiants</label>
                <input type="number" class="form-control" name="nombre_max" value="<?= htmlspecialchars($filiere['nombre_max']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="created_at" class="form-label">Date de Création</label>
                <input type="text" class="form-control" name="created_at" value="<?= htmlspecialchars($filiere['created_at']) ?>" required>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
        </form>
    <?php endif; ?>

    <!-- Liste des filières -->
    <h3 class="mt-5">Liste des Filières</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Nombre Max</th>
                <th>Date de Création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filiere_list as $filiere): ?>
                <tr>
                    <td><?= htmlspecialchars($filiere['filiere_id']) ?></td>
                    <td><?= htmlspecialchars($filiere['description']) ?></td>
                    <td><?= htmlspecialchars($filiere['nombre_max']) ?></td>
                    <td><?= htmlspecialchars($filiere['created_at']) ?></td>
                    <td>
                        <a href="?id=<?= $filiere['filiere_id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="?delete_id=<?= $filiere['filiere_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../sql/bootstrap-5.0.2-dist/bootstrap.bundle.min.js"></script>
</body>
</html>
