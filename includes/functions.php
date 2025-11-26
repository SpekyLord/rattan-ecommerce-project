<?php
// CRUD Functions Library for Team to Use

function getAllProducts($conn) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE active = 1 ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND active = 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addProduct($conn, $data) {
    $stmt = $conn->prepare(
        "INSERT INTO products (name, description, price, stock, category, image_path) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([
        $data['name'], 
        $data['description'], 
        $data['price'], 
        $data['stock'], 
        $data['category'], 
        $data['image_path']
    ]);
}

function updateProduct($conn, $id, $data) {
    $stmt = $conn->prepare(
        "UPDATE products 
         SET name=?, description=?, price=?, stock=?, category=?, image_path=?
         WHERE id=?"
    );
    return $stmt->execute([
        $data['name'], 
        $data['description'], 
        $data['price'], 
        $data['stock'], 
        $data['category'], 
        $data['image_path'],
        $id
    ]);
}

function deleteProduct($conn, $id) {
    $stmt = $conn->prepare("UPDATE products SET active = 0 WHERE id = ?");
    return $stmt->execute([$id]);
}

function searchProducts($conn, $keyword) {
    $stmt = $conn->prepare(
        "SELECT * FROM products 
         WHERE active = 1 AND (name LIKE ? OR description LIKE ?)
         ORDER BY name"
    );
    $search = "%$keyword%";
    $stmt->execute([$search, $search]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>