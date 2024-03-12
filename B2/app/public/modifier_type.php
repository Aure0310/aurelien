<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';

if ($page->session->isConnected()) {
    $user_id = $page->session->get('user')['id'];

    $user = $page->getUserById(['id' => $user_id]);

    if (!$user) {
        $msg = "Utilisateur introuvable dans la base de données.";
    }
} else {
    $msg = "Utilisateur non connecté.";
}

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type_id'])) {
        $typeId = $_POST['type_id'];

        if ($page->editType($typeId, $_POST)) {
            $msg = 'Type modifié avec succès !';
            header('Location: administration.php');
            exit(); 
        } else {
            $msg = 'Erreur lors de la modification du type.';
        }
    }

    $typeId = $_GET['id'] ?? null;
    $type = $page->getTypeById($typeId);

    echo $page->render('modifier_type.html.twig', [
        'msg' => $msg,
        'prenom' => $user['prenom'],
        'nom' => $user['nom'],
        'role' => $user['role'],
        'type' => $type,
    ]);
} else {
    header('Location: accueil.php');
    exit();
}
