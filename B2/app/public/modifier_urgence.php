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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['urgence_id'])) {
        $urgenceId = $_POST['urgence_id'];

        if ($page->editUrgence($urgenceId, $_POST)) {
            $msg = 'Urgence modifiée avec succès !';
            header('Location: administration.php');
            exit(); 
        } else {
            $msg = 'Erreur lors de la modification de l\'urgence.';
        }
    }

    $urgenceId = $_GET['id'] ?? null;
    $urgence = $page->getUrgenceById($urgenceId);

    echo $page->render('modifier_urgence.html.twig', [
        'msg' => $msg,
        'prenom' => $user['prenom'],
        'nom' => $user['nom'],
        'role' => $user['role'],
        'urgence' => $urgence,
    ]);
} else {
    header('Location: accueil.php');
    exit();
}
