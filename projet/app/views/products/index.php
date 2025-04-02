<?php

// récupération des variables 
$selectedCategory = $_GET['filter'] ?? '';
$searchTerm = $_GET['search'] ?? '';
?>

<!-- formulaire d'ajout de produit (visible uniquement si connecté) -->
<?php if ($isLoggedIn): ?>
    <form id="product-form" method="POST" action="index.php?action=add_product" enctype="multipart/form-data">
        <div class="form-group">
            <label for="product_name">Nom du produit :</label>
            <input type="text" id="product_name" name="product_name" required>
        </div>
        <div class="form-group">
            <label for="product_price">Prix du produit :</label>
            <input type="number" id="product_price" name="product_price" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="product_image">Image du produit :</label>
            <input type="file" id="product_image" name="product_image" accept=".jpg, .jpeg, .png">
        </div>
        <div class="form-group">
            <label for="product_category">Catégorie :</label>
            <select id="product_category" name="product_category" required>
                <option value="">--Sélectionner une catégorie--</option>
                <option value="Électronique">Électronique</option>
                <option value="Vêtements">Vêtements</option>
                <option value="Alimentation">Alimentation</option>
            </select>
        </div>
        <div class="form-group">
            <label for="product_stock">
                <input type="checkbox" id="product_stock" name="product_stock">
                En stock
            </label>
        </div>
        <button type="submit">Ajouter le produit</button>
    </form>
<?php else: ?>
    <div class="login-message">
        <p>Connectez-vous pour pouvoir ajouter de nouveaux produits.</p>
    </div>
<?php endif; ?>

<div class="search-filter">
    <div>
        <!-- recherche par nom -->
        <form method="GET" action="index.php">
            <input type="hidden" name="action" value="home">
            <label for="search">Rechercher par nom :</label>
            <input type="text" id="search" name="search" placeholder="Rechercher un produit"
                value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>
    <div>
        <!-- filtrage par catégorie -->
        <form method="GET" action="index.php">
            <input type="hidden" name="action" value="home">
            <label for="filter">Filtrer par catégorie :</label>
            <select id="filter" name="filter" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                <option value="Électronique" <?php echo ($selectedCategory == 'Électronique') ? 'selected' : ''; ?>>Électronique</option>
                <option value="Vêtements" <?php echo ($selectedCategory == 'Vêtements') ? 'selected' : ''; ?>>Vêtements</option>
                <option value="Alimentation" <?php echo ($selectedCategory == 'Alimentation') ? 'selected' : ''; ?>>Alimentation</option>
            </select>
        </form>
    </div>
</div>

<!-- liste des produits -->
<ul id="products">
    <?php if (empty($products)): ?>
        <li>Aucun produit disponible.</li>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <li>
                <div>
                    <?php if (!empty($product['imageURL'])): ?>
                        <img src="<?php echo htmlspecialchars($product['imageURL']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image">
                    <?php endif; ?>
                    <?php echo htmlspecialchars($product['name']); ?> -
                    <?php echo htmlspecialchars($product['category']); ?>
                </div>
                <div>
                    Prix : <?php echo number_format($product['price'], 2); ?> € -
                    <?php echo $product['inStock'] ? 'En stock' : 'Pas dans le stock'; ?>
                    <?php if ($isLoggedIn): ?>
                        <a href="index.php?action=delete&id=<?php echo $product['id']; ?>" class="delete-btn">Supprimer</a>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<div class="actions">
    <?php
    // construction des liens 
    $urlParams = [];

    // le paramètre de filtrage par catégorie
    if (!empty($selectedCategory)) {
        $urlParams['filter'] = $selectedCategory;
    }

    // le paramètre de recherche
    if (!empty($searchTerm)) {
        $urlParams['search'] = $searchTerm;
    }
    ?>

    <a href="<?php echo buildActionUrl('calculate', $urlParams); ?>" class="button">Calculer le prix total des produits en stock</a>

    <?php if ($isLoggedIn): ?>
        <a href="<?php echo buildActionUrl('reduction', $urlParams); ?>" class="button">Réduction de 10%</a>
        <a href="<?php echo buildActionUrl('restore', $urlParams); ?>" class="button">Prix d'origine</a>
    <?php endif; ?>

    <p id="total_price">
        <?php if (isset($totalPriceMessage)): ?>
            <?php echo htmlspecialchars($totalPriceMessage); ?>
        <?php endif; ?>
    </p>
</div>