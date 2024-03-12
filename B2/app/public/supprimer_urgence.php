<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['urgence_id'])) {
        $urgenceId = $_POST['urgence_id'];

        if ($page->deleteUrgence($urgenceId)) {
            header('Location: administration.php');
            exit(); 
        }
    }
}

header('Location: accueil.php');
exit();
