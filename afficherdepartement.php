<?php
require_once "conn.php"; // Inclure la connexion à la base de données

// Suppression d'un département
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $sql = "DELETE FROM departement WHERE id_dep = :delete_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: affcherdepartement.php?message=success");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupérer les données d'un département spécifique pour modification
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM departement WHERE id_dep = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $departement = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mise à jour du département
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id_dep = $_POST['id_dep'];
    //$prof_id = $_POST['prof_id'];
    $filiere_id = $_POST['filiere_id'];
    $created_on = $_POST['created_on'];
    $nom = $_POST['nom'];

    try {
        $sql = "UPDATE departement
                SET 
                    filiere_id = :filiere_id, 
                    created_on = :created_on, 
                    nom = :nom
                WHERE id_dep = :id_dep";
        $stmt = $conn->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':id_dep', $id_dep);
        //$stmt->bindParam(':prof_id', $prof_id);
        $stmt->bindParam(':filiere_id', $filiere_id);
        $stmt->bindParam(':created_on', $created_on);
        $stmt->bindParam(':nom', $nom);

        // Exécuter la mise à jour
        $stmt->execute();
        header("Location: gestion_departement.php?message=updated");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Ajouter un nouveau département
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
   
    $filiere_id = $_POST['filiere_id'];

    $nom = $_POST['nom'];

    try {
        $sql = "INSERT INTO departement ( filiere_id, created_on, nom) 
                VALUES ( :filiere_id, NOW(), :nom)";
        $stmt = $conn->prepare($sql);

        // Lier les paramètres
       // $stmt->bindParam(':prof_id', $prof_id);
        $stmt->bindParam(':filiere_id', $filiere_id);
       
        $stmt->bindParam(':nom', $nom);

        // Exécuter l'insertion
        $stmt->execute();
        header("Location: afficherdepartement.php?message=success");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

// Récupérer tous les départements
$sql = "SELECT * FROM departement";
$stmt = $conn->prepare($sql);
$stmt->execute();
$departement_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// $sql2 = "SELECT prof_id,nom FROM formateur";
// $stmt1 = $conn->prepare($sql2);
// $stmt1->execute();
// $formateurs = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$sql3 = "SELECT filiere_id FROM filiere";
$stmt2 = $conn->prepare($sql3);
$stmt2->execute();
$filieres = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Départements</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body>
<?php include_once 'navadmin.php'?>

<div class="container mt-5">
    <h2>Gestion des Départements</h2>

    <!-- Formulaire pour ajouter un nouveau département -->
    <h3>Ajouter un Nouveau Département</h3>
    <form method="POST">
        
        <div class="mb-3">
            <label for="filiere_id" class="form-label">ID de la Filière</label>
            <select class="form-control" name="filiere_id">
                <option value="" selected disabled>--choisi une filieres---</option>
                <?php
                    // Parcours du tableau des formateurs pour créer les options du select
                    foreach ($filieres as $filiere) {
                    echo '<option value="' . htmlspecialchars($filiere['filiere_id']) . '">' . htmlspecialchars($filiere['filiere_id']) . '</option>';
                     }
                ?>
            </select>
        </div>

       
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du Département</label>
            <input type="text" class="form-control" name="nom" required>
        </div>

        <button type="submit" name="add" class="btn btn-success">Ajouter</button>
    </form>

    <!-- Formulaire de modification d'un département -->
    <?php if (isset($departement)): ?>
        <h3>Modifier le Département</h3>
        <form method="POST">
            <input type="hidden" name="id_dep" value="<?= htmlspecialchars($departement['id_dep']) ?>">

           

            <div class="mb-3">
                <label for="filiere_id" class="form-label">ID de la Filière</label>
                <input type="text" class="form-control" name="filiere_id" value="<?= htmlspecialchars($departement['filiere_id']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="created_on" class="form-label">Date de Création</label>
                <input type="date" class="form-control" name="created_on" value="<?= htmlspecialchars($departement['created_on']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom du Département</label>
                <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($departement['nom']) ?>" required>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Mettre à jour</button>
        </form>
    <?php endif; ?>

    <!-- Liste des départements -->
    <h3 class="mt-5">Liste des Départements</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Filière</th>
                <th>Date de Création</th>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departement_list as $departement): ?>
                <tr>
                    <td><?= htmlspecialchars($departement['id_dep']) ?></td>
                    <td><?= htmlspecialchars($departement['filiere_id']) ?></td>
                    <td><?= htmlspecialchars($departement['created_on']) ?></td>
                    <td><?= htmlspecialchars($departement['nom']) ?></td>
                    <td>
                        <a href="?id=<?= $departement['id_dep'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="?delete_id=<?= $departement['id_dep'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce département ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../sql/bootstrap-5.0.2-dist/bootstrap.bundle.min.js"></script>
</body>
</html>
