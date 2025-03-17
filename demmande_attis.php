<?php
require_once "conn.php";
include_once "session_start.php";

// Validation de l'accès
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'stagiaire') {
    header("Location: login.php");
    exit();
}

// Nettoyage et validation de l'ID du stagiaire
$stagiaire_id = $_SESSION['user_id'];

if (!$stagiaire_id) {
    header("Location: login.php");
    exit();
}

try {
    // Fetch stagiaire information
    $stmt = $conn->prepare("
        SELECT s.stg_id AS stg_id, s.nom AS stagiaire_nom, f.filiere_id AS filiere_description, c.group_id AS class_nom
        FROM stagiaire s
        JOIN filiere f ON s.filiere_id = f.filiere_id
        JOIN class c ON s.group_id = c.group_id
        WHERE s.id_user = :stagiaire_id
    ");
    $stmt->bindParam(':stagiaire_id', $stagiaire_id);
    $stmt->execute();
    $stagiaire_info = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = htmlspecialchars($_POST['id']);
    $nom = htmlspecialchars($_POST['nom']);
    $group_id = htmlspecialchars($_POST['group']);
    $filiere_id = htmlspecialchars($_POST['filiere']);
    $type_att = htmlspecialchars($_POST['type_att']);

    try {
        $stmt = $conn->prepare("
            INSERT INTO demmande_attiss (stg_id, nom_stagiaire, group_id, filiere_id, type_att, date) 
            VALUES (:stg_id, :nom, :group_id, :filiere_id, :type_att, NOW())
        ");
        $stmt->bindParam(':stg_id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':group_id', $group_id);
        $stmt->bindParam(':filiere_id', $filiere_id);
        $stmt->bindParam(':type_att', $type_att);

        $stmt->execute();
        echo "<p class='alert alert-success'>Demande ajoutée avec succès !</p>";
    } catch (PDOException $e) {
        // Capturer l'erreur liée au trigger
        if ($e->getCode() == '45000') {
            echo "Erreur : " . $e->getMessage(); // Afficher le message d'erreur du trigger
        } else {
            echo "Erreur de base de données : " . $e->getMessage();
        }}
}// Préparer la requête<?php
// Préparer la requête SQL
$stmt3 = $conn->prepare("
SELECT status FROM demmande_attiss d join stagiaire s on d.stg_id=s.stg_id join users u on s.id_user=u.id WHERE  u.id = :id
");

// Lier le paramètre de manière sécurisée
$stmt3->bindParam(':id', $stagiaire_id, PDO::PARAM_INT);

// Exécuter la requête
$stmt3->execute();

// Récupérer une seule ligne
$statu = $stmt3->fetch(PDO::FETCH_ASSOC);

// Vérifier si un statut a été trouvé
$status_value = $statu ? $statu['status'] : "Non défini"; 

// Fermer la requête
$stmt3->closeCursor();
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'Attestation</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
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

        .status-indicator {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            display: inline-block;
        }

        .status-indicator.active {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-indicator.inactive {
            background-color: #f8d7da;
            color: #842029;
        }

        .btn-primary {
            background-color: #3498db;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- En-tête du formulaire -->
            <h2 class="form-title">Demande d'Attestation</h2>

            <!-- Indicateur de statut -->
            <div class="mb-4">
                <span class="status-indicator <?= ($status_value === 'Actif') ? 'active' : 'inactive' ?>">
                    Statut : <?= htmlspecialchars($status_value) ?>
                </span>
            </div>

            <!-- Formulaire de demande -->
            <form action="" method="POST">
                <div class="row g-4">
                    <!-- ID Stagiaire -->
                    <div class="col-md-6">
                        <label for="id" class="form-label">ID Stagiaire</label>
                        <input type="text" class="form-control" name="id" 
                               value="<?= htmlspecialchars($stagiaire_info['stg_id']) ?>" readonly>
                    </div>

                    <!-- Nom Complet -->
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom Complet</label>
                        <input type="text" class="form-control" name="nom" 
                               value="<?= htmlspecialchars($stagiaire_info['stagiaire_nom']) ?>" readonly>
                    </div>

                    <!-- Groupe -->
                    <div class="col-md-6">
                        <label for="group" class="form-label">Groupe</label>
                        <input type="text" class="form-control" name="group" 
                               value="<?= htmlspecialchars($stagiaire_info['class_nom']) ?>" readonly>
                    </div>

                    <!-- Filière -->
                    <div class="col-md-6">
                        <label for="filiere" class="form-label">Filière</label>
                        <input type="text" class="form-control" name="filiere" 
                               value="<?= htmlspecialchars($stagiaire_info['filiere_description']) ?>" readonly>
                    </div>

                    <!-- Type d'attestation -->
                    <div class="col-12">
                        <label for="type_att" class="form-label">Type d'Attestation</label>
                        <select name="type_att" class="form-select" required>
                            <option value="" selected disabled>-- Choisir un type --</option>
                            <option value="Attestation_de_Présence">Attestation de Présence</option>
                            <option value="Attestation_de_Réussite">Attestation de Réussite</option>
                            <option value="Attestation_de_Stage">Attestation de Stage</option>
                            <option value="Attestation_de_Fin_de_Formation">Attestation de Fin de Formation</option>
                            <option value="Chahada_Mdrasiya">Chahada Mdrasiya</option>
                        </select>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="col-12 text-end">
                        <button type="submit" name="valider" class="btn btn-primary">
                            <i class="bi bi-send-fill me-2"></i>Envoyer la demande
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>