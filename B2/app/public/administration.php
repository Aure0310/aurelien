<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = false;

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

if ($page->session->hasRole('Admin')) {
    $users = $page->getAllUsers();
    $statuts = $page->getAllStatuts();
    $types = $page->getAllTypes();
    $urgences = $page->getAllUrgences();

    echo $page->render('administration.html.twig', [
        'msg' => $msg,
        'prenom' => $prenom,
        'nom' => $nom,
        'role' => $role,
        'users' => $users,
        'statuts' => $statuts,
        'types' => $types,
        'urgences' => $urgences,
    ]);

} else {
    header('Location: accueil.php');
    exit();
}
