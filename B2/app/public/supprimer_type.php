<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if (isset($_GET['id'])) {
        $typeId = $_GET['id'];

        if ($page->deleteType($typeId)) {
        } 
        header('Location: administration.php');
        exit();
    }
}
