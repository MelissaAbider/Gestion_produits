<?php

// paramètres de connexion à la base de données MySQL
$host = 'localhost';
$user = 'root';
$password = '';

try {
    // connexion au serveur MySQL sans spécifier de base de données
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // création de la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS gestion_produits");

    // utilisation de la base de données
    $pdo->exec("USE gestion_produits");

    // création de la table products si elle n'existe pas
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        original_price DECIMAL(10, 2) NOT NULL, 
        imageURL VARCHAR(255),
        category VARCHAR(50) NOT NULL,
        inStock TINYINT(1) DEFAULT 0
    )";

    $pdo->exec($sql);

    // création de la table users si elle n'existe pas
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);

    // insérer un utilisateur par défaut (admin/admin)
    $checkUser = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn();

    if ($checkUser == 0) {
        $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $hashedPassword, 'admin@example.com']);
    }

    echo "base de données, tables et utilisateur admin créés avec succès!";

    // création de la structure de dossiers 
    $directories = [
        'app',
        'app/controllers',
        'app/models',
        'app/views',
        'app/views/layouts',
        'app/views/products',
        'app/views/users',
        'app/config',
        'app/utils',
        'public',
        'public/css',
        'public/uploads'
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            echo "<br>Dossier '$dir' créé avec succès.";
        }
    }

    echo "<br><br>Initialisation terminée ! Vous pouvez maintenant <a href='public/index.php'>accéder à l'application</a>.";
} catch (PDOException $e) {
    die("erreur d'initialisation de la BD " . $e->getMessage());
}
