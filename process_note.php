<?php
require_once "conn.php"; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si toutes les données nécessaires sont envoyées
    if (isset($_POST['id'], $_POST['type'], $_POST['value'], $_POST['module'], $_POST['name'])) {
        // Récupérer les données du formulaire
        $id_stagiaire = intval($_POST['id']);
        $noteType = htmlspecialchars($_POST['type']);
        $value = floatval($_POST['value']); // Note en tant que nombre flottant
        $course_name = htmlspecialchars($_POST['module']);
        $nom_stagiaire = htmlspecialchars($_POST['name']);

        // Liste des types de notes valides
        $validTypes = ['controle1', 'controle2', 'controle3', 'controle4', 'efm'];
        if (!in_array($noteType, $validTypes)) {
            echo "Type de note invalide.";
            exit;
        }

        try {
            // Vérifier si une ligne existe pour ce stagiaire et ce module
            $query = "SELECT COUNT(*) FROM notes WHERE id_stagiaire = :id_stagiaire AND course_name = :course_name";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':id_stagiaire' => $id_stagiaire,
                ':course_name' => $course_name,
            ]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Mettre à jour la note et le nom du stagiaire si la ligne existe
                $query = "UPDATE notes SET $noteType = :value, nom_stagiaire = :nom_stagiaire WHERE id_stagiaire = :id_stagiaire AND course_name = :course_name";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':value' => $value,
                    ':nom_stagiaire' => $nom_stagiaire,
                    ':id_stagiaire' => $id_stagiaire,
                    ':course_name' => $course_name,
                ]);
            } else {
                // Insérer une nouvelle ligne avec le nom du stagiaire et la note
                $query = "INSERT INTO notes (id_stagiaire, nom_stagiaire, $noteType, course_name) VALUES (:id_stagiaire, :nom_stagiaire, :value, :course_name)";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':id_stagiaire' => $id_stagiaire,
                    ':nom_stagiaire' => $nom_stagiaire,
                    ':value' => $value,
                    ':course_name' => $course_name,
                ]);
            }

            echo "Succès";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Données manquantes.";
    }
} else {
    echo "Méthode non autorisée.";
}
 
?>
