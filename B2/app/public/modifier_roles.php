<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if ($page->session->isConnected() && $page->session->hasRole('Admin')) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST['user_id']) && isset($_POST['roles'])) {

            $userId = $_POST['user_id'];
            $newRole = $_POST['roles'][$userId];

            $page->updateUserRole(['id' => $userId, 'role' => $newRole]);

            header('Location: administration.php?msg=Roles modifiés avec succès');
            exit();
        }
    }
}

header('Location: accueil.php');
exit();