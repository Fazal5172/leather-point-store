<?php
/**
 * Order Class (OOP)
 * Handles checkout transaction logic, order status lookups, history, and simulation of receipts (FR4, FR5, FR6, FR8).
 */
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create an order (FR4 Checkout & FR8 Confirmation)
     */
    public function create($user_id, $total_amount, $payment_method, $address, $phone, $email, $cart_items) {
        try {
            $this->conn->beginTransaction();

            // Insert into Orders table
            $query = "INSERT INTO orders (user_id, total_amount, payment_method, status, shipping_address, phone, email) 
                      VALUES (:user_id, :total_amount, :payment_method, 'pending', :address, :phone, :email)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":total_amount", $total_amount);
            $stmt->bindParam(":payment_method", $payment_method);
            $stmt->bindParam(":address", $address);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":email", $email);
            
            $stmt->execute();
            $order_id = $this->conn->lastInsertId();

            // Insert each item into Order Items and update product stocks
            foreach ($cart_items as $product_id => $item) {
                // Insert order item
                $item_query = "INSERT INTO order_items (order_id, product_id, price, quantity) 
                              VALUES (:order_id, :product_id, :price, :quantity)";
                $item_stmt = $this->conn->prepare($item_query);
                $item_stmt->bindParam(":order_id", $order_id);
                $item_stmt->bindParam(":product_id", $product_id);
                $item_stmt->bindParam(":price", $item['price']);
                $item_stmt->bindParam(":quantity", $item['quantity']);
                $item_stmt->execute();

                // Deduct stock (Admin 2: Stocks auto-managed on order!)
                $stock_query = "UPDATE products SET stock = stock - :qty WHERE id = :product_id";
                $stock_stmt = $this->conn->prepare($stock_query);
                $stock_stmt->bindParam(":qty", $item['quantity']);
                $stock_stmt->bindParam(":product_id", $product_id);
                $stock_stmt->execute();
            }

            $this->conn->commit();

            // FR8: After transaction, simulation of confirmation logs
            $this->simulateTransactionReceipt($order_id, $phone, $email, $total_amount, $cart_items);

            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Order failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve order history for a specific logged-in user (FR6)
     */
    public function getUserOrders($user_id) {
        $query = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Retrieve complete history of orders (Admin 9)
     */
    public function getAllOrders() {
        $query = "SELECT o.*, u.name as user_name FROM orders o 
                  JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get specific order status and details (FR5)
     */
    public function getOrderDetails($order_id) {
        $query = "SELECT o.*, u.name as user_name FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  WHERE o.id = :order_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Fetch list of items inside an order
     */
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.name as product_name, p.image FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update order status - approve/cancel/ship (Admin 10 & FR5 status checking)
     */
    public function updateStatus($order_id, $status) {
        $query = "UPDATE orders SET status = :status WHERE id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":order_id", $order_id);
        return $stmt->execute();
    }

    /**
     * MOCK receipt generator logging the confirmation message & details (FR8)
     */
    private function simulateTransactionReceipt($order_id, $phone, $email, $amount, $items) {
        $log_dir = __DIR__ . "/../logs/";
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        $log_file = $log_dir . "receipt_notifications.log";
        $date = date("Y-m-d H:i:s");

        $receipt = "=========================================================\n";
        $receipt .= "TRANSACTION CONFIRMATION RECEIPT (Simulated) - " . $date . "\n";
        $receipt .= "Order Reference: #LPS-" . str_pad($order_id, 6, "0", STR_PAD_LEFT) . "\n";
        $receipt .= "User Email: " . $email . " | Contact: " . $phone . "\n";
        $receipt .= "Total Amount Charged: $" . number_format($amount, 2) . "\n";
        $receipt .= "Purchased Products:\n";
        foreach ($items as $p_id => $item) {
            $receipt .= "  - " . $item['name'] . " (Qty: " . $item['quantity'] . " | Price: $" . number_format($item['price'], 2) . ")\n";
        }
        $receipt .= "---------------------------------------------------------\n";
        $receipt .= "[SIMULATED SMS SENT TO " . $phone . "]\n";
        $receipt .= "SMS Msg: \"Thank you! Your order #LPS-" . str_pad($order_id, 6, "0", STR_PAD_LEFT) . " has been received successfully. Total amount: $" . number_format($amount, 2) . ". The shipment status is currently PENDING.\"\n";
        $receipt .= "\n[SIMULATED EMAIL INVOICE SENT TO " . $email . "]\n";
        $receipt .= "Email Msg: \"Dear Leather Point customer, we have successfully processed your payment. Your receipt and shopping confirmation invoice for order #LPS-" . str_pad($order_id, 6, "0", STR_PAD_LEFT) . " are attached here as PDFs.\"\n";
        $receipt .= "=========================================================\n\n";

        file_put_contents($log_file, $receipt, FILE_APPEND);
    }

    /**
     * Read the transaction notifications logs (FR8 review utility)
     */
    public function getNotificationLogs() {
        $log_file = __DIR__ . "/../logs/receipt_notifications.log";
        if (file_exists($log_file)) {
            return file_get_contents($log_file);
        }
        return "No notifications triggered yet. Place an order to see confirmation SMS and Email mock traces.";
    }
}
?>