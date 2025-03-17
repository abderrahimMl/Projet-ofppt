<?php
require_once "conn.php"; // Connexion à la base de données

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Récupération des données du formulaire
        $cin = $_POST['cin'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashage du mot de passe
        $nom = $_POST['nom'];
        $genre = $_POST['genre'];
        $nationalite = $_POST['nationalite'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $adress = $_POST['adress'];
        $phone = $_POST['phone'];
        $date_naissance = $_POST['date_naissance'];
        $filiere = $_POST['filiere_id'] ;
        $groupe = $_POST['group_id'] ;
        $nivo_scolaire = $_POST['nivo_scolaire']; // Nouvelle variable

        // Requête SQL pour insérer un nouvel utilisateur
        $query = "INSERT INTO users (cin, password, nom, genre, nationalite, email, role, adress, phone, date_naissance, filiere_id, group_id, nivo_scolaire) 
                  VALUES (:cin, :password, :nom, :genre, :nationalite, :email, :role, :adress, :phone, :date_naiss, :filiere, :groupe, :nivo_scolaire)";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':cin', $cin);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':nationalite', $nationalite);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':adress', $adress);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date_naiss', $date_naissance);
        $stmt->bindParam(':filiere', $filiere);
        $stmt->bindParam(':groupe', $groupe);
        $stmt->bindParam(':nivo_scolaire', $nivo_scolaire);

        if ($stmt->execute()) {
            echo "<p class='bg-success text-white fw-bold fs-5'>Utilisateur ajouté avec succès !</p>";
        } else {
            echo "<p class='bg-danger text-white fw-bold fs-5'>Erreur lors de l'ajout de l'utilisateur.</p>";
        }
    }

    // Requête pour récupérer les filières
    $queryFilieres = "SELECT filiere_id, description FROM filiere";
    $stmtFilieres = $conn->prepare($queryFilieres);
    $stmtFilieres->execute();
    $filieres = $stmtFilieres->fetchAll(PDO::FETCH_ASSOC);

    // Requête pour récupérer les groupes
    $queryGroupes = "SELECT group_id, nom FROM class";
    $stmtGroupes = $conn->prepare($queryGroupes);
    $stmtGroupes->execute();
    $groupes = $stmtGroupes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body class="bg-light text-success fs-5">
<?php include_once 'navadmin.php'?>

    <div class="container mt-5">
        <h2 class="fw-bold text-primary">Ajouter un nouvel utilisateur</h2>
        <form action="" method="POST">
            <div class="row">
                <!-- Champs du formulaire -->
                <div class="mb-3 col-12 col-lg-6">
                    <label for="cin" class="form-label">CIN :</label>
                    <input type="text" class="form-control" id="cin" name="cin">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="email" class="form-label">Email :</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" class="form-control" id="nom" name="nom">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="genre" class="form-label">Genre :</label>
                    <select class="form-select" id="genre" name="genre">
                        <option value="" disabled selected>-- Sélectionner --</option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                    </select>
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="nationalite" class="form-label">Nationalité :</label>
                    <input type="text" class="form-control" id="nationalite" name="nationalite">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="role" class="form-label">Rôle :</label>
                    <select class="form-select" id="role" name="role">
                        <option value="" disabled selected>-- Sélectionner --</option>
                        <option value="Admin">Admin</option>
                        <option value="Formateur">Formateur</option>
                        <option value="Stagiaire">Stagiaire</option>
                    </select>
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="adress" class="form-label">Adresse :</label>
                    <input type="text" class="form-control" id="adress" name="adress">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="phone" class="form-label">Numéro de téléphone :</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="date_naissance" class="form-label">Date de naissance :</label>
                    <input type="date" class="form-control" id="date_naissance" name="date_naissance">
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="filiere_id" class="form-label">Filière :</label>
                    <select name="filiere_id" id="filiere_id" class="form-select">
                        <option value="" disabled selected>-- Choisir une filière --</option>
                        <?php foreach ($filieres as $filiere): ?>
                            <option value="<?= htmlspecialchars($filiere['filiere_id']) ?>">
                                <?= htmlspecialchars($filiere['filiere_id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="group_id" class="form-label">Groupe :</label>
                    <select name="group_id" id="group_id" class="form-select">
                        <option value="" disabled selected>-- Sélectionnez un groupe --</option>
                        <?php foreach ($groupes as $groupe): ?>
                            <option value="<?= htmlspecialchars($groupe['group_id']) ?>">
                                <?= htmlspecialchars($groupe['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="nivo_scolaire" class="form-label">Niveau scolaire :</label>
                    <input type="text" class="form-control" id="nivo_scolaire" name="nivo_scolaire">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter l'utilisateur</button>
        </form>
    </div>
    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.js"></script>
</body>
</html>
