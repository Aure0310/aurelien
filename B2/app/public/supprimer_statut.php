<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if (isset($_GET['id'])) {
        $statutId = $_GET['id'];

        if ($page->deleteStatut($statutId)) {
            header('Location: administration.php');
            exit();
        } else {
            echo "Erreur lors de la suppression du statut.";
        }
    } else {
        echo "L'ID du statut à supprimer n'est pas spécifié.";
    }
} else {
    header('Location: accueil.php');
    exit();
}
