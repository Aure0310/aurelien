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

    // Vérifier si un rôle est sélectionné dans le filtre
    if(isset($_GET['filter_role']) && in_array($_GET['filter_role'], $roles)) {
        $filteredRole = $_GET['filter_role'];
        $users = $page->getUsersByRole($filteredRole); // Modifier cette ligne pour récupérer les utilisateurs par rôle depuis la base de données
    } else {
        // Si aucun rôle n'est sélectionné, afficher tous les utilisateurs
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
        'filter_role' => isset($_GET['filter_role']) ? $_GET['filter_role'] : '', // Passer le rôle filtré à Twig
    ]);

} else {
    header('Location: accueil.php');
    exit();
}
