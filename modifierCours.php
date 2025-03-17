<?php
require_once "conn.php"; // Inclure la connexion à la base de données

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Préparer la requête SQL pour récupérer les informations actuelles
    $sql = "SELECT * FROM course WHERE course_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les données de l'utilisateur
    $cours = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le cours existe
    if ($cours) {
        $course_id = $cours['course_id'];
        $course_name = $cours['course_name'];
        $nombre_heure = $cours['nombre_heure'];
        $description = $cours['description'];
        $filiere_id = $cours['filiere_id'];
        $prof_id = $cours['prof_id'];
        $coefficient = $cours['coefficient'];
        $nivo_scolaire = $cours['nivo_scolaire'];
        $created_at = $cours['created_at'];
    } else {
        echo "Cours non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <title>Modifier le cours</title>
</head>
<body>

    <h2>Modifier le cours</h2>

    <?php if (isset($cours)): ?>
        <form method="POST" class="container mt-5">
            <div class="mb-3">
                <label for="course_id" class="form-label">ID du Cours:</label>
                <input type="text" class="form-control" name="course_id" value="<?= htmlspecialchars($course_id) ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="course_name" class="form-label">Nom du Cours:</label>
                <input type="text" class="form-control" id="course_name" name="course_name" value="<?= htmlspecialchars($course_name) ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($description) ?>" required>
            </div>

            <div class="mb-3">
                <label for="nombre_heure" class="form-label">Nombre d'Heures:</label>
                <input type="number" class="form-control" id="nombre_heure" name="nombre_heure" value="<?= htmlspecialchars($nombre_heure) ?>" required>
            </div>

            <div class="mb-3">
                <label for="filiere_id" class="form-label">ID Filière:</label>
                <input type="text" class="form-control" id="filiere_id" name="filiere_id" value="<?= htmlspecialchars($filiere_id) ?>" required>
            </div>

            <div class="mb-3">
                <label for="prof_id" class="form-label">ID Professeur:</label>
                <input type="text" class="form-control" id="prof_id" name="prof_id" value="<?= htmlspecialchars($prof_id) ?>" required>
            </div>

            <div class="mb-3">
                <label for="coefficient" class="form-label">Coefficient:</label>
                <input type="text" class="form-control" id="coefficient" name="coefficient" value="<?= htmlspecialchars($coefficient) ?>" required>
            </div>

            <div class="mb-3">
                <label for="nivo_scolaire" class="form-label">Niveau Scolaire:</label>
                <input type="text" class="form-control" id="nivo_scolaire" name="nivo_scolaire" value="<?= htmlspecialchars($nivo_scolaire) ?>" required>
            </div>

            <div class="mb-3">
                <label for="created_at" class="form-label">Date de Création:</label>
                <input type="text" class="form-control" id="created_at" name="created_at" value="<?= htmlspecialchars($created_at) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Modifier</button>
        </form>
    <?php endif; ?>

    <script src="../sql/bootstrap-5.0.2-dist/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once "conn.php"; // Inclure la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $nombre_heure = $_POST['nombre_heure'];
    $description = $_POST['description'];
    $filiere_id = $_POST['filiere_id'];
    $prof_id = $_POST['prof_id'];
    $coefficient = $_POST['coefficient'];
    $nivo_scolaire = $_POST['nivo_scolaire'];
    $created_at = $_POST['created_at'];

    try {
        // Préparer la requête SQL pour mettre à jour le cours
        $sql = "UPDATE course
                SET
                    course_name = :course_name,
                    nombre_heure = :nombre_heure,
                    description = :description,
                    filiere_id = :filiere_id,
                    prof_id = :prof_id,
                    coefficient = :coefficient,
                    nivo_scolaire = :nivo_scolaire,
                    created_at = :created_at
                WHERE course_id = :course_id";

        $stmt = $conn->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':course_name', $course_name);
        $stmt->bindParam(':nombre_heure', $nombre_heure);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':filiere_id', $filiere_id);
        $stmt->bindParam(':prof_id', $prof_id);
        $stmt->bindParam(':coefficient', $coefficient);
        $stmt->bindParam(':nivo_scolaire', $nivo_scolaire);
        $stmt->bindParam(':created_at', $created_at);

        // Exécuter la mise à jour
        $stmt->execute();
        
        // Rediriger après succès
        header("Location: cours.php?message=success");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
