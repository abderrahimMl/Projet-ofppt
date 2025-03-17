<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,    // Utilisez uniquement via HTTPS
        'cookie_httponly' => true, // Empêche l'accès au cookie via JavaScript
        'use_strict_mode' => true, // Bloque les ID de session invalide
    ]);
}
?>