<?php
require_once '../vendor/autoload.php';

use App\Page;

$page = new Page();
$msg = '';  

if(isset($_POST['send'])) {
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $inserted = $page->insert('users', [
            'email'     => $_POST['email'],
            'password'  => $hashedPassword,
            'nom'       => $_POST['nom'],
            'prenom'    => $_POST['prenom'],
            'adresse'    => $_POST['adresse'],
            'telephone'    => $_POST['telephone']
        ]);

        if ($inserted) {
            header("Location: index.php");
            exit(); 
        }  
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $msg = "Cette adresse e-mail est déjà associée à un compte.";
        } else {
            $msg = "Erreur : " . $e->getMessage();
        }
    }
}

echo $page->render('register.html.twig', ['msg' => $msg]);
?>
