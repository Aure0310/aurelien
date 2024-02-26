<?php
require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
if (!$page->session->isConnected()) {
    header('Location: index.php');  
    exit;
}

$user_id = $page->session->get('user')['id'];
$user = $page->getUserById(['id' => $user_id]);

if (!$user) {
    echo "Utilisateur introuvable";
    exit;
}

echo $page->render('edit_profil.html.twig', [
    'user' => $user
]);
