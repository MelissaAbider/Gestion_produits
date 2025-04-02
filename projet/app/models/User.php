<?php
//Interactions avec la table users
class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function authenticate($username, $password)
    {
        // requête pour trouver l'utilisateur par son nom d'utilisateur
        $query = "SELECT id, username, password FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // vérification si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {
            // retourne les données utilisateur sans le mot de passe
            unset($user['password']);
            return $user;
        }

        return false;
    }

    public function getUserById($id)
    {
        $query = "SELECT id, username, email, created_at FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function createUser($userData)
    {
        try {
            // hashage du mot de passe
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

            // préparation de la requête
            $query = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':username' => $userData['username'],
                ':password' => $hashedPassword,
                ':email' => $userData['email']
            ]);

            return $result;
        } catch (PDOException $e) {
            // en cas d'erreur
            return false;
        }
    }
}
