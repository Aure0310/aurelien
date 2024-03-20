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

        $isStandardiste = $page->session->hasRole('Standardiste');
        $isAdmin = $page->session->hasRole('Admin');
        $isClient = $page->session->hasRole('Client');
        $isIntervenant = $page->session->hasRole('Intervenant');
    } else {
        $msg = "Utilisateur introuvable dans la base de données.";
    }
} else {
    $msg = "Utilisateur non connecté.";
}

$search_query = $_GET['search_query'] ?? '';

$searchResults = [];

if (is_numeric($search_query)) {
    $user = $page->searchUserById($search_query);
    if ($user) {
        $searchResults['Utilisateurs'][] = $user;
    }
}

$intervention = $page->searchInterventionById($search_query);
if ($intervention) {
    $searchResults['Interventions'][] = $intervention;
}

echo $page->render('navbar.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);

echo $page->render('recherche.html.twig', [
    'search_results' => $searchResults,
    'search_query' => $search_query,
]);
