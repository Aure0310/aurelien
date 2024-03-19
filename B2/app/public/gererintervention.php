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

$intervention_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($intervention_id) {
    $intervention = $page->getInterventionDetails($intervention_id);

    if (!$intervention) {
        exit("Intervention introuvable");
    }
 
    $client_id = $intervention['ID_Client'];
 
    $client = $page->getUserById(['id' => $client_id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $client_id = isset($_POST['client']) ? $_POST['client'] : null;
    $intervenant_id = isset($_POST['intervenant']) ? $_POST['intervenant'] : null;
    $intervenant_id2 = isset($_POST['intervenant2']) ? $_POST['intervenant2'] : null;

    if (empty($_POST['intervenant2'])) {
        $_POST['intervenant2'] = null;
    }

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

    $intervention_id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($intervention_id) {
        $client_id = $_POST['client'];
        $intervenant_id = $_POST['intervenant'];
        $intervenant_id2 = $_POST['intervenant2'];
        $date = $_POST['date'];
        $commentaire = $_POST['comment'];
        $type_id = $_POST['intervention_type'];
        $statut_id = $_POST['intervention_statut'];
        $urgence_id = $_POST['intervention_urgence'];

        $data = [
            'ID_Client' => $client_id,
            'ID_Intervenant' => $intervenant_id,
            'ID_Intervenant2' => $intervenant_id2,
            'Date' => $date,
            'Commentaire' => $commentaire,
            'ID_Type' => $type_id,
            'ID_Statut' => $statut_id,
            'ID_Urgence' => $urgence_id,
        ];

        if ($page->updateIntervention($intervention_id, $data)) {
            $msg = 'Intervention modifiée avec succès!';
            header('Location: interventions.php');
            exit;
        } else {
            $msg = 'Erreur lors de la modification de l\'intervention.';
        }
    }

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
    'intervention' => $intervention,
    'client' => $client,  
    'clients' => $page->getAllClients(),
    'intervenants' => $page->getAllIntervenants(),
    'types' => $page->getAllTypes(),
    'statuts' => $page->getAllStatuts(),
    'urgences' => $page->getAllUrgences(),
]);
