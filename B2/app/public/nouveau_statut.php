<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';

if ($page->session->isConnected()) {
    $user_id = $page->session->get('user')['id'];

    $user = $page->getUserById(['id' => $user_id]);

    if ($user) {
        $prenom = $user['prenom'];
        $nom = $user['nom'];
        $role = $user['role'];

    } else {
        $msg = "Utilisateur introuvable dans la base de données.";
    }
} else {
    $msg = "Utilisateur non connecté.";
}

if (!$page->session->hasRole('Admin')) {
    header('Location: accueil.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];

    if (empty($nom)) {
        $msg = 'Veuillez saisir un nom de statut.';
    } else {
        $success = $page->addStatut($nom);

        if ($success) {
            $msg = 'Statut ajouté avec succès !';
            header('Location: administration.php');
            exit(); 
        } else {
            $msg = 'Erreur lors de l\'ajout du statut.';
        }
    }
}

echo $page->render('nouveau_statut.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);
