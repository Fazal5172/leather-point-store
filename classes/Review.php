<?php
/**
 * Review Class (OOP)
 * Handles submission of product reviews, ratings, and website service feedback (FR7 & Admin 3 view feeds)
 */
class Review {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * User submits product review and rating (FR7)
     */
    public function addProductReview($user_id, $product_id, $rating, $review_text) {
        $query = "INSERT INTO reviews (user_id, product_id, rating, review_text) 
                  VALUES (:user_id, :product_id, :rating, :review_text)";
        $stmt = $this->conn->prepare($query);

        $review_text = htmlspecialchars(strip_tags($review_text));
        $rating = intval($rating);

        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":rating", $rating);
        $stmt->bindParam(":review_text", $review_text);

        return $stmt->execute();
    }

    /**
     * Get all reviews for a specific product
     */
    public function getProductReviews($product_id) {
        $query = "SELECT r.*, u.name as user_name FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.product_id = :product_id ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Submit general website feedback (FR7)
     */
    public function addWebsiteFeedback($name, $email, $message) {
        $query = "INSERT INTO feedbacks (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $message = htmlspecialchars(strip_tags($message));

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":message", $message);

        return $stmt->execute();
    }

    /**
     * Get all general website feedback responses (Admin 3)
     */
    public function getAllFeedbacks() {
        $query = "SELECT * FROM feedbacks ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>