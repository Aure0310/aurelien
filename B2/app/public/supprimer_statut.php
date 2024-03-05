<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut_id'])) {
        $statutId = $_POST['statut_id'];

        if ($page->deleteStatut($statutId)) {
            $msg = 'Statut supprimé avec succès !';
        } else {
            $msg = 'Erreur lors de la suppression du statut.';
        }
    }

    header('Location: administration.php?msg=' . $msg);
    exit();
} else {
    header('Location: accueil.php');
    exit();
}
