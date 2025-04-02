<?php
//Interactions avec la table products
class Product
{
    private $db;

    // Constructeur appelé dans la création de Product
    public function __construct($db)
    {
        // On stocke l'objet PDO (connexion à la base de données)
        $this->db = $db;
    }

    // Récupérer tous les produits de la BD
    public function getAllProducts()
    {
        // Sélectionner tous les produits
        $query = "SELECT * FROM products";
        // Préparation de la requete
        $stmt = $this->db->prepare($query);
        // Exécution de la requete
        $stmt->execute();
        // Récupération de tous les résultats
        return $stmt->fetchAll();
    }

    // Récupérer les produits selon leur catégorie
    public function getProductsByCategory($category)
    {
        $query = "SELECT * FROM products WHERE category = :category";
        $stmt = $this->db->prepare($query);
        // Exécution avec la valeur 
        $stmt->execute([':category' => $category]);
        // Récupération des résultats
        return $stmt->fetchAll();
    }

    // Fonction pour rechercher des produits selon un mot-clé
    public function searchProducts($searchTerm)
    {
        // avec LIKE pour chercher par nom
        $query = "SELECT * FROM products WHERE name LIKE :search";
        $stmt = $this->db->prepare($query);
        // Exécution avec le terme de recherche 
        $stmt->execute([':search' => '%' . $searchTerm . '%']);
        return $stmt->fetchAll();
    }

    // Ajouter un nouveau produit
    public function addProduct($productData)
    {
        try {
            // insertion avec les champs correspondants
            $query = "INSERT INTO products (name, price, original_price, imageURL, category, inStock) 
                      VALUES (:name, :price, :original_price, :imageURL, :category, :inStock)";

            $stmt = $this->db->prepare($query);

            // Exécution avec les données du tableau
            $result = $stmt->execute([
                ':name' => $productData['name'],
                ':price' => $productData['price'],
                ':original_price' => $productData['price'], // original_price = price à l'ajout
                ':imageURL' => $productData['imageURL'],
                ':category' => $productData['category'],
                ':inStock' => $productData['inStock']
            ]);

            // ttrue si l'ajout a réussi
            return $result;
        } catch (PDOException $e) {
            // on stocke le message dans la session si erreur
            $_SESSION['error_message'] = "erreur lors de l'ajout du produit : " . $e->getMessage();

            // Retourne false si une exception
            return false;
        }
    }

    // supprimer un produit par son identifiant
    public function deleteProduct($id)
    {
        // supprimer un produit
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // calculer le total des prix des produits en stock
    public function calculateTotal($category = null, $searchTerm = null)
    {
        // somme des prix des produits
        $query = "SELECT SUM(price) as total FROM products WHERE inStock = 1";

        // paramètres à passer à la requête
        $params = [];

        // filtre de catégorie
        if ($category) {
            $query .= " AND category = :category";
            $params[':category'] = $category;
        }

        // Ajout d'un filtre de recherche 
        if ($searchTerm) {
            $query .= " AND name LIKE :search";
            $params[':search'] = '%' . $searchTerm . '%';
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();

        // Retourne le total ou 0 
        return $result['total'] ?? 0;
    }

    // appliquer une réduction de 10% 
    public function applyDiscount($category = null, $searchTerm = null)
    {
        // réduction de 10% sur tous les produits
        $query = "UPDATE products SET price = price * 0.9";

        // Tableau pour stocker les paramètres de filtre
        $params = [];

        // construire les conditions WHERE
        $whereConditions = [];

        // Si une catégorie est sélectionnée on l'ajoute
        if ($category) {
            $whereConditions[] = "category = :category";
            $params[':category'] = $category;
        }

        // Si un mot clé est sélectionné on l'ajoute
        if ($searchTerm) {
            $whereConditions[] = "name LIKE :search";
            $params[':search'] = '%' . $searchTerm . '%';
        }

        // s'il y a des conditions on les ajoute à la requête
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $stmt = $this->db->prepare($query);

        return $stmt->execute($params);
    }

    // restaurer les prix d'origine
    public function restoreOriginalPrices($category = null, $searchTerm = null)
    {
        // remettre les prix à leur valeur d'origine
        $query = "UPDATE products SET price = original_price";

        // Tableau des paramètres
        $params = [];

        // Tableau pour les conditions where
        $whereConditions = [];

        // Ajout de la catégorie
        if ($category) {
            $whereConditions[] = "category = :category";
            $params[':category'] = $category;
        }

        // Ajout de la recherche
        if ($searchTerm) {
            $whereConditions[] = "name LIKE :search";
            $params[':search'] = '%' . $searchTerm . '%';
        }

        // Ajout des conditions à la requête si elles existent
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }
}
