<?php
// Connexion à la base de données
require_once "conn.php";

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
        $stmt = $conn->prepare("CALL GetAllUsers()");
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
                        <th>Cin</th>
                        <th>Password</th>
                        <th>Nom</th>
                        <th>Genre</th>
                        <th>Nationalité</th>
                        <th>Email</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Date de naissance</th>
                        <th>Enrollement</th>
                        <th>Groupe</th>
                        <th>Filiere</th>
                        <th>Nivo Scolaire</th>
                        <th>Actions</th>
                    </tr>
                  </thead>";
            echo "<tbody>";
            foreach ($users as $user) {
                echo "<tr>
                <td>" . htmlspecialchars($user['id']) . "</td>
                <td>" . htmlspecialchars($user['Cin']) . "</td>
                <td>" . htmlspecialchars($user['password']) . "</td>
                <td>" . htmlspecialchars($user['nom']) . "</td>
                <td>" . htmlspecialchars($user['genre']) . "</td>
                <td>" . htmlspecialchars($user['nationalite']) . "</td>
                <td>" . htmlspecialchars($user['email']) . "</td>
                <td>" . htmlspecialchars($user['adress']) . "</td>
                <td>" . htmlspecialchars($user['phone']) . "</td>
                <td>" . htmlspecialchars($user['date_naissance']) . "</td>
                <td>" . htmlspecialchars($user['enrollement']) . "</td>
                <td>" . htmlspecialchars($user['group_id']) . "</td>
                <td>" . htmlspecialchars($user['filiere_id']) . "</td>
                <td>" . htmlspecialchars($user['nivo_scolaire']) . "</td>
                <td>
                    <div class='btn-group'>
                        <a href='ajouter_stagiaire.php?id=" . htmlspecialchars($user['id']) . "' class='btn btn-danger btn-sm'>Ajouter</a>
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les paramètres du formulaire
    $id = !empty($_POST['id']) ? $_POST['id'] : NULL;
    $nom = !empty($_POST['nom']) ? $_POST['nom'] : NULL;
    $genre = !empty($_POST['genre']) ? $_POST['genre'] : NULL;
    $nationalite = !empty($_POST['nationalite']) ? $_POST['nationalite'] : NULL;
    $adress = !empty($_POST['adress']) ? $_POST['adress'] : NULL;
    $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : NULL;
    $enrollement = !empty($_POST['enrollement']) ? $_POST['enrollement'] : NULL;
    $group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : NULL;
    $filiere_id = !empty($_POST['filiere_id']) ? $_POST['filiere_id'] : NULL;
    $nivo_scol = !empty($_POST['nivo_scolaire']) ? $_POST['nivo_scolaire'] : NULL;

    try {
        // Préparer et exécuter l'appel à la procédure stockée
        $stmt1 = $conn->prepare("CALL GetUsers(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bindParam(1, $id, PDO::PARAM_INT);
        $stmt1->bindParam(2, $nom, PDO::PARAM_STR);
        $stmt1->bindParam(3, $genre, PDO::PARAM_STR);
        $stmt1->bindParam(4, $nationalite, PDO::PARAM_STR);
        $stmt1->bindParam(5, $adress, PDO::PARAM_STR);
        $stmt1->bindParam(6, $date_naissance, PDO::PARAM_STR);
        $stmt1->bindParam(7, $enrollement, PDO::PARAM_STR);
        $stmt1->bindParam(8, $group_id, PDO::PARAM_STR);
        $stmt1->bindParam(9, $filiere_id, PDO::PARAM_STR);
        $stmt1->bindParam(10, $nivo_scol, PDO::PARAM_STR);
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
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.8rem 1rem;
        }

        .nav-link {
            font-size: 1.1rem;
            margin: 0 15px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #0056b3 !important;
        }

        h2 {
            color: #2c3e50;
            font-weight: 600;
            margin: 2rem 0;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 2px solid #e0e0e0;
            transition: border-color 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 0.5rem 2rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .table {
            margin-top: 2rem;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .table thead {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .table th {
            font-weight: 600;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            margin: 0 2px;
            font-size: 0.85rem;
        }

        .btn-warning {
            background-color: #f1c40f;
            border-color: #f1c40f;
        }

        .btn-dark {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }

        .alert {
            border-radius: 0.5rem;
        }

        .table-responsive {
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
       <?php include_once 'nav_espaceStgagire.php'?>
       <div class="row mt-5">
    <h2 class="text-center fw-bold">Derniers 5 Stagiaires non inscrits</h2>
    <div class="col-12 table-responsive" style="max-height: 400px; overflow-y: auto;">
        <?php
        try {
            $sql = "
                SELECT u.*
                FROM users u
                LEFT JOIN stagiaire s ON u.id = s.id_user
                WHERE u.role = 'stagiaire' AND s.id_user IS NULL
                ORDER BY u.id DESC
                LIMIT 5
            ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($users)) {
                echo "<table class='table table-striped table-bordered table-hover'>";
                echo "<thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>CIN</th>
                            <th>Nom & Prénom</th>
                            <th>Genre</th>
                            <th>Nationalité</th>
                            <th>Email</th>
                            <th>Adresse</th>
                            <th>Téléphone</th>
                            <th>Date de naissance</th>
                            <th>Filière</th>
                            <th>Groupe</th>
                            <th>Date d'inscription</th>
                            <th>Niveau Scolaire</th>
                            <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>";
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['cin']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['genre']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['nationalite']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['adress']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['date_naissance']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['filiere_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['group_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['nivo_scolaire']) . "</td>";
                    echo "<td>
                            <a href='ajouter_stagiaire.php?id=" . urlencode(htmlspecialchars($user['id'])) . "' class='btn btn-warning btn-sm'>Ajouter</a>
                            <a href='modifier_users.php?id=" . urlencode(htmlspecialchars($user['id'])) . "' class='btn btn-warning btn-sm'>Modifier</a>
                            <button class='btn btn-danger btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur ?\");'>
                                <a href='suppUsers.php?id=" . urlencode(htmlspecialchars($user['id'])) . "' class='text-dark'>Supprimer</a>
                            </button>
                          </td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-warning'>Aucun stagiaire trouvé.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
    </div>
</div>
        <!-- Contenu principal -->
        <main class="container mt-4">
            
            <section>
                <h2>Gestion des Stagiaires</h2>
                
                <!-- Section affichage -->
                <div class="mb-5">
                    <form method="POST">
                        <button type="submit" name="afficher" class="btn btn-primary">
                            Afficher les Utilisateurs
                        </button>
                    </form>
                    
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['afficher'])) : ?>
                        <div class="mt-4">
                            <?php afficherUtilisateurs(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulaire de recherche -->
                <div class="card shadow-sm mb-5">
                    <div class="card-body">
                        <h3 class="card-title mb-4">Recherche avancée</h3>
                        
                        <form method="POST" class="row g-4">
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
    <div class="col-md-3">
        <label for="adress" class="form-label">Groupe</label>
        <input type="text" class="form-control" id="adress" name="group_id" placeholder="Groupe">
    </div>
    <div class="col-md-3">
        <label for="adress" class="form-label">Filiere</label>
        <input type="text" class="form-control" id="adress" name="filiere_id" placeholder="Filiere">
    </div>
    <div class="col-md-2">
        <label for="genre" class="form-label">Nivo scolaire</label>
        <select id="" name="nivo_scolaire" class="form-select">
            <option value=""  checked>--choisi--</option>
            <option value="1ere_anne">1ere anne</option>
            <option value="2eme_anne">2eme anne</option>
            <option value="3eme_anne">3eme anne</option>
        </select>
    </div>
<div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="bi bi-search me-2"></i>Rechercher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Résultats -->
                <?php if (!empty($users)) : ?>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
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
                    <th>Groupe</th>
                    <th>Filiere</th>
                    <th>Nivo Scolaire</th>
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
                        <td><?= htmlspecialchars($user['enrollement']) ?></td>
                        <td><?=  htmlspecialchars($user['group_id']) ?></td>
                <td><?=htmlspecialchars($user['filiere_id'])?></td>
                <td><?= htmlspecialchars($user['nivo_scolaire'])?></td>
                        <td>
                             <a href="ajouter_stagiaire.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-warning btn-sm">Ajouter</a>
                            <a href="modifier_users.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="suppUsers.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-dark btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
                        </div>
                    </div>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
                    <div class="alert alert-info mt-4">
                        Aucun résultat trouvé pour votre recherche
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>