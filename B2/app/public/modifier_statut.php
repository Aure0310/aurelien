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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut_id'])) {
            $statutId = $_POST['statut_id'];

            if ($page->editStatut($statutId, $_POST)) {
                $msg = 'Statut modifié avec succès !';
                header('Location: administration.php');
                exit(); 
            } else {
                $msg = 'Erreur lors de la modification du statut.';
            }
    }

    $statutId = $_GET['id'] ?? null;
    $statut = $page->getStatutById($statutId);

    echo $page->render('modifier_statut.html.twig', [
        'msg' => $msg,
        'prenom' => $user['prenom'],
        'nom' => $user['nom'],
        'role' => $user['role'],
        'statut' => $statut,
    ]);
} else {
    header('Location: accueil.php');
    exit();
}
