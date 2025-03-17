<?php
// Connexion à la base de données
require_once "conn.php";
include_once "session_start.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Vérifier si le paramètre 'message' est passé dans l'URL


// Afficher le message si présent
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    echo  "<script type='text/javascript'>alert('Opération réussie!');</script>";;

    // Insérer un script JavaScript pour rediriger après un délai
    echo "<script>
        setTimeout(function() {
            window.location.href = '" . strtok($_SERVER['REQUEST_URI'], '?') . "';
        }, 1000); // Rediriger après 1 secondes
    </script>";
}
// Fonction pour afficher les utilisateurs
// Le code PHP pour afficher la liste des utilisateurs (fonction déjà fournie)
function afficherUtilisateurs() {
    global $conn;

    try {
        // Appel de la procédure stockée
        $stmt = $conn->prepare("CALL GetAllUsersForm()");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        // Affichage des utilisateurs dans un tableau
        if (!empty($users)) {
            echo "<div class=' col-12 table-striped table-responsive table-bordered'>";

            echo "<table class='table col-12 table-striped table-responsive table-bordered'>";
            echo "<thead class='table-dark'>
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Password</th>
                        <th>Nom</th>
                        <th>Genre</th>
                        <th>Nationalité</th>
                        <th>Email</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Date de naissance</th>
                        <th>Enrollement</th>
                        <th>Actions</th>
                    </tr>
                  </thead>";
            echo "<tbody>";
            foreach ($users as $user) {
                echo "<tr>
                <td>" . htmlspecialchars($user['id']) . "</td>
                <td>" . htmlspecialchars($user['cin']) . "</td>
                <td>" . htmlspecialchars($user['password']) . "</td>
                <td>" . htmlspecialchars($user['nom']) . "</td>
                <td>" . htmlspecialchars($user['genre']) . "</td>
                <td>" . htmlspecialchars($user['nationalite']) . "</td>
                <td>" . htmlspecialchars($user['email']) . "</td>
                <td>" . htmlspecialchars($user['adress']) . "</td>
                <td>" . htmlspecialchars($user['phone']) . "</td>
                <td>" . htmlspecialchars($user['date_naissance']) . "</td>
                <td>" . htmlspecialchars($user['created_at']) . "</td>
                <td>
                    <div class='btn-group'>
                        <a href='add_form.php?id=" . htmlspecialchars($user['id']) . "' class='btn btn-danger btn-sm'>Ajouter</a>
                        <a href='modifier_users.php?id=" . htmlspecialchars($user['id']) . "' class='btn btn-warning btn-sm'>Modifier</a>
                        <a href='suppUsers.php?id=" . htmlspecialchars($user['id']) . "' class='btn btn-dark btn-sm' 
                           onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');\">Supprimer</a>
                    </div>
                </td>
            </tr>";
        "</div>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Aucun utilisateur trouvé.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur lors de l'exécution de la requête : " . $e->getMessage() . "</p>";
    }
}


// partie pour la recherch
// Initialiser les variables de résultats et de message
$users = [];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rechercher'])) {
    // Récupérer les paramètres du formulaire
    $id = !empty($_POST['id']) ? $_POST['id'] : NULL;
    $nom = !empty($_POST['nom']) ? $_POST['nom'] : NULL;
    $genre = !empty($_POST['genre']) ? $_POST['genre'] : NULL;
    $nationalite = !empty($_POST['nationalite']) ? $_POST['nationalite'] : NULL;
    $adress = !empty($_POST['adress']) ? $_POST['adress'] : NULL;
    $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : NULL;
    $enrollement = !empty($_POST['enrollement']) ? $_POST['enrollement'] : NULL;

    try {
        // Préparer et exécuter l'appel à la procédure stockée
        $stmt1 = $conn->prepare("CALL GetUsersForm(?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bindParam(1, $id, PDO::PARAM_INT);
        $stmt1->bindParam(2, $nom, PDO::PARAM_STR);
        $stmt1->bindParam(3, $genre, PDO::PARAM_STR);
        $stmt1->bindParam(4, $nationalite, PDO::PARAM_STR);
        $stmt1->bindParam(5, $adress, PDO::PARAM_STR);
        $stmt1->bindParam(6, $date_naissance, PDO::PARAM_STR);
        $stmt1->bindParam(7, $enrollement, PDO::PARAM_STR);
        $stmt1->execute();
        // Récupérer les résultats
        $users = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt1->closeCursor(); 
    } catch (PDOException $e) {
        $message = "Erreur lors de l'exécution : " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Stagiaire</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
</head>
<body>
    <div class="container-fluid p-fixed">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                    <a class="navbar-brand text-light fw-bold fs-3" href="#">OFPPT</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active text-dark fs-4" href="admin.php">Accueil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark fs-4" href="add_form.php">Ajouter Formateur</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark fs-4" href="#">Contact</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h2>Liste les derniere 10 Utilisateurs formateur</h2>
                <form method="POST">
                    <button type="submit" name="afficher" class="btn btn-primary">Afficher les Utilisateurs</button>
                </form>

                <div class="mt-3">
                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['afficher'])) {
                        afficherUtilisateurs();
                    }
                    ?>
                </div>

                <div id="formContainer"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            <h2 class="mb-4">Recherche d'utilisateurs(choisissez un champ)</h2>

<!-- Formulaire de recherche -->
<form method="POST" class="row g-3">
    <div class="col-md-2">
        <label for="id" class="form-label">ID</label>
        <input type="number" class="form-control" id="id" name="id" placeholder="ID">
    </div>

    <div class="col-md-3">
        <label for="nom" class="form-label">Nom</label>
        <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom">
    </div>

    <div class="col-md-2">
        <label for="genre" class="form-label">Genre</label>
        <select id="genre" name="genre" class="form-select">
            <option value="">Tous</option>
            <option value="Homme">Homme</option>
            <option value="Femme">Femme</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="nationalite" class="form-label">Nationalité</label>
        <input type="text" class="form-control" id="nationalite" name="nationalite" placeholder="Nationalité">
    </div>

    <div class="col-md-3">
        <label for="adress" class="form-label">Adresse</label>
        <input type="text" class="form-control" id="adress" name="adress" placeholder="Adresse">
    </div>

    <div class="col-md-2">
        <label for="date_naissance" class="form-label">Date de Naissance</label>
        <input type="date" class="form-control" id="date_naissance" name="date_naissance">
    </div>

    <div class="col-md-2">
        <label for="enrollement" class="form-label">Date d'enrôlement</label>
        <input type="date" class="form-control" id="enrollement" name="enrollement">
    </div>

    <div class="col-md-12">
        <button type="submit" name="rechercher" class="btn btn-primary">Rechercher</button>
    </div>
</form>
<!-- Message d'erreur -->
<?php if (!empty($message)): ?>
    <div class="alert alert-danger mt-3"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Table des résultats -->
<?php if (!empty($users)): ?>
    <div class="table-responsive  mt-4">
        <table class="table table-dark table-sm table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Genre</th>
                    <th>Nationalité</th>
                    <th>Adresse</th>
                    <th>Date de Naissance</th>
                    <th>Enrôlement</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>  <!-- Assurez-vous que la colonne 'id' existe -->
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['genre']) ?></td>
                        <td><?= htmlspecialchars($user['nationalite']) ?></td>
                        <td><?= htmlspecialchars($user['adress']) ?></td>
                        <td><?= htmlspecialchars($user['date_naissance']) ?></td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td>
    <div class='btn-group'>
        <a href='add_form.php?id=<?= htmlspecialchars($user["id"]) ?>' class='btn btn-danger btn-sm'>Ajouter</a>
        <a href='modifier_users.php?id=<?= htmlspecialchars($user["id"]) ?>' class='btn btn-warning btn-sm'>Modifier</a>
        <a href='suppUsers.php?id=<?= htmlspecialchars($user["id"]) ?>' class='btn btn-dark btn-sm' 
           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
    </div>
</td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="alert alert-warning mt-4">Aucun utilisateur trouvé.</div>
<?php endif; ?>
</div>

            </div>
        </div>
    </div>

    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.js" crossorigin="anonymous"></script>
    
</body>
</html>
