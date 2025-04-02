<?php

$basePath = __DIR__;
$directories = [
    'app',
    'app/config',
    'app/controllers',
    'app/models',
    'app/utils',
    'app/views',
    'app/views/layouts',
    'app/views/products',
    'app/views/users',
    'public',
    'public/css',
    'public/uploads'
];

echo "Vérification de la structure de dossiers...\n";
foreach ($directories as $dir) {
    $path = $basePath . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($path)) {
        if (mkdir($path, 0755, true)) {
            echo "Dossier créé: $path\n";
        } else {
            echo "ERREUR: Impossible de créer le dossier: $path\n";
        }
    } else {
        echo "Le dossier existe déjà: $path\n";
    }
}

// Vérifier les fichiers essentiels
$files = [
    'app/config/database.php',
    'app/controllers/ProductController.php',
    'app/controllers/UserController.php',
    'app/models/Product.php',
    'app/models/User.php',
    'app/utils/functions.php',
    'app/views/layouts/header.php',
    'app/views/layouts/footer.php',
    'app/views/products/index.php',
    'app/views/products/form.php',
    'app/views/users/login.php',
    'public/css/styles.css',
    'public/index.php'
];

echo "\nVérification des fichiers essentiels...\n";
foreach ($files as $file) {
    $path = $basePath . DIRECTORY_SEPARATOR . $file;
    if (!file_exists($path)) {
        echo "MANQUANT: $path\n";
    } else {
        echo "Existe: $path\n";
    }
}

echo "\nTerminé.\n";
