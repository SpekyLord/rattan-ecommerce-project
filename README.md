# 🇵🇭 Rattan E-Commerce Project

## 📋 Project Overview
A Filipino culture-focused e-commerce website showcasing handcrafted rattan products with a complete admin dashboard for inventory management.

**Project Duration**: November 24 - December 5, 2024  
**Team Size**: 5 members

---

## ✨ Features

### User Side
- Homepage with featured products
- Product catalog with search
- Individual product details with stock status
- About page (Filipino rattan craftsmanship)
- Mobile-responsive design

### Admin Dashboard
- Secure login system
- **CRUD Operations**:
  - ✅ Create: Add new products with images
  - ✅ Read: View all products
  - ✅ Update: Edit product details
  - ✅ Delete: Remove products
- Order management

---

## 🛠️ Technology Stack
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP 7.4+, MySQL (Aiven Cloud Database)
- **Tools**: Git, XAMPP

---

## 📁 Project Structure

```
rattan-ecommerce-project/
├── index.php
├── products.php
├── product_detail.php
├── about.php
├── contact.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       ├── bgs/
│       └── products/
├── admin/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── navbar.php
│   ├── sidebar.php
│   ├── products_manage.php
│   ├── orders_manage.php
│   ├── product_add.php
│   ├── product_delete.php
├── includes/
│   ├── db_connect.php
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── config/
│   └── db_config.php
├── database/
│   └── schema.sql
├── .gitignore
└── README.md

```

---

## 🚀 Installation

### Prerequisites
- PHP 7.4+
- Aiven MySQL database account
- Apache (XAMPP)
- Git

### Setup Steps

1. **Clone Repository**
```bash
git clone https://github.com/YOUR-USERNAME/rattan-ecommerce-project.git
cd rattan-ecommerce-project
```

2. **Set Up Server**
- Move project to `htdocs/` (XAMPP)
- Start Apache

3. **Set Up Aiven Database**
- Log in to your Aiven account
- Access your MySQL service
- Use the provided connection details (host, port, username, password)
- Create database: `rattan_shop`
- Import schema: Run the SQL from `database/schema.sql`

4. **Configure Database Connection**
```bash
cd config
cp db_config.template.php db_config.php
# Edit db_config.php with your Aiven MySQL credentials
```

**Example db_config.php for Aiven:**
```php
<?php
define('DB_HOST', 'your-project-name.aivencloud.com');
define('DB_PORT', '12345'); // Aiven port
define('DB_NAME', 'rattan_shop');
define('DB_USER', 'avnadmin');
define('DB_PASS', 'your-aiven-password');
?>
```

5. **Access Website**
- User site: `http://localhost/rattan-ecommerce-project/`
- Admin login: `http://localhost/rattan-ecommerce-project/admin/login.php`
  - Username: `admin`
  - Password: `admin123`

---

## 👥 Team Members

### Project Manager / Full Stack
- **Francis Gabriel Austria** - Project lead, integration, code reviews, GitHub management

### Frontend Team
- **Lorin Mikaela Amaller Sernicula** - Homepage design, product pages, responsive layout
- **Mikay Cruz** - Product pages styling, about page, mobile responsiveness

### Backend Team
- **Ace Camariosa** - Admin authentication, CRUD operations, database management
- **Lucky Rey Tumbokon** - Database schema, order management, security implementation

---

## 🔄 Git Workflow

### Branch Structure
```
main
├── dev
├── frontend-dev
└── backend-dev
```

### Daily Workflow
```bash
git checkout frontend-dev       # Switch to your team branch
git pull origin frontend-dev    # Get latest changes
git checkout -b feature/task-name
# Make your changes
git add .
git commit -m "Description of changes"
git push origin feature/task-name
# Create Pull Request on GitHub
```

---

## 📊 Database Schema

```sql
-- Products Table
products (id, name, description, price, stock, category, image_path, active, created_at)

-- Admin Users Table
admin_users (id, username, password_hash, created_at)

-- Orders Table
orders (id, product_id, customer_name, customer_email, customer_phone, quantity, status, created_at)
```

---

## 🔐 Security Features
- PDO prepared statements (SQL injection prevention)
- Password hashing (bcrypt)
- Input validation and sanitization
- Session-based authentication

---

## 📝 License
This project is for educational purposes as part of a web development course.


## 📞 Contact
For questions or issues, contact the team lead: [[Facebook](https://www.facebook.com/francisgabriel.austria/)]
