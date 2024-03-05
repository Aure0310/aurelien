<?php
require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['nom'];

    if (!empty($name)) { // Correction ici
        $page->insertStatut($name);
        $msg = 'Le nouveau statut a été créé avec succès.';
    } else {
        $msg = 'Veuillez remplir tous les champs.';
    }
}

echo $page->render('nouveau_statut.html.twig', ['msg' => $msg]);
?>
