<?php

require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    if ($page->deleteUsers($user_id)) {
        header("Location: administration.php");
        exit();
    } else {
        echo "Erreur lors de la suppression de l'utilisateur.";
    }
} else {
    echo "ID de l'utilisateur non spécifié.";
}
