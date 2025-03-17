<?php
require_once "conn.php";  // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $group_id = $_POST['group_id'];
    $nom = $_POST['nom'];
    $filiere_id = $_POST['filiere_id'];

    try {
        // Insertion dans la table class avec ajout de la date de création
        $query = "INSERT INTO class (group_id, nom, filiere_id, created_at) 
                  VALUES (:grp_id, :nom, :fil_id, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':grp_id', $group_id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':fil_id', $filiere_id);

        if ($stmt->execute()) {
            echo "<p class='bg-success text-white fw-bold fs-5'>Groupe ajouté avec succès !</p>";
        } else {
            echo "<p class='bg-danger text-white fw-bold fs-5'>Erreur lors de l'ajout du groupe.</p>";
        }
    } catch (PDOException $e) {
        die("<p class='bg-danger text-white fw-bold fs-5'>Erreur : " . $e->getMessage() . "</p>");
    }
}

// Récupération des filières pour afficher dans la liste déroulante
try {
    $queryFilieres = "SELECT filiere_id, description FROM filiere";
    $stmtFilieres = $conn->prepare($queryFilieres);
    $stmtFilieres->execute();
    $filieres = $stmtFilieres->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<p class='bg-danger text-white fw-bold fs-5'>Erreur lors de la récupération des filières : " . $e->getMessage() . "</p>");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un groupe</title>
    <!-- Lien vers Bootstrap CSS -->
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body class="bg-light text-success fs-5">
<?php include_once 'navadmin.php'?>

    <div class="container mt-5">
        <h2 class="fw-bold text-primary">Ajouter un groupe</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="group_id" class="form-label">ID du groupe</label>
                <input type="text" class="form-control" id="group_id" name="group_id" required>
                <small class="form-text text-muted">L'ID du groupe doit être unique (par ex. : GRP001, DEV002).</small>
            </div>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom du groupe</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>

            <div class="mb-3">
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

            <button type="submit" class="btn btn-primary">Ajouter le groupe</button>
        </form>
    </div>

    <!-- Lien vers Bootstrap JS -->
    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.js" crossorigin="anonymous"></script>
</body>
</html>
