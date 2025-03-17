<?php
require_once "conn.php"; // Inclure la connexion à la base de données
include_once "session_start.php";
// Vérifier si un ID est passé en paramètre GET
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // Récupérer les informations actuelles du stagiaire
    $sql = "SELECT * FROM stagiaire WHERE stg_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stagiaire) {
        echo "Stagiaire non trouvé.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modifier le Stagiaire</title>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Modifier les Informations du Stagiaire</h2>
        <div class="card shadow-lg">
            <div class="card-body">
                <?php if (isset($stagiaire)): ?>
                    <form method="POST">
                        <input type="hidden" name="stg_id" value="<?= htmlspecialchars($stagiaire['stg_id']) ?>">

                        <div class="mb-3">
                            <label for="Cin" class="form-label">CIN :</label>
                            <input type="text" class="form-control" name="Cin" value="<?= htmlspecialchars($stagiaire['Cin']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom :</label>
                            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($stagiaire['nom']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="anne" class="form-label">Année :</label>
                            <input type="text" class="form-control" name="anne" value="<?= htmlspecialchars($stagiaire['created_at']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="group" class="form-label">Groupe :</label>
                            <input type="text" class="form-control" name="group" value="<?= htmlspecialchars($stagiaire['group_id']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="filiere_id" class="form-label">Filière :</label>
                            <input type="text" class="form-control" name="filiere_id" value="<?= htmlspecialchars($stagiaire['filiere_id']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="nivo_scol" class="form-label">Niveau scolaire :</label>
                            <select class="form-select" name="nivo_scol" required>
                                <option value="<?= htmlspecialchars($stagiaire['nivo_scolaire']) ?>" selected>
                                    <?= htmlspecialchars($stagiaire['nivo_scolaire']) ?>
                                </option>
                                <option value="1ere_annee">1ère_année</option>
                                <option value="2eme_annee">2ème_année</option>
                                <option value="3eme_annee">3ème_année</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Modifier</button>
                    </form>
                <?php else: ?>
                    <p class="text-danger text-center">Aucun stagiaire trouvé.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données envoyées par le formulaire
    $stg_id = $_POST['stg_id'];
    $Cin = $_POST['Cin'];
    $nom = $_POST['nom'];
    $anne = $_POST['anne'];
    $group = $_POST['group'];
    $filiere = $_POST['filiere_id'];
    $nivo_scolaire = $_POST['nivo_scol'];
    $updated_at = date('Y-m-d H:i:s'); // Date de mise à jour

    try {
        // Requête SQL pour mettre à jour le stagiaire
        $sql = "UPDATE stagiaire
                SET 
                    Cin = :Cin,
                    nom = :nom,
                    group_id = :group_id,
                    filiere_id = :filiere_id,
                    nivo_scolaire = :nivo_scol,
                    created_at = :anne,
                    updated_at = :updated_at
                WHERE stg_id = :stg_id";

        $stmt = $conn->prepare($sql);

        // Liaison des paramètres
        $stmt->bindParam(':Cin', $Cin);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':anne', $anne);
        $stmt->bindParam(':group_id', $group);
        $stmt->bindParam(':filiere_id', $filiere);
        $stmt->bindParam(':nivo_scol', $nivo_scolaire);
        $stmt->bindParam(':updated_at', $updated_at);
        $stmt->bindParam(':stg_id', $stg_id);

        // Exécution de la requête
        $stmt->execute();

        // Redirection en cas de succès
        header("Location: espace_stg.php?message=success");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
