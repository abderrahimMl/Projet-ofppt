<?php
include_once "conn.php";


function ajouterStagiaire() {
    global $conn;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = htmlspecialchars($_POST['nom']);
        $departement = htmlspecialchars($_POST['departement']);
        $course = htmlspecialchars($_POST['course_name']);
        $filiere_id = htmlspecialchars($_POST['filiere_id']);
        $group_id = htmlspecialchars($_POST['group_id']);

        $id_user = htmlspecialchars($_POST['user_id']);

        try {
            $stmt = $conn->prepare("
                INSERT INTO formateur (nom, departement,course_name,filiere_id,group_id,user_id) 
                VALUES (:nom, :departement,  :course_name,:filiere_id,:group_id,:user_id)
            ");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':departement', $departement);
            $stmt->bindParam(':course_name', $course);
            $stmt->bindParam(':filiere_id', $filiere_id);
            $stmt->bindParam(':group_id', $group_id);
            $stmt->bindParam(':user_id', $id_user);

            $stmt->execute();

            echo "<p class='alert alert-success'>Formateur ajouté avec succès !</p>";
        } catch (PDOException $e) {
            echo "<p class='alert alert-danger'>Erreur : " . $e->getMessage() . "</p>";
        }
    }
}

ajouterStagiaire();

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    
        // Préparer et exécuter la requête pour récupérer les informations de l'utilisateur
        $stmtUser = $conn->prepare("SELECT id, nom FROM users WHERE id = :id");
        $stmtUser->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtUser->execute();
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
        // Vérifier si l'utilisateur existe
        if ($user) {
            $userNom = $user['nom'];
            $userid = $user['id'];
        } else {
            $userNom = ''; // Si l'utilisateur n'existe pas, laisser vide
            $userid = '';  // Si l'utilisateur n'existe pas, laisser vide
        }
    } else {
        $userNom = ''; // Si aucun ID n'est passé, laisser vide
        $userid = '';  // Si aucun ID n'est passé, laisser vide
    }


    // Récupérer les departement
    $stmtDep = $conn->query("SELECT d.nom FROM departement d");
    $departemets = $stmtDep->fetchAll(PDO::FETCH_ASSOC);
    // Récupérer les course

    $stmtCors = $conn->query("SELECT course_name FROM course ");
    $courses = $stmtCors->fetchAll(PDO::FETCH_ASSOC);
    // Récupérer les utilisateurs
    $stmtUser = $conn->query("SELECT id, email FROM users");
    $users = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les filières
    $stmtFiliere = $conn->query("SELECT filiere_id, description FROM filiere");
    $filieres = $stmtFiliere->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
    // Récupérer les groupes
    $stmtGroup = $conn->query("SELECT c.group_id , c.nom FROM class c  ");
    $stmtGroup->execute();
    $groupes = $stmtGroup->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Formateur</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body>
    <div id="formContainer" class="container mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <button class="btn btn-outline-dark"><a href="espace_form.php" class="text-decoration-none">Retour</a></button>
                <button class="btn btn-outline-primary"><a href="espace_stg.php" class="text-decoration-none">Voir stagiaires</a></button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Ajouter un Formateur</h2>
                <form method="POST">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" value="<?= htmlspecialchars($userNom ?? '') ?>" class="form-control" id="nom" name="nom" placeholder="Entrez le nom du formateur" required>
                    </div>
                    <div class="mb-3">
                        <label for="departement" class="form-label">Département</label>
                        <select class="form-select" id="departement" name="departement" required>
                            <option value="" selected disabled>Choisissez un département</option>
                            <?php foreach ($departemets as $departement): ?>
                                <option value="<?= htmlspecialchars($departement['nom']) ?>"><?= htmlspecialchars($departement['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Nom du Cours</label>
                        <select class="form-select" id="course_name" name="course_name" required>
                            <option value="" selected disabled>Choisissez un cours</option>
                            <option value="--Apres--" >--Apres--</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= htmlspecialchars($course['course_name']) ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                            <label for="group_id" class="form-label">Nom Groupe</label>
                            <select class="form-select" id="group_id" name="group_id" required>
                                <option value=""  disabled>Choisissez un groupe</option>
                                <option value="<?= htmlspecialchars($usergroup ?? '')?>" selected><?= htmlspecialchars($usergroup ?? '')?></option>
                                <?php foreach ($groupes as $groupe): ?>
                                    <option value="<?= htmlspecialchars($groupe['group_id']) ?>"><?= htmlspecialchars($groupe['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    <div class="mb-3">
                        <label for="filiere_id" class="form-label">Filière</label>
                        <select class="form-select" id="filiere_id" name="filiere_id" required>
                            <option value="" selected disabled>Choisissez une filière</option>
                            <option value="<?= htmlspecialchars($userfilere ?? '')?>" selected><?= htmlspecialchars($usergroup ?? '')?></option>

                            <?php foreach ($filieres as $filiere): ?>
                                <option value="<?= htmlspecialchars($filiere['filiere_id']) ?>"><?= htmlspecialchars($filiere['description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">ID Utilisateur</label>
                        <input type="number" name="user_id" value="<?= htmlspecialchars($userid ?? '') ?>" class="form-control" id="id_user" placeholder="Entrez l'ID de l'utilisateur" required>
                    </div>
                    <button type="submit" name="ajouter" class="btn btn-primary">Ajouter le Formateur</button>
                </form>
            </div>
        </div>
    </div>
    <script src="../sql/bootstrap-5.0.2-dist/bootstrap.bundle.min.js"></script>
</body>
</html>
