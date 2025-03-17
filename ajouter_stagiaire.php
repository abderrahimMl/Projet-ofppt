<?php
include_once "conn.php";


function ajouterStagiaire() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = htmlspecialchars($_POST['nom']);
        $cin = htmlspecialchars($_POST['cin']);
        $group_id = htmlspecialchars($_POST['group_id']);
        $id_user = htmlspecialchars($_POST['id_user']);
        $filiere_id = htmlspecialchars($_POST['filiere_id']);
        $nivo_scol = htmlspecialchars($_POST['nivo_scolaire']);
        

        try {
            $stmt = $conn->prepare("
                INSERT INTO stagiaire (Cin,nom, group_id, id_user, filiere_id,nivo_scolaire) 
                VALUES (:Cin,:nom,:group_id, :id_user, :filiere_id,:nivo_scolaire)
            ");   
            $stmt->bindParam(':Cin', $cin);
            $stmt->bindParam(':nom', $nom);

            $stmt->bindParam(':group_id', $group_id);
            $stmt->bindParam(':id_user', $id_user);
            $stmt->bindParam(':filiere_id', $filiere_id);
            $stmt->bindParam(':nivo_scolaire', $nivo_scol);

            $stmt->execute();

            echo "<p class='alert alert-success'>Stagiaire ajouté avec succès !</p>";
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
        $stmtUser = $conn->prepare("SELECT id,Cin, nom,nivo_scolaire,filiere_id,group_id FROM users WHERE id = :id");
        $stmtUser->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtUser->execute();
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
        // Vérifier si l'utilisateur existe
        if ($user) {
            $userNom = $user['nom'];
            $userid = $user['id'];
            $userCin = $user['Cin'];
            $userNivo = $user['nivo_scolaire'];
            $userfilere = $user['filiere_id'];
            $usergroup = $user['group_id'];
        } else {
            $userNom = ''; // Si l'utilisateur n'existe pas, laisser vide
            $userid = '';  // Si l'utilisateur n'existe pas, laisser vide
        }
    } else {
        $userNom = ''; // Si aucun ID n'est passé, laisser vide
        $userid = '';  // Si aucun ID n'est passé, laisser vide
    }

    $id = $_GET['id'];
   // hna rani zet had  ster join stagiaire s on s.group_id= c.group_id where s.stg_id=':id'  f groupe o filiere  
    // Récupérer les groupes
    $stmtGroup = $conn->query("SELECT c.group_id , c.nom FROM class c  ");
    $stmtGroup->execute();
    $groupes = $stmtGroup->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les utilisateurs
    $stmtUser = $conn->query("SELECT distinct(nivo_scolaire) FROM users");
    $userss = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les filières
    $stmtFiliere = $conn->query("SELECT f.filiere_id, f.description FROM filiere f");
        $stmtFiliere->execute();
    $filieres = $stmtFiliere->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Stagiaire</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 800px;
        }

        .form-title {
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #dee2e6;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.25);
        }

        .action-buttons {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
        }

        .btn-custom {
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-custom i {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="action-buttons">
            <a href="add_stagiaire.php" class="btn btn-outline-secondary btn-custom">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <a href="add_stagiaire.php" class="btn btn-outline-primary btn-custom">
                <i class="bi bi-eye"></i> Voir stagiaire
            </a>
        </div>

        <div class="form-container">
            <h2 class="form-title">Ajouter un Stagiaire</h2>
            
            <form method="POST">
                <div class="row g-4">
                    <!-- Nom -->
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom complet</label>
                        <input type="text" value="<?= htmlspecialchars($userNom ?? '') ?>" 
                               class="form-control" id="nom" name="nom" 
                               placeholder="Nom du stagiaire" required>
                    </div>

                    <!-- ID Utilisateur -->
                    <div class="col-md-6">
                        <label for="id_user" class="form-label">ID Utilisateur</label>
                        <input type="number" name="id_user" value="<?= htmlspecialchars($userid ?? '') ?>" 
                               class="form-control" placeholder="ID utilisateur" required>
                    </div>

                    <!-- CIN -->
                    <div class="col-md-6">
                        <label for="cin" class="form-label">CIN</label>
                        <input type="text" value="<?= htmlspecialchars($userCin ?? '') ?>" 
                               class="form-control" name="cin" 
                               placeholder="Numéro CIN" required>
                    </div>

                    <!-- Groupe -->
                    <div class="col-md-6">
                        <label for="group_id" class="form-label">Groupe</label>
                        <select class="form-select" id="group_id" name="group_id" required>
                            <option value="" disabled>Choisir un groupe</option>
                            <?php foreach ($groupes as $groupe): ?>
                            <option value="<?= htmlspecialchars($groupe['group_id']) ?>" 
                                <?= ($groupe['group_id'] == ($usergroup ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($groupe['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filière -->
                    <div class="col-md-6">
                        <label for="filiere_id" class="form-label">Filière</label>
                        <select class="form-select" id="filiere_id" name="filiere_id" required>
                            <option value="" disabled>Choisir une filière</option>
                            <?php foreach ($filieres as $filiere): ?>
                            <option value="<?= htmlspecialchars($filiere['filiere_id']) ?>" 
                                <?= ($filiere['filiere_id'] == ($userfilere ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($filiere['description']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Niveau scolaire -->
                    <div class="col-md-6">
                        <label for="nivo_scolaire" class="form-label">Niveau scolaire</label>
                        <select class="form-select" id="nivo_scolaire" name="nivo_scolaire" required>
                            <option value="" disabled>Choisir un niveau</option>
                            <?php foreach ($userss as $uss): ?>
                            <option value="<?= htmlspecialchars($uss['nivo_scolaire']) ?>" 
                                <?= ($uss['nivo_scolaire'] == ($userNivo ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($uss['nivo_scolaire']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="col-12 text-end">
                        <button type="submit" name="ajouter" class="btn btn-primary btn-custom">
                            <i class="bi bi-person-plus"></i> Ajouter le stagiaire
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>