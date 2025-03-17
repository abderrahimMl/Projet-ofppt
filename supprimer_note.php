<?php
// Inclure le fichier de configuration ou de connexion à la base de données
require_once 'conn.php';

// Vérifier si l'ID de la note est passé via l'URL
if (isset($_GET['id'])) {
    $id_note = intval($_GET['id']); // Convertir en entier pour éviter les injections SQL

    // Préparer la requête de suppression
    $sql = "DELETE FROM notes WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Exécuter la requête avec l'ID
    if ($stmt->execute([$id_note])) {
       // echo "La note a été supprimée avec succès.";
        header("Location: afficher_note.php?message=success");
    } else {
        echo "Erreur lors de la suppression de la note.";
    }
} else {
    echo "ID de la note non spécifié.";
}
?>
