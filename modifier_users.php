<?php
require_once "conn.php"; // Inclure la connexion à la base de données

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Préparer la requête SQL pour récupérer les informations actuelles
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les données de l'utilisateur
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe
    if ($user) {
        $login = $user['cin'];
        $password = password_hash($user['password'], PASSWORD_BCRYPT);
        $nom = $user['nom'];
        $genre = $user['genre'];
        $nationalite = $user['nationalite'];
        $email = $user['email'];
        $role = $user['role'];
        $adress = $user['adress'];
        $phone = $user['phone'];
        $date_naissance = $user['date_naissance'];
    } else {
        echo "Utilisateur non trouvé.";
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <title>Modifier l'utilisateur</title>
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h2 class="mb-4">Modifier l'utilisateur</h2>

        <?php if (isset($user)): ?>
            <form method="POST" class="bg-white p-4 rounded shadow-sm">
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

                <div class="mb-3">
                    <label for="login" class="form-label">Login:</label>
                    <input type="text" id="login" name="login" value="<?= htmlspecialchars($login) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" value="<?= htmlspecialchars($password) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom:</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="genre" class="form-label">Genre:</label>
                    <select id="genre" name="genre" class="form-select" required>
                        <option selected value="<?= htmlspecialchars($genre) ?>"><?= htmlspecialchars($genre) ?></option>
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nationalite" class="form-label">Nationalité:</label>
                    <input type="text" id="nationalite" name="nationalite" value="<?= htmlspecialchars($nationalite) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role:</label>
                    <input type="text" id="role" name="role" value="<?= htmlspecialchars($role) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="adress" class="form-label">Adresse:</label>
                    <input type="text" id="adress" name="adress" value="<?= htmlspecialchars($adress) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Téléphone:</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="date_naissance" class="form-label">Date de Naissance:</label>
                    <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($date_naissance) ?>" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Modifier</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="../sql/bootstrap-5.0.2-dist/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once "conn.php"; // Inclure la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $id = $_POST['id'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $nom = $_POST['nom'];
    $genre = $_POST['genre'];
    $nationalite = $_POST['nationalite'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $adress = $_POST['adress'];
    $phone = $_POST['phone'];
    $date_naissance = $_POST['date_naissance'];

    try {
        // Préparer la requête SQL pour mettre à jour l'utilisateur
        $sql = "UPDATE users
                SET
                    cin = :login,
                    password = :password,
                    nom = :nom,
                    genre = :genre,
                    nationalite = :nationalite,
                    email = :email,
                    role = :role,
                    adress = :adress,
                    phone = :phone,
                    date_naissance = :date_naissance
                WHERE id = :id";

        $stmt = $conn->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':nationalite', $nationalite);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':adress', $adress);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':id', $id);

        // Exécuter la mise à jour
        $stmt->execute();
        
        // Rediriger après succès
        header("Location: add_stagiaire.php?message=success");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }

}
?>
