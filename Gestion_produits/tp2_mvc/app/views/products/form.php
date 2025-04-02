<?php
//Formulaire pour ajouter un produit
?>

<h2>Ajouter un produit</h2>

<form method="POST" action="index.php?action=add_product" enctype="multipart/form-data">
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
    <a href="index.php?action=home" class="button">Annuler</a>
</form>