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
        $adresse = $user['adresse'];
        $telephone = $user['telephone'];
        $createdAt = $user['CREATED_AT'];
    } else {
        $msg = "Utilisateur introuvable dans la base de données.";
    }
} else {
    $msg = "Utilisateur non connecté.";
}

echo $page->render('navbar.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);

echo $page->render('profil.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
    'adresse' => $adresse,
    'telephone' => $telephone,
    'createdAt' => $createdAt,
]);
