<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if (isset($_GET['id'])) {
        $urgenceId = $_GET['id'];

        if ($page->deleteUrgence($urgenceId)) {
        } 
        header('Location: administration.php');
        exit();
    }
}
