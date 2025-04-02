<?php
// initialisation de la session
session_start();

// définition du chemin racine de l'application
define('ROOT_PATH', dirname(__DIR__));

// inclusion des fichiers nécessaires
require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'utils' . DIRECTORY_SEPARATOR . 'functions.php';
require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'ProductController.php';
require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'UserController.php';

// détermination de l'action demandée
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// vérification si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);

// routage des requêtes vers les contrôleurs 
if ($action === 'login') {
    // route pour la connexion
    $userController = new UserController($db);
    $userController->login();
} elseif ($action === 'logout') {
    // route pour la déconnexion
    $userController = new UserController($db);
    $userController->logout();
} else {
    // routes pour la gestion des produits
    $productController = new ProductController($db);

    switch ($action) {
        case 'home':
            // afficher la liste des produits
            $productController->index();
            break;
        case 'add_product':
            // ajouter un produit (uniquement pour les utilisateurs connectés)
            if ($isLoggedIn) {
                $productController->add();
            } else {
                // rediriger vers la page de connexion si non connecté
                $_SESSION['error_message'] = "Vous devez être connecté pour ajouter un produit";
                header('Location: index.php?action=login');
                exit;
            }
            break;
        case 'delete':
            // supprimer un produit (uniquement pour les utilisateurs connectés)
            if ($isLoggedIn) {
                $productController->delete();
            } else {
                $_SESSION['error_message'] = "Vous devez être connecté pour supprimer un produit";
                header('Location: index.php?action=login');
                exit;
            }
            break;
        case 'calculate':
            // calculer le prix total
            $productController->calculateTotal();
            break;
        case 'reduction':
            // appliquer une réduction (les utilisateurs connectés)
            if ($isLoggedIn) {
                $productController->applyDiscount();
            } else {
                $_SESSION['error_message'] = "Vous devez être connecté pour appliquer une réduction";
                header('Location: index.php?action=login');
                exit;
            }
            break;
        case 'restore':
            // restaurer les prix d'origine (les utilisateurs connectés)
            if ($isLoggedIn) {
                $productController->restoreOriginalPrices();
            } else {
                $_SESSION['error_message'] = "Vous devez être connecté pour restaurer les prix";
                header('Location: index.php?action=login');
                exit;
            }
            break;
        default:
            // action non reconnue 
            header("HTTP/1.0 404 Not Found");
            echo "<h1>Page non trouvée</h1>";
            echo "<p>L'action demandée n'existe pas.</p>";
            echo "<a href='index.php'>Retour à l'accueil</a>";
            break;
    }
}
