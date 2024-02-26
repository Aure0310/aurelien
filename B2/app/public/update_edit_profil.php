<?php

require_once '../vendor/autoload.php';
use App\Page;

$page = new Page();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have a hidden input field in your form for the user's ID
    $user_id = $_POST['id'];

    // Collect data from the form
    $data = [
        'nom' => filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING),
        'prenom' => filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING),
        'adresse' => filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING),
        'telephone' => filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING),
        //'adresse' => $_POST['adresse'],
        //'telephone' => $_POST['telephone'],
       
    ];

    // Attempt to update the user's information
    try {
        $updateSuccess = $page->updateUserById($user_id, $data);

        if ($updateSuccess) {
            // Redirect to the profile page with a success message
            header('Location: profil.php?msg=Profil mis à jour avec succès');
        } else {
            // Handle the case where the update did not succeed
            echo "Erreur lors de la mise à jour du profil.";
        }
    } catch (Exception $e) {
        // Handle any exceptions, such as database errors
        echo "Une erreur est survenue : " . $e->getMessage();
    }
} else {
    // Redirect back to the edit profile page if the method is not POST
    header('Location: edit_profil.php');
}

