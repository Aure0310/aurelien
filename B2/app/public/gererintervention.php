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
    }
}

$interventionId = isset($_GET['id']) ? $_GET['id'] : null;

$interventionDetails = $page->getInterventionDetails($interventionId);
$intervenants = $page->getAllIntervenants();
$statuts = $page->getAllStatuts();
$types = $page->getAllTypes();
$urgences = $page->getAllUrgences();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        'ID_Intervenant' => $_POST['intervention_intervenant'],
        'Date' => $_POST['date'],
        'Commentaire' => $_POST['comment'],
        'ID_Type' => $_POST['intervention_type'],
        'ID_Statut' => $_POST['intervention_statut'],
        'ID_Urgence' => $_POST['intervention_urgence'],
    ];

    $page->updateIntervention($interventionId, $data);

    $msg = 'Intervention modifiée avec succès!';
}

echo $page->render('navbar.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);

echo $page->render('gererintervention.html.twig', [
    'msg' => $msg,
    'intervention' => $interventionDetails,
    'intervenants' => $intervenants,
    'statuts' => $statuts,
    'types' => $types,
    'urgences' => $urgences,
]);
