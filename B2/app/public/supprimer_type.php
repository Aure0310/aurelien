<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type_id'])) {
        $typeId = $_POST['type_id'];

        if ($page->deleteType($typeId)) {
            $msg = 'Type supprimé avec succès !';
        } else {
            $msg = 'Erreur lors de la suppression du type.';
        }
    }

    header('Location: administration.php?msg=' . $msg);
    exit();
} else {
    header('Location: accueil.php');
    exit();
}
