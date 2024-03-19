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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $client_id = isset($_POST['client']) ? $_POST['client'] : null;
    $intervenant_id = isset($_POST['intervenant']) ? $_POST['intervenant'] : null;
    $intervenant_id2 = isset($_POST['intervenant2']) ? $_POST['intervenant2'] : null;

    $data = [
        'ID_Client' => $client_id,
        'ID_Intervenant' => $intervenant_id,
        'ID_Intervenant2' => $intervenant_id2,
        'Date' => $_POST['date'],
        'Commentaire' => $_POST['comment'],
        'ID_Type' => $_POST['intervention_type'],
        'ID_Statut' => $_POST['intervention_statut'],
        'ID_Urgence' => $_POST['intervention_urgence'],
    ];

    $page->insertIntervention($data);

    $msg = 'Intervention créée avec succès!';
}

echo $page->render('navbar.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);

echo $page->render('addintervention.html.twig', [
    'msg' => $msg,
    'clients' => $page->getAllClients(),
    'intervenants' => $page->getAllIntervenants(),
    'types' => $page->getAllTypes(),
    'statuts' => $page->getAllStatuts(),
    'urgences' => $page->getAllUrgences(),
]);
