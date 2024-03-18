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
        $filter_intervenant = isset($_GET['filter_intervenant']) ? $_GET['filter_intervenant'] : '';
        $interventions = $page->getInterventionsByUser($user_id, $filter_intervenant);

        $count_interventions_en_cours = $page->countInterventionsEnCoursByIntervenant($user_id);

        if ($role == 'Admin' || $role == 'Standardiste') {
            $count_all_interventions_en_cours = $page->countAllInterventionsEnCours();
        }
    }
}

$count_all_interventions_en_cours = $page->countAllInterventionsEnCours();
$intervenants = $page->getAllIntervenants();


echo $page->render('navbar.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);

echo $page->render('interventions.html.twig', [
    'msg' => $msg,
    'interventions' => $interventions,
    'intervenants' => $intervenants,  
    'filter_role' => isset($_GET['filter_role']) ? $_GET['filter_role'] : '',
    'filter_intervenant' => isset($_GET['filter_intervenant']) ? $_GET['filter_intervenant'] : '',
    'count_interventions_en_cours' => $count_interventions_en_cours,
    'count_all_interventions_en_cours' => $count_all_interventions_en_cours,
    'role' => $role,
]);
