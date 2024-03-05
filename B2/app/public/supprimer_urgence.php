<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['urgence_id'])) {
        $urgenceId = $_POST['urgence_id'];

        if ($page->deleteUrgence($urgenceId)) {
            $msg = 'Urgence supprimée avec succès !';
        } else {
            $msg = 'Erreur lors de la suppression de l\'urgence.';
        }
    }

    header('Location: administration.php?msg=' . $msg);
    exit();
} else {
    header('Location: accueil.php');
    exit();
}
