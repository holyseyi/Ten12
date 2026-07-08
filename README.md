# Ten12 Portfolio Website

A modern, responsive portfolio website for the Ten12 brand, built with PHP backend and vanilla JavaScript frontend. Features a clean, professional design with strong contrast and smooth user experience.

## Features

### Frontend
- **Modern, Clean Design**: Minimalist UI with strong contrast (black/white + red accent)
- **Responsive Layout**: Mobile-first design that works on all devices
- **Smooth Animations**: Hover effects, transitions, and scroll animations
- **Project Showcase**: Grid-based project display with filtering and search
- **Project Detail Pages**: Image galleries and comprehensive project information
- **Contact Form**: Functional contact form with validation
- **SEO Optimized**: Semantic HTML and meta tags

### Backend
- **PHP REST API**: Clean, organized API structure
- **MySQL Database**: Secure data storage with PDO
- **Admin Dashboard**: Complete project management system
- **Authentication**: JWT-based admin authentication
- **Image Upload**: Secure file handling with validation
- **CRUD Operations**: Full create, read, update, delete functionality

### Technical Stack
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), PHP includes
- **Backend**: PHP 8+, MySQL/MariaDB, PDO
- **Styling**: Custom CSS with CSS variables
- **Typography**: Inter font family
- **Icons**: Inline SVG icons
- **Security**: Prepared statements, input validation, JWT tokens

## Project Structure

```
Ten12/
├── backend/
│   ├── api/
│   │   ├── projects.php          # Project CRUD API
│   │   ├── contact.php           # Contact form API
│   │   ├── upload.php            # Image upload API
│   │   └── admin/
│   │       └── auth.php          # Admin authentication
│   ├── config/
│   │   └── database.php          # Database configuration
│   ├── models/
│   │   ├── Project.php           # Project model
│   │   └── Contact.php           # Contact model
│   ├── utils/
│   │   └── auth.php              # Authentication utilities
│   ├── uploads/                  # Uploaded images
│   └── setup_database.sql        # Database setup script
├── frontend/
│   ├── includes/
│   │   ├── header.php            # Header include
│   │   └── footer.php            # Footer include
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css         # Main stylesheet
│   │   ├── js/
│   │   │   └── main.js           # Main JavaScript
│   │   └── images/               # Static images
│   ├── index.php                 # Homepage
│   ├── projects.php              # Projects listing
│   └── project-detail.php        # Project details
├── admin/
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin.css         # Admin styles
│   │   └── js/
│   │       └── admin.js          # Admin JavaScript
│   ├── login.html                # Admin login
│   ├── dashboard.html            # Admin dashboard
│   └── add-project.html          # Add/edit project
└── README.md                     # This file
```

## Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL/MariaDB 5.7 or higher
- Web server (Apache, Nginx, or PHP built-in server)
- File write permissions for uploads directory

### Step 1: Database Setup

1. Create a new database:
   ```sql
   CREATE DATABASE ten12_portfolio;
   ```

2. Import the database structure:
   ```bash
   mysql -u username -p ten12_portfolio < backend/setup_database.sql
   ```

3. Update database credentials in `backend/config/database.php`:
   ```php
   private $host = 'localhost';
   private $db_name = 'ten12_portfolio';
   private $username = 'your_db_username';
   private $password = 'your_db_password';
   ```

### Step 2: Configure Web Server

#### Option A: Apache
1. Place the project in your web root (e.g., `/var/www/html/ten12/`)
2. Ensure `mod_rewrite` is enabled
3. Create `.htaccess` file if needed for clean URLs

#### Option B: Nginx
1. Configure your server block to point to the project directory
2. Set up PHP-FPM handling
3. Configure URL rewriting if desired

#### Option C: PHP Built-in Server (for development)
```bash
cd frontend
php -S localhost:8000
```

### Render Deployment
Render can run this PHP app as a Web Service using the provided `render.yaml`.

1. Connect your repository to Render and create a new Web Service.
2. Use the start command:
   ```bash
   php -S 0.0.0.0:$PORT
   ```
3. Set the `DATABASE_URL` environment variable in Render to a supported database connection string.
   - For PostgreSQL: `postgres://user:pass@host:port/dbname`
   - For MySQL: `mysql://user:pass@host:port/dbname`
4. If you do not set `DATABASE_URL`, the app will fall back to file-based SQLite at `/data/app.db`.
   - Note: Render file storage is ephemeral unless you attach a persistent disk.

> On Render, persistent production data should use a managed database and `DATABASE_URL`.

```

### Step 3: Set Permissions

Ensure the uploads directory is writable:
```bash
chmod 755 backend/uploads
chmod 664 backend/uploads/*
```

### Step 4: Admin Access

1. Navigate to `admin/login.html`
2. Default credentials:
   - Username: `admin`
   - Password: `password`
3. **Important**: Change the default password after first login

## Usage

### Managing Projects

1. **Add New Project**:
   - Login to admin dashboard
   - Click "Add New Project"
   - Fill in project details
   - Upload thumbnail image
   - Set status (Published/Draft)
   - Click "Create Project"

2. **Edit Existing Project**:
   - Go to admin dashboard
   - Click "Edit" on any project
   - Modify details as needed
   - Click "Update Project"

3. **Delete Project**:
   - Click "Delete" on any project
   - Confirm deletion action

### Frontend Pages

- **Homepage** (`index.php`): Hero section, featured projects, about, contact
- **Projects** (`projects.php`): All projects with search and filtering
- **Project Detail** (`project-detail.php?id=X`): Individual project view

### API Endpoints

- `GET /backend/api/projects.php` - Get all projects
- `GET /backend/api/projects.php?id=X` - Get specific project
- `POST /backend/api/projects.php` - Create project (admin only)
- `PUT /backend/api/projects.php?id=X` - Update project (admin only)
- `DELETE /backend/api/projects.php?id=X` - Delete project (admin only)
- `POST /backend/api/contact.php` - Submit contact form
- `POST /backend/api/upload.php` - Upload image (admin only)
- `POST /backend/api/admin/auth.php` - Admin login

## Customization

### Colors and Styling

Edit CSS variables in `frontend/assets/css/style.css`:
```css
:root {
    --primary-color: #dc2626;        /* Red accent */
    --text-primary: #111827;         /* Dark text */
    --text-secondary: #6b7280;       /* Light text */
    --bg-primary: #ffffff;           /* White background */
    --bg-secondary: #f9fafb;         /* Light gray */
}
```

### Typography

The site uses the Inter font family. You can change this in the header includes:
```php
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
```

### Logo and Branding

Update the Ten12 logo and branding in:
- Header includes (`frontend/includes/header.php`)
- Admin header (`admin/dashboard.html`)
- CSS styles for the logo

## Security Considerations

1. **Database Security**: Uses PDO with prepared statements
2. **Input Validation**: All inputs are sanitized
3. **File Upload**: Validates file types and sizes
4. **Authentication**: JWT tokens with expiration
5. **XSS Prevention**: Output escaping with htmlspecialchars()

## Performance Optimization

1. **Image Optimization**: Compress uploaded images
2. **Caching**: Consider adding browser caching headers
3. **Minification**: Minify CSS and JS files for production
4. **Lazy Loading**: Images use lazy loading attribute
5. **Database Indexing**: Add indexes to frequently queried columns

## Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check database credentials in `backend/config/database.php`
   - Ensure database server is running
   - Verify database name and permissions

2. **Image Upload Fails**:
   - Check uploads directory permissions
   - Verify PHP upload limits in `php.ini`
   - Ensure file type validation is working

3. **Admin Login Issues**:
   - Clear browser cookies/localStorage
   - Check JWT token configuration
   - Verify admin user exists in database

4. **404 Errors**:
   - Check file paths and permissions
   - Verify web server configuration
   - Ensure PHP files are being processed

### Debug Mode

To enable debug mode, add this to the top of your PHP files:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

**Remember to disable debug mode in production!**

## Deployment

### Production Checklist

1. **Security**:
   - Change default admin password
   - Set proper file permissions
   - Disable debug mode
   - Use HTTPS

2. **Performance**:
   - Enable gzip compression
   - Set up browser caching
   - Optimize images
   - Minimize CSS/JS

3. **SEO**:
   - Add meta descriptions
   - Set up sitemap
   - Configure robots.txt
   - Submit to search engines

## Support

For issues and questions:
1. Check the troubleshooting section above
2. Review the code comments for additional context
3. Test API endpoints using tools like Postman
4. Verify database structure matches the setup script

## License

This project is open source and available under the [MIT License](LICENSE).

---

**Ten12 Portfolio** - Modern. Memorable. Ten12.
