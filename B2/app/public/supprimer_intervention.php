<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->hasRole('Admin') || $page->session->hasRole('Standardiste')) {
    if (isset($_GET['id'])) {
        $intervention_id = $_GET['id'];
        
        if ($page->deleteIntervention($intervention_id)) {
            header('Location: interventions.php');
            exit();
        }
        header('Location: interventions.php');
        exit();
    } else {
        echo "L'ID de l'intervention n'est pas spécifié.";
    }
} else {
    echo "Vous n'avez pas les autorisations nécessaires pour effectuer cette action.";
}
