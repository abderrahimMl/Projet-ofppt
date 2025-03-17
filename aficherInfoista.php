<?php
require_once 'conn.php'; // Connexion à la base de données

// Insertion de nouveaux enregistrements
if (isset($_POST['insert'])) {
    $name = $_POST['name'];
    $logo = $_FILES['logo']['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $established_year = $_POST['established_year'];
    $description = $_POST['description'];
    $social_media_links = $_POST['social_media_links'];

    // Déplacement du fichier logo dans un dossier
    move_uploaded_file($_FILES['logo']['tmp_name'], 'C:\xampp2\htdocs\web dyamique\projet_ofppt\/' . $logo);

    // Insertion dans la base de données
    $stmt = $conn->prepare("INSERT INTO school_info (name, logo, address, email, website, established_year, description, social_media_links, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $logo, $address, $email, $website, $established_year, $description, $social_media_links]);
}

// Mise à jour des enregistrements
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $logo = $_FILES['logo']['name'] ? $_FILES['logo']['name'] : $_POST['existing_logo'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $established_year = $_POST['established_year'];
    $description = $_POST['description'];
    $social_media_links = $_POST['social_media_links'];

    // Si un nouveau logo a été téléchargé
    if ($_FILES['logo']['name']) {
        move_uploaded_file($_FILES['logo']['tmp_name'], 'uploads/' . $logo);
    }

    // Mise à jour de l'enregistrement
    $stmt = $conn->prepare("UPDATE schol_info SET name = ?, logo = ?, address = ?, email = ?, website = ?, established_year = ?, description = ?, social_media_links = ? WHERE id = ?");
    $stmt->execute([$name, $logo, $address, $email, $website, $established_year, $description, $social_media_links, $id]);
}

// Récupération de l'enregistrement existant si un id est fourni pour mise à jour
$schol_info = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM schol_info WHERE id = ?");
    $stmt->execute([$id]);
    $schol_info = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de l'Organisme</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once 'navadmin.php'?>


<div class="container mt-4">
    <h2>Gestion des Informations de l'Organisme</h2>

    <!-- Formulaire d'ajout ou de mise à jour -->
    <form action="" method="POST" enctype="multipart/form-data">
        <?php if ($schol_info): ?>
            <input type="hidden" name="id" value="<?php echo $schol_info['id']; ?>">
            <input type="hidden" name="existing_logo" value="<?php echo $schol_info['logo']; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="name" class="form-label">Nom de l'organisme</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $schol_info['name'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" class="form-control" id="logo" name="logo">
            <?php if ($schol_info && $schol_info['logo']): ?>
                <img src="uploads/<?php echo $schol_info['logo']; ?>" alt="Logo actuel" width="100">
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $schol_info['address'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $schol_info['email'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="website" class="form-label">Site Web</label>
            <input type="url" class="form-control" id="website" name="website" value="<?php echo $schol_info['website'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="established_year" class="form-label">Année de création</label>
            <input type="number" class="form-control" id="established_year" name="established_year" value="<?php echo $schol_info['established_year'] ?? ''; ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo $schol_info['description'] ?? ''; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="social_media_links" class="form-label">Liens Réseaux Sociaux</label>
            <input type="text" class="form-control" id="social_media_links" name="social_media_links" value="<?php echo $schol_info['social_media_links'] ?? ''; ?>">
        </div>

        <!-- Boutons d'action -->
        <?php if ($schol_info): ?>
            <button type="submit" class="btn btn-warning" name="update">Mettre à jour</button>
        <?php else: ?>
            <button type="submit" class="btn btn-primary" name="insert">Ajouter</button>
        <?php endif; ?>
    </form>

    <h3 class="mt-4">Liste des Organismes</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Email</th>
                <th>Site Web</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Afficher les informations existantes
            $stmt = $conn->prepare("SELECT * FROM school_info");
            $stmt->execute();
            $schol_infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($schol_infos as $row) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['address'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['website'] . "</td>";
                echo "<td>
                        <a href=\"?id=" . $row['id'] . "\" class=\"btn btn-warning btn-sm\">Modifier</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
