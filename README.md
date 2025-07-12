# ItemHunt - Lost and Found System

A PHP-based web application for managing lost and found items with user authentication and database integration.

## Features

- User registration and authentication
- Item upload with image support
- Item browsing by categories
- Item claiming system
- User account management
- Responsive design

## Prerequisites

- XAMPP (Apache + MySQL + PHP)
- MySQL database
- Web browser

## Installation

### 1. Database Setup

1. Start XAMPP and ensure Apache and MySQL services are running
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Create a new database named `lost_and_found_project`
4. Import the database schema:

```sql
CREATE DATABASE lost_and_found_project;
USE lost_and_found_project;

-- CREATE USER TABLE 
CREATE TABLE User(
    User_ID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password_hash VARCHAR(255) NOT NULL,
    Phone_Number VARCHAR(15) NOT NULL UNIQUE,
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CREATE ITEMS TABLE 
CREATE TABLE Items(
    Item_ID INT PRIMARY KEY AUTO_INCREMENT,
    Item_Name VARCHAR(200) NOT NULL,
    Description TEXT,
    Location VARCHAR (150),
    Date_found DATE NOT NULL ,
    Image_url VARCHAR (255),
    Found_by_id INT NOT NULL,
    Is_claimed BOOLEAN DEFAULT FALSE,
    Created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (found_by_id) REFERENCES User(User_ID) ON DELETE CASCADE
);

-- CREATE CLAIM TABLE
CREATE TABLE Claims(
    Claim_ID INT PRIMARY KEY AUTO_INCREMENT,
    Item_ID INT NOT NULL,
    Claimed_by_id INT NOT NULL,
    Claim_message TEXT,
    Status ENUM('pending' , 'approved', 'rejected') DEFAULT 'pending',
    Created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (claimed_by_id) REFERENCES User(User_ID) ON DELETE CASCADE
);

-- CREATE CLAIM VERIFICATION TABLE 
CREATE TABLE claim_verifications(
    Verification_ID INT PRIMARY KEY AUTO_INCREMENT,
    Claim_ID INT NOT NULL,
    Verified_by INT NOT NULL,
    Verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Remarks TEXT,
    FOREIGN KEY (Claim_ID) REFERENCES Claims(Claim_ID) ON DELETE CASCADE,
    FOREIGN KEY (Verified_by) REFERENCES User(User_ID) ON DELETE CASCADE
);
```

### 2. Project Setup

1. Place the project files in your XAMPP htdocs directory: `C:\xampp\htdocs\ItemHunt\`
2. Update database configuration in `config/database.php` if needed:
   - Host: 127.0.0.1
   - Port: 3307
   - Username: root
   - Password: (empty)
   - Database: lost_and_found_project

### 3. Test Database Connection

1. Open your browser and navigate to: `http://localhost/ItemHunt/test_db.php`
2. Verify that all database tables exist and connection is successful

## Usage

### Starting the Application

1. Ensure XAMPP services are running
2. Open your browser and go to: `http://localhost/ItemHunt/`
3. You will be redirected to the login page if not authenticated

### User Registration

1. Navigate to: `http://localhost/ItemHunt/signup/`
2. Fill in the registration form with:
   - Username
   - Email address
   - Phone number
   - Password (minimum 6 characters)
3. Submit the form to create your account

### User Login

1. Navigate to: `http://localhost/ItemHunt/login/`
2. Enter your username/email and password
3. Upon successful login, you'll be redirected to the main application

### Using the Application

1. **Categories Page**: Browse all items or filter by category
2. **Upload Items**: Click "Upload Item" to add new lost/found items
3. **Claim Items**: Click "Claim Item" on available items
4. **Account Settings**: Manage your profile information
5. **Logout**: Use the logout link in the sidebar

## File Structure

```
ItemHunt/
├── api/                    # API endpoints
│   ├── upload_item.php    # Handle item uploads
│   ├── get_items.php      # Retrieve items
│   └── claim_item.php     # Handle item claims
├── config/
│   └── database.php       # Database configuration
├── includes/
│   └── auth.php          # Authentication functions
├── categories/
│   ├── content.php       # Categories page content
│   ├── script.js         # Categories JavaScript
│   └── styles.css        # Categories styles
├── login/
│   ├── index.php         # Login page
│   ├── script.js         # Login JavaScript
│   └── styles.css        # Login styles
├── signup/
│   ├── index.php         # Signup page
│   ├── script.js         # Signup JavaScript
│   └── style.css         # Signup styles
├── uploads/              # Uploaded images (auto-created)
├── images/               # Static images
├── styles/               # Global styles
├── js/                   # Global JavaScript
├── index.php             # Main application page
├── logout.php            # Logout handler
├── test_db.php           # Database test page
└── README.md             # This file
```

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Input validation and sanitization
- File upload security (type and size validation)

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Verify XAMPP MySQL service is running
   - Check database credentials in `config/database.php`
   - Ensure database exists and tables are created

2. **Upload Directory Not Found**
   - The `uploads/` directory will be created automatically
   - Ensure PHP has write permissions to the project directory

3. **Session Issues**
   - Clear browser cookies and cache
   - Restart Apache service in XAMPP

4. **Image Upload Fails**
   - Check file size (should be reasonable)
   - Verify file type (JPG, PNG, GIF only)
   - Ensure uploads directory has proper permissions

### Testing

Run the database test: `http://localhost/ItemHunt/test_db.php`

## Development

### Adding New Features

1. Create new PHP files in appropriate directories
2. Update database schema if needed
3. Add corresponding CSS/JS files
4. Test thoroughly before deployment

### Database Modifications

If you need to modify the database schema:
1. Update the SQL in this README
2. Modify relevant PHP files
3. Test all functionality

## License

This project is for educational purposes. Feel free to modify and use as needed.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Verify database connection with test_db.php
3. Review error logs in XAMPP 