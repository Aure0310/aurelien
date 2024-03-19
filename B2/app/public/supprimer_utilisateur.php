<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {
    if (isset($_GET['id'])) {
        $user_id = $_GET['id'];

        if ($page->deleteUsers($user_id)) {
        }
        header('Location: administration.php');
        exit();
    }
} 
