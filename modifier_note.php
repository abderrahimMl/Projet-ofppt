<?php
require_once "conn.php"; // Connexion à la base de données

// Vérifiez si un ID de note est fourni dans l'URL
include_once "session_start.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'formateur') {
    header("Location: login.php");
    exit();
}
$id1 = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur depuis la session


$id = $_GET['id'];

try {
    // Requête pour récupérer les données de la note
    $sql = "SELECT * FROM notes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $note = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$note) {
        echo "<div class='alert alert-danger'>Erreur : Note introuvable.</div>";
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controle1 = $_POST['controle1'];
    $controle2 = $_POST['controle2'];
    $controle3 = $_POST['controle3'];
    $controle4 = $_POST['controle4'];
    $efm = $_POST['efm'];
    $coef = $_POST['coef'];
    $course_name = $_POST['course_name'];

    // Validation des données
    if ($controle1 > 20 || $controle2 > 20 || $controle3 > 20 || $controle4 > 20 || $efm > 40) {
        echo "<div class='alert alert-danger'>Erreur : Les notes ne doivent pas dépasser leur limite.</div>";
    } else {
        try {
            // Requête de mise à jour
            $updateSql = "UPDATE notes 
                          SET controle1 = :controle1, 
                              controle2 = :controle2, 
                              controle3 = :controle3, 
                              controle4 = :controle4, 
                              efm = :efm, 
                              coef = :coef, 
                              course_name = :course_name 
                          WHERE id = :id";
            $stmt = $conn->prepare($updateSql);
            $stmt->bindParam(':controle1', $controle1, PDO::PARAM_INT);
            $stmt->bindParam(':controle2', $controle2, PDO::PARAM_INT);
            $stmt->bindParam(':controle3', $controle3, PDO::PARAM_INT);
            $stmt->bindParam(':controle4', $controle4, PDO::PARAM_INT);
            $stmt->bindParam(':efm', $efm, PDO::PARAM_INT);
            $stmt->bindParam(':coef', $coef, PDO::PARAM_INT);
            $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        header("Location: afficher_note.php?message=success");


        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur lors de la mise à jour : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
try{
    $queryCourse = "SELECT c.course_name
                      FROM course c 
                      JOIN formateur f ON f.prof_id = c.prof_id
                      WHERE f.user_id = :id"; 
    $stmtCourse = $conn->prepare($queryCourse);
    $stmtCourse->bindParam(':id', $id1, PDO::PARAM_INT); // Lier l'ID de l'utilisateur à la requête
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
    <title>Modifier Note</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier la note</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="controle1" class="form-label">Contrôle 1</label>
                <input type="number" class="form-control" id="controle1" name="controle1" value="<?= htmlspecialchars($note['controle1']) ?>" >
            </div>
            <div class="mb-3">
                <label for="controle2" class="form-label">Contrôle 2</label>
                <input type="number" class="form-control" id="controle2" name="controle2" value="<?= htmlspecialchars($note['controle2']) ?>" >
            </div>
            <div class="mb-3">
                <label for="controle3" class="form-label">Contrôle 3</label>
                <input type="number" class="form-control" id="controle3" name="controle3" value="<?= htmlspecialchars($note['controle3']) ?>" >
            </div>
            <div class="mb-3">
                <label for="controle4" class="form-label">Contrôle 4</label>
                <input type="number" class="form-control" id="controle4" name="controle4" value="<?= htmlspecialchars($note['controle4']) ?>" >
            </div>
            <div class="mb-3">
                <label for="efm" class="form-label">EFM</label>
                <input type="number" class="form-control" id="efm" name="efm" value="<?= htmlspecialchars($note['efm']) ?>" >
            </div>
            <div class="mb-3">
                <label for="coef" class="form-label">Coef</label>
                <input type="number" class="form-control" id="coef" name="coef" value="<?= htmlspecialchars($note['coef']) ?>" >
            </div>
            <div class="mb-3">
                <label for="course_name" class="form-label">Module</label>
                <select id="" name="course_name" class="form-select" required>
                                    <option value="" disabled selected>--choisir--</option>
                                    <?php foreach ($Courses as $course): ?>
                                        <option value="<?= htmlspecialchars($note['course_name']) ?>" selected> <?= htmlspecialchars($note['course_name']) ?></option>
                                        <option value="<?= htmlspecialchars($course['course_name']) ?>">
                                            <?= htmlspecialchars($course['course_name']) ?>
                                        </option>
                                            <?php endforeach; ?>
                            </select>            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
