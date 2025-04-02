<?php
// Contrôleur des utilisateurs
require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'User.php';

class UserController
{
    private $db;
    private $userModel;

    // Initialise le contrôleur
    public function __construct($db)
    {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    // Connexion utilisateur
    public function login()
    {
        // Redirige si déjà connecté
        if (isset($_SESSION['user_id'])) {
            redirect('home');
        }

        $error_message = '';

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error_message = 'Veuillez remplir tous les champs';
            } else {
                // Authentification
                $user = $this->userModel->authenticate($username, $password);

                if ($user) {
                    // Enregistrement dans la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    // Redirection
                    redirect('home');
                } else {
                    $error_message = 'Identifiants incorrects';
                }
            }
        }

        // Affichage du formulaire
        include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'header.php';
        include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'login.php';
        include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'footer.php';
    }

    // Déconnexion utilisateur
    public function logout()
    {
        // Réinitialise la session
        $_SESSION = [];

        // Supprime le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Détruit la session
        session_destroy();

        // Redirection
        redirect('login');
    }
}
