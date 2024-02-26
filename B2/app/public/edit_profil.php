<?php
require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';

if ($page->session->isConnected()) {
    $user_id = $page->session->get('user')['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'adresse' => $_POST['adresse'],
            'telephone' => $_POST['telephone'],
        ];

        $page->updateUserProfile($user_id, $data);
        header('Location: profil.php');
        $msg = 'Profil mis à jour avec succès!';
        exit();
    }

    $user = $page->getUserById(['id' => $user_id]);

    if ($user) {
        $prenom = $user['prenom'];
        $nom = $user['nom'];
        $role = $user['role'];
        $adresse = $user['adresse'];
        $telephone = $user['telephone'];
    }
}

echo $page->render('navbar.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'role' => $role,
]);

echo $page->render('edit_profil.html.twig', [
    'msg' => $msg,
    'prenom' => $prenom,
    'nom' => $nom,
    'adresse' => $adresse,
    'telephone' => $telephone,
]);
?>
