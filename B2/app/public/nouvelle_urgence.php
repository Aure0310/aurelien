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
    $niveau = $_POST['niveau'];

    if (empty($niveau)) {
        $msg = 'Veuillez saisir un niveau d\'urgence.';
    } else {
        $success = $page->addUrgence(['Niveau' => $niveau]);

        if ($success) {
            $msg = 'Urgence ajoutée avec succès !';
        } else {
            $msg = 'Erreur lors de l\'ajout de l\'urgence.';
        }
    }
}

echo $page->render('nouvelle_urgence.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);
