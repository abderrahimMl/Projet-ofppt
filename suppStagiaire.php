<?php
// Connexion à la base de données
require_once "conn.php";

// Vérifiez si un ID est fourni
if (isset($_GET['id']) ){
    $id = intval($_GET['id']);

    try {
        // Préparez la requête SQL pour supprimer l'utilisateur
        $stmt = $conn->prepare("DELETE FROM stagiaire WHERE stg_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirection vers la page principale avec un message de succès
       header("Location: admin.php?message=success");
        exit();
    } catch (PDOException $e) {
        // Redirection avec un message d'erreur
      echo  $e->getMessage();
        exit();
    }
} else {
    
    exit();
}
?>
