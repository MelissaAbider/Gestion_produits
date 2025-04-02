<?php

require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Product.php';

class ProductController
{
    private $db;
    private $productModel;

    //Initialise la connexion à la base de données
    // Initialise le contrôleur et le modèle produit
    public function __construct($db)
    {
        $this->db = $db;
        $this->productModel = new Product($db);
    }

    // Affiche la page d'accueil avec la liste des produits
    public function index()
    {
        // Récupère les filtres depuis l'URL (catégorie ou recherche)
        $category = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : null;
        $searchTerm = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : null;

        // Charge les produits selon le filtre ou la recherche
        if ($category) {
            $products = $this->productModel->getProductsByCategory($category);
        } elseif ($searchTerm) {
            $products = $this->productModel->searchProducts($searchTerm);
        } else {
            $products = $this->productModel->getAllProducts();
        }

        // Vérifie si l'utilisateur est connecté
        $isLoggedIn = isset($_SESSION['user_id']);

        // Récupère un éventuel message de prix total
        $totalPriceMessage = isset($_SESSION['total_price_message']) ? $_SESSION['total_price_message'] : null;

        // Affiche la page avec l'en-tête le contenu et le pied de page
        include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'header.php';
        include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . 'index.php';
        include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'footer.php';
    }

    // Gère l'ajout d'un nouveau produit
    public function add()
    {
        // Si le formulaire est envoyé
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
            // Récupère et nettoie les données du formulaire
            $name = sanitize($_POST['product_name']);
            $price = floatval($_POST['product_price']);
            $category = sanitize($_POST['product_category']);
            $inStock = isset($_POST['product_stock']) ? 1 : 0;

            // Gère l'image du produit
            $imageURL = '';
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                $tempName = $_FILES['product_image']['tmp_name'];
                $fileName = $_FILES['product_image']['name'];
                $uploadDir = 'uploads/';

                // Crée le dossier si nécessaire
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755);
                }

                // Enregistre l'image
                if (move_uploaded_file($tempName, $uploadDir . $fileName)) {
                    $imageURL = $uploadDir . $fileName;
                }
            }

            // Prépare les données à enregistrer
            $productData = [
                'name' => $name,
                'price' => $price,
                'imageURL' => $imageURL,
                'category' => $category,
                'inStock' => $inStock
            ];

            // Ajoute le produit
            if ($this->productModel->addProduct($productData)) {
                redirect('home'); // Succès
            } else {
                redirect('home'); // Échec
            }
        } else {
            // Affiche le formulaire d'ajout
            include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'header.php';
            include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . 'form.php';
            include ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'footer.php';
        }
    }

    // Supprime un produit
    public function delete()
    {
        // Vérifie que l'ID du produit est correct
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int)$_GET['id'];

            // Supprime le produit
            if ($this->productModel->deleteProduct($id)) {
                redirect('home'); // Succès
            } else {
                $_SESSION['error_message'] = "Erreur lors de la suppression";
                redirect('home');
            }
        } else {
            $_SESSION['error_message'] = "ID invalide";
            redirect('home');
        }
    }

    // Calcule le prix total des produits en stock
    public function calculateTotal()
    {
        // Récupère les filtres (catégorie ou recherche)
        $category = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : null;
        $searchTerm = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : null;

        // Calcule le prix total
        $totalPrice = $this->productModel->calculateTotal($category, $searchTerm);

        // Enregistre le message du total
        $_SESSION['total_price_message'] = sprintf("Prix total des produits en stock : %.2f €", $totalPrice);

        // Redirige vers la page avec les filtres appliqués
        $params = [];
        if ($category) $params['filter'] = $category;
        if ($searchTerm) $params['search'] = $searchTerm;

        redirect('home', $params);
    }

    // Applique une réduction sur les prix
    public function applyDiscount()
    {
        $category = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : null;
        $searchTerm = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : null;

        // Applique la réduction
        if ($this->productModel->applyDiscount($category, $searchTerm)) {
            $_SESSION['success_message'] = "Réduction appliquée";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la réduction";
        }

        $params = [];
        if ($category) $params['filter'] = $category;
        if ($searchTerm) $params['search'] = $searchTerm;

        redirect('home', $params);
    }

    // Restaure les prix d'origine
    public function restoreOriginalPrices()
    {
        $category = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : null;
        $searchTerm = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : null;

        // Restaure les prix
        if ($this->productModel->restoreOriginalPrices($category, $searchTerm)) {
            $_SESSION['success_message'] = "Prix d'origine restaurés";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la restauration des prix";
        }

        $params = [];
        if ($category) $params['filter'] = $category;
        if ($searchTerm) $params['search'] = $searchTerm;

        redirect('home', $params);
    }
}
