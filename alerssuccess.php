<?php
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    echo  "<script type='text/javascript'>alert('Opération réussie!');</script>";;

    // Insérer un script JavaScript pour rediriger après un délai
    echo "<script>
        setTimeout(function() {
            window.location.href = '" . strtok($_SERVER['REQUEST_URI'], '?') . "';
        }, 1000); // Rediriger après 1 secondes
    </script>";
}
?>