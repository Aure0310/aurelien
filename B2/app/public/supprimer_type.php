<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type_id'])) {
        $typeId = $_POST['type_id'];

        if ($page->deleteType($typeId)) {
            header('Location: administration.php');
            exit(); 
        }
    }
}

header('Location: accueil.php');
exit();
