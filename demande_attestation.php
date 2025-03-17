<?php
require_once "conn.php"; // Connexion à la base de données

// Vérifier si l'utilisateur est connecté et a le rôle 'admin'
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location:login.php");
    exit();
}

// Récupérer toutes les demandes
$stmt = $conn->prepare("SELECT * FROM demmande_attiss GROUP BY id_demmande DESC");
$stmt->execute();
$demande_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mettre à jour le statut de la demande
if (isset($_POST['update_status'])) {
    $id_demmande = $_POST['id_demmande'];
    $new_status = $_POST['status'];

    // Mettre à jour le statut dans la base de données
    $update_stmt = $conn->prepare("UPDATE demmande_attiss SET status = :status WHERE id_demmande = :id_demmande");
    $update_stmt->bindParam(':status', $new_status);
    $update_stmt->bindParam(':id_demmande', $id_demmande);
    $update_stmt->execute();

    echo "<p class='alert alert-success'>Statut mis à jour avec succès.</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Pour les icônes -->
</head>
<body>
<?php include_once 'navadmin.php'?>

<div class="container mt-4">
    <h2>Liste des Demandes d'Attestation</h2>
    
    <!-- Table des demandes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom Stagiaire</th>
                <th>Filière</th>
                <th>Type d'Attestation</th>
                <th>Date de Demande</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demande_list as $demande): ?>
            <tr>
                <td><?php echo $demande['id_demmande']; ?></td>
                <td><?php echo $demande['nom_stagiaire']; ?></td>
                <td><?php echo $demande['filiere_id']; ?></td>
                <td><?php echo $demande['type_att']; ?></td>
                <td><?php echo $demande['date']; ?></td>
                <td>
                    <!-- Affichage du statut -->
                    <span class="badge <?php echo ($demande['status'] === 'approuve') ? 'bg-success' : ($demande['status'] === 'refuse' ? 'bg-danger' : 'bg-warning'); ?>">
                        <?php echo ucfirst($demande['status']); ?>
                    </span>
                </td>
                <td>
                    <!-- Formulaire pour changer le statut -->
                    <form action="" method="POST" class="d-inline">
                        <input type="hidden" name="id_demmande" value="<?php echo $demande['id_demmande']; ?>">
                        <select name="status" class="form-select">
                            <option value="en_attente" <?php echo ($demande['status'] === 'en_attente') ? 'selected' : ''; ?>>En Attente</option>
                            <option value="approuve" <?php echo ($demande['status'] === 'approuve') ? 'selected' : ''; ?>>Approuvé</option>
                            <option value="refuse" <?php echo ($demande['status'] === 'refuse') ? 'selected' : ''; ?>>Refusé</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-warning mt-2">Mettre à jour</button>
                    </form>
                    <br>

                    <!-- Bouton d'impression basé sur le type d'attestation -->
                    <?php
                    $type_att = strtolower($demande['type_att']);
                    $impression_url = "attistation/imprimer_" . $type_att . ".php?id=" . $demande['id_demmande'];
                    ?>
                    <a href="<?php echo $impression_url; ?>" class="btn btn-sm btn-success mt-2">Imprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
