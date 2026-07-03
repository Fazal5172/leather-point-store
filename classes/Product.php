<?php
/**
 * Product Class (OOP)
 * Handles Product catalog, category/subcategory management, inventory management, and searching.
 */
class Product {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- CATEGORY CRUD (Admin 4) ---

    public function addCategory($name) {
        $query = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $name = strip_tags($name);
        $stmt->bindParam(":name", $name);
        return $stmt->execute();
    }

    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCategoryById($id) {
        $query = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateCategory($id, $name) {
        $query = "UPDATE categories SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $name = strip_tags($name);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // --- SUBCATEGORY CRUD (Admin 5) ---

    public function addSubcategory($category_id, $name) {
        $query = "INSERT INTO subcategories (category_id, name) VALUES (:category_id, :name)";
        $stmt = $this->conn->prepare($query);
        $name = strip_tags($name);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":name", $name);
        return $stmt->execute();
    }

    public function getSubcategories() {
        $query = "SELECT s.*, c.name as category_name FROM subcategories s 
                  JOIN categories c ON s.category_id = c.id 
                  ORDER BY c.name ASC, s.name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSubcategoriesByCategory($category_id) {
        $query = "SELECT * FROM subcategories WHERE category_id = :category_id ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSubcategoryById($id) {
        $query = "SELECT * FROM subcategories WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateSubcategory($id, $category_id, $name) {
        $query = "UPDATE subcategories SET category_id = :category_id, name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $name = strip_tags($name);
        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function deleteSubcategory($id) {
        $query = "DELETE FROM subcategories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // --- PRODUCT CRUD & SEARCH (Admin 2 & FR3) ---

    /**
     * Add a product item
     */
    public function addProduct($category_id, $subcategory_id, $name, $description, $price, $color, $stock, $image) {
        $query = "INSERT INTO products (category_id, subcategory_id, name, description, price, color, stock, image) 
                  VALUES (:category_id, :subcategory_id, :name, :description, :price, :color, :stock, :image)";
        $stmt = $this->conn->prepare($query);

        $name = strip_tags($name);
        $description = strip_tags($description);
        $color = strip_tags($color);
        $image = strip_tags($image);

        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":subcategory_id", $subcategory_id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":color", $color);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":image", $image);

        return $stmt->execute();
    }

    /**
     * Get products with optional search parameters for multi-filter (FR3)
     */
    public function getProducts($searchName = "", $searchPrice = "", $searchColor = "", $category_id = "", $subcategory_id = "") {
        $query = "SELECT p.*, c.name as category_name, s.name as subcategory_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN subcategories s ON p.subcategory_id = s.id WHERE 1=1";
        
        $params = [];
        
        // FR3: Search box for items by Name
        if (!empty($searchName)) {
            $query .= " AND p.name LIKE :name";
            $params[":name"] = "%" . $searchName . "%";
        }
        // FR3: Search box for items by Price range (max price)
        if (!empty($searchPrice)) {
            $query .= " AND p.price <= :price";
            $params[":price"] = $searchPrice;
        }
        // FR3: Search box for items by Color
        if (!empty($searchColor)) {
            $query .= " AND p.color LIKE :color";
            $params[":color"] = "%" . $searchColor . "%";
        }
        // Category filtering
        if (!empty($category_id)) {
            $query .= " AND p.category_id = :cat_id";
            $params[":cat_id"] = $category_id;
        }
        // Subcategory filtering
        if (!empty($subcategory_id)) {
            $query .= " AND p.subcategory_id = :sub_cat_id";
            $params[":sub_cat_id"] = $subcategory_id;
        }

        $query .= " ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch single product details
     */
    public function getProductById($id) {
        $query = "SELECT p.*, c.name as category_name, s.name as subcategory_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Update product details (Admin 2)
     */
    public function updateProduct($id, $category_id, $subcategory_id, $name, $description, $price, $color, $stock, $image = "") {
        $query = "UPDATE products SET category_id = :category_id, subcategory_id = :subcategory_id, 
                  name = :name, description = :description, price = :price, color = :color, stock = :stock";
        
        if (!empty($image)) {
            $query .= ", image = :image";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $name = strip_tags($name);
        $description = strip_tags($description);
        $color = strip_tags($color);

        $stmt->bindParam(":category_id", $category_id);
        $stmt->bindParam(":subcategory_id", $subcategory_id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":color", $color);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":id", $id);
        
        if (!empty($image)) {
            $image = strip_tags($image);
            $stmt->bindParam(":image", $image);
        }

        return $stmt->execute();
    }

    /**
     * Delete product (Admin 2)
     */
    public function deleteProduct($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
