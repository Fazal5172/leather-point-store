# Leather Point Store 👜

A professional, e-commerce web application developed in **Object-Oriented PHP (OOP)** and styled with modern, responsive **Tailwind CSS**.

---

## 🚀 Key Professional Features
- **Object-Oriented Programming (OOP):** Complete business models encapsulated in standalone classes with robust encapsulation, property bindings, and modular constructor dependency injections.
- **Secure Database Interactions:** Powered by **PHP Data Objects (PDO)** utilizing strictly parameterized, prepared statements to completely mitigate SQL injection risks.
- **Secure Hashing:** Customer passwords are salted and hashed using modern `PASSWORD_BCRYPT` cryptographies.
- **XSS Prevention:** Clean inputs sanitized dynamically using recursive string strips and `htmlspecialchars()` output encodings.
- **Session-Based State Management:** Secure multi-role authentication controls separating Standard Users and Administrative Desk operators.
- **Automatic Fallback Simulator ( recruiter-friendly ):** If a MySQL server is not configured, the site automatically enables an interactive **Session Mock DB Fallback mode** letting users register, log in, search, write reviews, check out, and administer orders in real-time completely out-of-the-box!

---

## 📁 Repository Directory Structure
```text
leather-point-store/
│
├── config/
│   ├── app.php               # Application configuration (APP_URL & global settings)
│   ├── bootstrap.php         # Centralized application initialization
│   ├── Database.php          # Database Connection class (PDO Pattern)
│   └── developer.php         # Developer contact & portfolio configuration
│
├── classes/
│   ├── User.php              # Authentication, profiles & customer management
│   ├── Product.php           # Product catalog & inventory operations
│   ├── Order.php             # Checkout, order processing & notifications
│   └── Review.php            # Product ratings & customer reviews
│
├── includes/
│   ├── init.php              # Customer application initialization
│   ├── header.php            # Storefront navigation & page header
│   ├── footer.php            # Footer layout & shared scripts
│   └── contact-widget.php    # Floating developer contact widget
│
├── admin/                    # Administrative Control Panel
│   ├── includes/
│   │   ├── init.php          # Admin application initialization & authorization
│   │   ├── header.php        # Admin sidebar & dashboard layout
│   │   └── footer.php        # Admin footer
│   ├── categories.php        # Category CRUD management
│   ├── dashboard.php         # Dashboard metrics & statistics
│   ├── feedbacks.php         # Customer feedback management
│   ├── logout.php            # Terminates admin session
│   ├── orders.php            # Order approval & management
│   ├── products.php          # Inventory & product management
│   ├── subcategories.php     # Subcategory CRUD management
│   └── users.php             # Customer directory management│
├── assets/
│   ├── css/
│   │   └── contact-widget.css    # Floating contact widget styles
│   └── js/
│       └── contact-widget.js     # Floating contact widget interactions
│
├── downloads/
│   └── Fazal-Abbas-Shah-Resume.pdf    # Downloadable resume
│
├── logs/
│   └── receipt_notifications.log      # Simulated SMS & Email notification logs
│
├── screenshots/              # README project screenshots
│
├── index.php                 # Storefront catalog & product listing (FR3)
├── product-details.php       # Product details & review page (FR7)
├── cart.php                  # Shopping cart
├── checkout.php              # Checkout & payment selection (FR4)
├── order-status.php          # Order tracking & printable receipt (FR5, FR8)
├── feedback.php              # Customer feedback submission (FR7)
├── login.php                 # Secure user authentication (FR2)
├── register.php              # Customer registration (FR1)
├── logout.php                # User/Admin logout & session termination
│
├── schema.sql                # Database schema & sample data
└── README.md                 # Professional project documentation
---

## 📋 Functional Requirements Map

This application is fully responsive and implements all functional specifications using clean OOP models:

### Customer-Facing Requirements (FR)
| Requirement ID | Description | OOP Implementation Path |
| :--- | :--- | :--- |
| **FR1: Registration** | Create a registration page for new users. | `register.php` utilizing `User::register()` with standard password hashing. |
![register](screenshots/register.png)

| **FR2: Login** | Secure login credentials authentication. | `login.php` using `User::login()` verifying against hashed storage. |
![login](screenshots/login.png)

| **FR3: Search Box** | Multi-attribute search (by name, price, and color). | `index.php` using `Product::getProducts()` utilizing multi-conditional dynamic SQL. |
![home](screenshots/home.png)

| **FR4: Payment Methods** | Choose Credit Card or Cash On Delivery (COD). | `checkout.php` capturing inputs and storing choices in order rows. |
![payment methods](screenshots/checkout.png)

| **FR5: Order Status** | Check order shipment status (Approved/Pending/Cancel). | `order-status.php` pulling status properties through `Order::getOrderDetails()`. |
![Order Status](screenshots/my_orders.png)


| **FR6: Order History** | List complete historical customer purchases. | `order-status.php` listing orders via `Order::getUserOrders()`. |
![Order History](screenshots/my_orders.png)


| **FR7: Reviews & Feedbacks** | Submit product review stars and general website feedback. | `product-details.php` using `Review::addProductReview()`; `feedback.php` using `Review::addWebsiteFeedback()`. |
![Website Feedback Form ](screenshots/feedback.png)

| **FR8: Confirm Receipt** | Simulate transaction copy receipt logging to phone & email. | `Order::simulateTransactionReceipt()` writing logs to `logs/receipt_notifications.log` displayed visually in real-time. |
![Confirm Receipt](screenshots/order_details.png)


### Administrative Requirements (Admin)
| Requirement ID | Description | OOP Implementation Path |
| :--- | :--- | :--- |
| **Admin 1: Admin Login** | Specialized administrator authorization gate. | `admin/includes/header.php` verification checks; secure login via `User::login()`. |
![Admin Login](screenshots/login.png)

| **Admin 2: Stock Management** | Control item prices, specs, images, colors, and quantities. | `admin/products.php` performing edits using `Product::updateProduct()` & `Product::deleteProduct()`. |
![Stock Management](screenshots/admin_manage_inventory.png)

| **Admin 3: Metric Dashboard** | View counts of users, orders, categories, and active feedback feeds. | `admin/dashboard.php` querying statistical records; `admin/feedbacks.php` listing messages. |
![Metric Dashboard](screenshots/admin_dashboard.png)

| **Admin 4: Categories CRUD** | Add, edit, or delete item classifications. | `admin/categories.php` using `Product::addCategory()`, `Product::updateCategory()`, etc. |
![Manage Categories](screenshots/admin_manage_categories.png)

| **Admin 5: Subcategories CRUD** | Add, edit, or delete related classifications. | `admin/subcategories.php` using `Product::addSubcategory()`, `Product::updateSubcategory()`, etc. |
![Manage Sub-categories](screenshots/admin_manage_subcategories.png)

| **Admin 6: Customers Directory** | List all registered non-admin users. | `admin/users.php` listing database directories using `User::getAllUsers()`. |
![Manage Users](screenshots/admin_manage_users.png)

| **Admin 7: Delete Users** | Remove accounts from the store directories. | `admin/users.php` using `User::deleteUser()`. |
![Manage Users](screenshots/admin_manage_users.png)

| **Admin 8: Update Users** | Edit name, email, and contact details of existing profiles. | `admin/users.php` editing profiles using `User::updateUserInfo()`. |
![Manage Users](screenshots/admin_manage_users.png)

| **Admin 9: Orders History** | Inspect and monitor all customer booking logs. | `admin/orders.php` listing overall histories via `Order::getAllOrders()`. |
![Orders History](screenshots/my_orders.png)

| **Admin 10: Approve / Cancel** | Approve orders for dispatch or cancel bookings. | `admin/orders.php` toggling states using `Order::updateStatus()`. |
![Orders Status](screenshots/my_orders.png)


---

## 🛠️ Installation & Setup Instructions

### 1. Database Setup (MySQL)
1. Open your database administration dashboard (e.g., **phpMyAdmin** or **MySQL Workbench**).
2. Create a new database named `leather_point_db`.
3. Import the `schema.sql` file into your newly created database.

### 2. Configure PHP Connection
1. Open `config/Database.php`.
2. Update your credentials (host, user, password, database name) inside the private properties:
   ```php
   private $host = "localhost";
   private $db_name = "leather_point_db";
   private $username = "root";
   private $password = ""; // your DB password
   ```

### 3. Quick Run Credentials
Use these pre-seeded users to test out both roles immediately:
- **Customer User Account:**
  - **Email:** `user@gmail.com`
  - **Password:** `userpassword`
- **Administrator Account:**
  - **Email:** `admin@leatherpoint.com`
  - **Password:** `adminpassword`

---
