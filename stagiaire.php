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
        SELECT s.nom AS stagiaire_nom, f.description AS filiere_description, c.nom AS class_nom
        FROM stagiaire s
        LEFT JOIN filiere f ON s.filiere_id = f.filiere_id
        LEFT JOIN class c ON s.group_id = c.group_id
        WHERE s.id_user = :stagiaire_id
    ");
    $stmt->bindParam(':stagiaire_id', $stagiaire_id);
    $stmt->execute();
    $stagiaire_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch notes information
    $stmt = $conn->prepare("
        SELECT n.*,c.course_name AS module_name,c.coefficient As coefff, u.password AS Pass,s.nom AS nom_stagiaire
        FROM notes n
        JOIN course c ON n.course_name = c.course_name
        join stagiaire s on s.stg_id=n.id_stagiaire
        join users u on u.id=s.id_user
        WHERE u.id=:stagiaire_id ");
    $stmt->bindParam(':stagiaire_id', $stagiaire_id);
    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
  
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Close the database connection
$conn = null;


// Pas besoin de fermer la connexion car PDO le gère automatiquement en fin de script
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin de Notes - Stagiaire</title>
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header-section {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            padding: 1.5rem;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .student-info {
            color: white;
            font-size: 1.1rem;
            margin: 0.5rem 0;
        }
        
        .grade-table {
            margin-top: 2rem;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .table thead {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
        }
        
        .total-row {
            background-color: #e9ecef;
            font-weight: 600;
        }
        
        .password-alert {
            border-radius: 10px;
            margin: 2rem auto;
            max-width: 500px;
        }
        
        .request-btn {
            transition: transform 0.2s;
        }
        
        .request-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <!-- En-tête -->
        <div class="row header-section align-items-center">
            <div class="col-12 col-md-3 text-center mb-3 mb-md-0">
                <img src="ofppt.png" alt="Logo OFPPT" class="img-fluid" style="max-width: 100px;">
            </div>
            <div class="col-12 col-md-9">
                <div class="row">
                    <div class="col-12 col-md-4 text-center text-md-start">
                        <p class="student-info mb-2">
                            <i class="bi bi-person-fill me-2"></i>
                            <?= htmlspecialchars($stagiaire_info['stagiaire_nom']) ?>
                        </p>
                    </div>
                    <div class="col-12 col-md-4 text-center">
                        <p class="student-info mb-2">
                            <i class="bi bi-book me-2"></i>
                            <?= htmlspecialchars($stagiaire_info['filiere_description']) ?>
                        </p>
                    </div>
                    <div class="col-12 col-md-4 text-center text-md-end">
                        <p class="student-info mb-2">
                            <i class="bi bi-people-fill me-2"></i>
                            <?= htmlspecialchars($stagiaire_info['class_nom']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des notes -->
        <div class="row justify-content-center mt-4">
            <div class="col-12 col-lg-10">
                <?php if (count($notes) > 0): ?>
                    <div class="grade-table">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="text-center">
                                    <tr>
                                        <th scope="col">Module</th>
                                        <th scope="col">Note 1</th>
                                        <th scope="col">Note 2</th>
                                        <th scope="col">Note 3</th>
                                        <th scope="col">Note 4</th>
                                        <th scope="col">EFM</th>
                                        <th scope="col">Coefficient</th>
                                        <th scope="col">Moyenne</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php foreach ($notes as $note): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($note['module_name']) ?></td>
                                        <td><?= htmlspecialchars($note['controle1']) ?></td>
                                        <td><?= htmlspecialchars($note['controle2']) ?></td>
                                        <td><?= htmlspecialchars($note['controle3']) ?></td>
                                        <td><?= htmlspecialchars($note['controle4']) ?></td>
                                        <td><?= htmlspecialchars($note['efm']) ?></td>
                                        <td><?= htmlspecialchars($note['coefff']) ?></td>
                                        <td class="fw-semibold">
                                            <?= number_format($note['moyenne'], 2) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="total-row">
                                        <td colspan="6" class="text-end fw-bold">Moyenne Générale</td>
                                        <td colspan="2" class="text-center fw-bold">
                                           <!-- //total -->
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Section mot de passe -->
                    <div class="alert alert-warning password-alert text-center">
                        <h5 class="alert-heading">Informations d'identification</h5>
                        <p class="mb-0">
                            Votre mot de passe temporaire : 
                            <span class="fw-bold text-danger"><?= htmlspecialchars($note['Pass']) ?></span>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center mt-5">
                        Aucune note disponible pour le moment
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bouton de demande -->
        <div class="row justify-content-center mb-4">
            <div class="col-auto">
                <a href="demmande_attis.php" class="btn btn-primary request-btn">
                    <i class="bi bi-envelope-paper me-2"></i>Faire une demande
                </a>
            </div>
        </div>
    </div>

    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>