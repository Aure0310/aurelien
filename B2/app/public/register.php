<?php

    require_once '../vendor/autoload.php';

    use App\Page;
    
    $page = new Page();

    if(isset($_POST['send'])) {

        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $page->insert('users', [
            'email'     => $_POST['email'],
            'password'  => $hashedPassword
        ]);
    }

    echo $page->render('register.html.twig', []);