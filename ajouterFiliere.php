<?php
require_once "conn.php";  // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $filiere_id = $_POST['filiere_id'];
    $description = $_POST['description'];
    $nombre_max = $_POST['nombre_max'];

    // Requête SQL pour insérer une nouvelle filière dans la table filiere
    $query = "INSERT INTO filiere (filiere_id, description, nombre_max,created_at) VALUES (:filiere_id, :description, :nombre_max,NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':filiere_id', $filiere_id);
    $stmt->bindParam( ':description',$description);
    $stmt->bindParam(':nombre_max', $nombre_max);
    
    if ($stmt->execute()) {
        echo "<script>alert('operation reussite')</script>";
    } else {
        echo "<p class='bg-danger text-white fw-bold fs-5'>Erreur lors de l'ajout de la filière : " . $stmt->error . "</p>";
    }

  
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une filière</title>
    <!-- Lien vers Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-success fs-5">
<?php include_once 'navadmin.php'?>


    <div class="container mt-5">
        
        <h2 class="fw-bold text-primary">Ajouter une nouvelle filière</h2>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="filiere_id" class="form-label">ID de la filière</label>
                <input type="text" class="form-control" id="filiere_id" name="filiere_id" required>
                <small class="form-text text-muted">L'ID de la filière doit être unique.</small>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="nombre_max" class="form-label">Nombre maximum d'étudiants</label>
                <input type="number" class="form-control" id="nombre_max" name="nombre_max" required>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter la filière</button>
        </form>
    </div>

    <!-- Lien vers Bootstrap JS et Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
