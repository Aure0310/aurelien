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
    $users = [];
    $roles = $page->getAllRoles();  

    if(isset($_GET['filter_role']) && in_array($_GET['filter_role'], $roles)) {
        $filteredRole = $_GET['filter_role'];
        $users = $page->getUsersByRole($filteredRole);
    } else {
        $users = $page->getAllUsers();
    }

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
        'roles' => $roles,
        'filter_role' => isset($_GET['filter_role']) ? $_GET['filter_role'] : '',
    ]);

} else {
    header('Location: accueil.php');
    exit();
}
