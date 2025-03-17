<?php
include_once "session_start.php";
// Vérifiez si l'utilisateur est connecté et a le rôle admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require_once "conn.php";
// Récupérer le nombre des demandes faites dans les dernières 48 heures
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM demmande_attiss 
    WHERE date > DATE_SUB(NOW(), INTERVAL 48 HOUR)
");
$stmt->execute();
$count = $stmt->fetchColumn();  // Récupérer le nombre de demandes

// Afficher le nombre dans le badge
echo "<span class='badge bg-danger'>$count</span>";



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: #f8f9fa; /* Light gray background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add shadow to navbar */
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            width: 70px;
            margin: 10px auto;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .card-text {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .btn-outline-danger, .btn-outline-dark, .btn-outline-primary {
            border-width: 2px;
            font-weight: bold;
        }

        .btn-outline-danger:hover, .btn-outline-dark:hover, .btn-outline-primary:hover {
            color: white !important;
        }

        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background: linear-gradient(135deg, #6a11cb, #2575fc); /* Gradient background */
            color: white;
        }

        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: #dc3545;
            border: none;
        }

        .btn-warning {
            background: #ffc107;
            border: none;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        h1, h2 {
            color: #333;
            font-weight: bold;
        }

        h1 {
            border-bottom: 3px solid gray;
            padding-bottom: 10px;
        }

        h2 {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include_once 'navadmin.php'; ?>
    <dib class="row">
        <div class="col-12 mt-5"></div>
    </dib>
    <div class="container-fluid p-4">
        <!-- Dashboard Title -->
        <h1 class="fw-bold">Tableau de Bord</h1>

        <!-- Cards Section -->
        <div class="row mt-4">
            <!-- Stagiaires Card -->
            <div class="col-12 col-lg-3 mb-4">
                <div class="card bg-success text-white">
                    <img src="./img/téléchargement.jpeg" class="card-img-top" alt="Stagiaires">
                    <div class="card-body text-center">
                        <h5 class="card-title">Nombre des (users) stagiaires inscrits</h5>
                        <p class="card-text">
                            <?php
                            require_once "conn.php";
                            try {
                                $sql = "SELECT COUNT(*) AS total_stagiaires FROM users WHERE role = 'stagiaire'";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo htmlspecialchars($result['total_stagiaires']);
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                        <a href="#" class="btn btn-outline-light">En savoir plus</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users me-2"></i>Stagiaires par Groupe</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT c.nom AS Group_name, COUNT(s.stg_id) AS total_stagiaires
                                        FROM formateur fo
                                        JOIN filiere f ON fo.filiere_id = f.filiere_id
                                        JOIN class c ON c.group_id = fo.group_id
                                        JOIN stagiaire s ON s.filiere_id = f.filiere_id AND s.group_id = c.group_id
                                        
                                        GROUP BY c.group_id";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($results) {
                                    echo "<ul class='list-unstyled'>";
                                    foreach ($results as $row) {
                                        echo "<li>{$row['Group_name']}: <strong>{$row['total_stagiaires']}</strong></li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "Aucun stagiaire trouvé.";
                                }
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Formateurs Card -->
            <div class="col-12 col-lg-3 mb-4">
                <div class="card bg-primary text-white">
                    <img src="./img/img2.png" class="card-img-top" alt="Formateurs">
                    <div class="card-body text-center">
                        <h5 class="card-title">Nombre des (users) formateurs inscrits</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT COUNT(*) AS total_formateurs FROM users WHERE role = 'formateur'";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo htmlspecialchars($result['total_formateurs']);
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                        <a href="#" class="btn btn-outline-light">En savoir plus</a>
                    </div>
                </div>
            </div>

            <!-- Admins Card -->
            <div class="col-12 col-lg-3 mb-4">
                <div class="card bg-danger text-white">
                    <img src="./img/img3.png" class="card-img-top" alt="Admins">
                    <div class="card-body text-center">
                        <h5 class="card-title">Nombre des admins inscrits</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo htmlspecialchars($result['total_admins']);
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                        <a href="#" class="btn btn-outline-light">En savoir plus</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
            <div class="col-md-6 col-lg-3 mt-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users me-2"></i>Stagiaires par Filière</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT f.description, COUNT(s.stg_id) AS total_stagiaires,f.nombre_max As nbmax
                                        FROM formateur fo
                                        JOIN filiere f ON fo.filiere_id = f.filiere_id
                                        JOIN stagiaire s ON s.filiere_id = f.filiere_id
                                        GROUP BY f.filiere_id";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($results) {
                                    echo "<ul class='list-unstyled'>";
                                    foreach ($results as $row) {
                                        echo "<li>{$row['description']}: <strong>{$row['total_stagiaires']}  </strong>->Nombre max :<strong>{$row['nbmax']}</strong></li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "Aucun stagiaire trouvé.";
                                }
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users me-2"></i>Formateur Courses</h5>
                        <p class="card-text">
                            <?php
                            try {
                                $sql = "SELECT nom AS nom_fo, course_name As course_name,group_id As group_id
                                        FROM formateur fo
                                       
                                        ";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($results) {
                                    echo "<ul class='list-unstyled'>";
                                    foreach ($results as $row) {
                                        echo "<li>{$row['nom_fo']}:<strong>{$row['group_id']}</strong>-> <strong>{$row['course_name']}</strong></li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "Aucun forrmateur course.";
                                }
                            } catch (PDOException $e) {
                                echo "Erreur : " . htmlspecialchars($e->getMessage());
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Latest Stagiaires Section -->
        <div class="row mt-5">
            <h2 class="text-center fw-bold">Derniers 10 stagiaires inscrits</h2>
            <div class="col-12 table-responsive">
                <?php
                try {
                    $sql = "
                        SELECT s.stg_id, s.nom AS stagiaire_nom, s.group_id, u.email, s.filiere_id, s.nivo_scolaire
                        FROM stagiaire s
                        JOIN users u ON s.id_user = u.id
                        ORDER BY s.stg_id DESC
                        LIMIT 10
                    ";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($users)) {
                        echo "<table class='table table-striped table-bordered table-hover'>";
                        echo "<thead class='table-dark'>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom & Prénom</th>
                                    <th>Groupe</th>
                                    <th>Email</th>
                                    <th>Filière</th>
                                    <th>Niveau Scolaire</th>
                                    <th>Actions</th>
                                </tr>
                              </thead>
                              <tbody>";
                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($user['stg_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['stagiaire_nom']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['group_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['filiere_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['nivo_scolaire']) . "</td>";
                            echo "<td>
                                    <a href='suppStagiaire.php?id=" . urlencode(htmlspecialchars($user['stg_id'])) . "' class='btn btn-danger btn-sm'>Supprimer</a>
                                    <a href='modifierStagiaire.php?id=" . urlencode(htmlspecialchars($user['stg_id'])) . "' class='btn btn-warning btn-sm'>Modifier</a>
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
    </div>
    
    <!-- Bootstrap JS -->
    <script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>