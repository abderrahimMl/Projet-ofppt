<?php
require_once "conn.php";  // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $nombre_heure = $_POST['nombre_heure'];
    $description = $_POST['description'];
    $filiere_id = $_POST['filiere_id'];
    $prof_id = $_POST['prof_id'];
    $coefficient = $_POST['coefficient'];
    $nivo_scolaire = $_POST['nivo_scolaire'];

    try {
        // Requête SQL pour insérer un nouveau cours dans la table course avec ajout de la date de création
        $query = "INSERT INTO course (course_id, course_name, nombre_heure, description, filiere_id, prof_id, coefficient, nivo_scolaire, created_at) 
                  VALUES (:course_id, :course_name, :nombre_heure, :description, :filiere_id, :prof_id, :coefficient, :nivo_scolaire, NOW())";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':course_name', $course_name);
        $stmt->bindParam(':nombre_heure', $nombre_heure);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':filiere_id', $filiere_id);
        $stmt->bindParam(':prof_id', $prof_id);
        $stmt->bindParam(':coefficient', $coefficient);
        $stmt->bindParam(':nivo_scolaire', $nivo_scolaire);

        if ($stmt->execute()) {
            echo "<p class='bg-success text-white fw-bold fs-5'>Cours ajouté avec succès !</p>";
        } else {
            echo "<p class='bg-danger text-white fw-bold fs-5'>Erreur lors de l'ajout du cours.</p>";
        }
    } catch (PDOException $e) {
        die("<p class='bg-danger text-white fw-bold fs-5'>Erreur : " . $e->getMessage() . "</p>");
    }
}

try {
    // Récupération des filières
    $queryFilieres = "SELECT filiere_id, description FROM filiere";
    $stmtFilieres = $conn->prepare($queryFilieres);
    $stmtFilieres->execute();
    $filieres = $stmtFilieres->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des formateurs
    $queryFormateurs = "SELECT prof_id, nom, departement FROM formateur";
    $stmtFormateurs = $conn->prepare($queryFormateurs);
    $stmtFormateurs->execute();
    $formateurs = $stmtFormateurs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<p class='bg-danger text-white fw-bold fs-5'>Erreur lors de la récupération des données : " . $e->getMessage() . "</p>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un cours</title>
    <!-- Lien vers Bootstrap CSS -->
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body class="bg-light text-success fs-5">
<?php include_once 'navadmin.php'?>
    <div class="container mt-5">
        <h2 class="fw-bold text-primary">Ajouter un nouveau cours</h2>
        <form action="" method="POST">
            <div class="row">
                <div class="mb-3 col-lg-6 col-12">
                    <label for="course_id" class="form-label">ID du cours</label>
                    <input type="text" class="form-control" id="course_id" name="course_id" required>
                    <small class="form-text text-muted">L'ID du cours doit être unique.</small>
                </div>

                <div class="mb-3 col-lg-6 col-12">
                    <label for="course_name" class="form-label">Nom du cours</label>
                    <input type="text" class="form-control" id="course_name" name="course_name" required>
                </div>

                <div class="mb-3 col-lg-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-3 col-lg-6 col-12">
                    <label for="nombre_heure" class="form-label">Nombre d'heures</label>
                    <input type="number" class="form-control" id="nombre_heure" name="nombre_heure" min="1" required>
                </div>

                <div class="mb-3 col-lg-6 col-12">
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

                <div class="mb-3 col-lg-6 col-12">
                    <label for="prof_id" class="form-label">Formateur</label>
                    <select name="prof_id" id="prof_id" class="form-select" required>
                        <option value="" disabled selected>-- Choisir un formateur --</option>
                        <?php foreach ($formateurs as $formateur): ?>
                            <option value="<?= htmlspecialchars($formateur['prof_id']) ?>">
                                <?= htmlspecialchars($formateur['nom']) ?> - <?= htmlspecialchars($formateur['departement']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3 col-lg-6 col-12">
                    <label for="coefficient" class="form-label">Coefficient</label>
                    <input type="number" class="form-control" id="coefficient" name="coefficient" min="1" step="0.1" required>
                </div>

                <div class="mb-3 col-lg-6 col-12">
                    <label for="nivo_scolaire" class="form-label">Niveau scolaire</label>
                    <select name="nivo_scolaire" id="nivo_scolaire" class="form-select" required>
                        <option value="" disabled selected>-- Choisir un niveau --</option>
                        <option value="1ere_anne">1ère année</option>
                        <option value="2eme_anne">2ème année</option>
                        <option value="3eme_anne">3ème année</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Ajouter le cours</button>
        </form>
    </div>

    <!-- Lien vers Bootstrap JS -->
    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.js" crossorigin="anonymous"></script>
</body>
</html>
