# Aishley India Journeys - Tourism Website

A modern, responsive India Tourism website built with Core PHP, MySQL, HTML5, CSS3, Bootstrap 5, JavaScript, and jQuery.

## Features

### Frontend
- **Home Page** with Featured Tours, Popular Destinations, Why Choose Us, Testimonials, Latest Blogs, Call to Action, Newsletter, and Footer
- **Tour Packages** page with filtering and pagination
- **Tour Details** page with complete tour information, itinerary, inclusions, exclusions
- **Destinations** page showcasing all destinations
- **Blog** section with categories and blog details
- **Gallery** with lightbox functionality
- **Testimonials** page
- **FAQs** page with accordion
- **Contact** page with enquiry form
- **User Registration and Login**
- **User Dashboard** with profile management, favorite tours, and enquiry history
- **Dynamic Pages** via CMS
- **404 Error Page**
- **SEO Features**: XML Sitemap, robots.txt, Schema Markup, Open Graph tags

### Admin Panel
- **Secure Login** with authentication
- **Dashboard** with statistics and recent enquiries
- **Tour Management**: Create, Edit, Delete tours with rich content
- **Destination Management**: Manage destinations with images
- **Blog Management**: Full blog system with categories and rich text editor
- **Page Management**: CMS for creating/editing dynamic pages
- **Gallery Management**: Upload and manage gallery images
- **Testimonial Management**: Manage customer testimonials
- **FAQ Management**: Manage frequently asked questions
- **Enquiry Management**: View and manage customer enquiries with status updates
- **Media Library**: Centralized image management
- **Settings**: Configure website settings, SEO, social media, contact info

### User Panel
- **Dashboard** with overview
- **Profile Management**: Update personal information
- **Favorite Tours**: Save and manage favorite tours
- **Enquiry History**: View past enquiries

### Security Features
- PDO prepared statements for SQL injection prevention
- Password hashing for secure authentication
- Session management
- Input sanitization
- File upload validation

## Tech Stack

- **Backend**: Core PHP
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **JavaScript**: JavaScript, jQuery
- **Rich Text Editor**: CKEditor 5
- **Icons**: Font Awesome 6

## Installation Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP (for local development)

### Step 1: Extract Files
Extract the project files to your web server directory:
```
C:\xampp\htdocs\Aishley\
```

### Step 2: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `aishley_tourism`
3. Import the `database.sql` file located in the project root
4. This will create all required tables and insert sample data

### Step 3: Configure Database Connection
Edit `config/database.php` and update the database credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'aishley_tourism');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 4: Configure Site URL
Edit `config/database.php` and update the site URL:
```php
define('SITE_URL', 'http://localhost/Aishley');
```

### Step 5: Set File Permissions
Ensure the following directories have write permissions:
- `uploads/`
- `uploads/tours/`
- `uploads/destinations/`
- `uploads/blogs/`
- `uploads/gallery/`
- `uploads/pages/`
- `uploads/users/`

### Step 6: Access the Website
- **Frontend**: http://localhost/Aishley
- **Admin Panel**: http://localhost/Aishley/admin/login.php
- **User Panel**: http://localhost/Aishley/user/dashboard.php

### Default Login Credentials

**Admin Panel:**
- Email: admin@aishley.com
- Password: admin123

**User Registration:**
- Register a new account at http://localhost/Aishley/register.php

## Project Structure

```
Aishley/
├── admin/                  # Admin panel pages
│   ├── login.php
│   ├── dashboard.php
│   ├── tours.php
│   ├── tours-create.php
│   ├── tours-edit.php
│   ├── destinations.php
│   ├── destinations-create.php
│   ├── destinations-edit.php
│   ├── blogs.php
│   ├── blogs-create.php
│   ├── blogs-edit.php
│   ├── pages.php
│   ├── pages-create.php
│   ├── pages-edit.php
│   ├── gallery.php
│   ├── testimonials.php
│   ├── faqs.php
│   ├── enquiries.php
│   ├── media.php
│   └── settings.php
├── user/                   # User panel pages
│   ├── dashboard.php
│   ├── profile.php
│   ├── favorites.php
│   └── enquiries.php
├── config/                 # Configuration files
│   ├── database.php
│   └── functions.php
├── controllers/            # Controllers
│   ├── AuthController.php
│   ├── AdminController.php
│   └── EnquiryController.php
├── models/                 # Models
│   ├── Auth.php
│   └── Admin.php
├── assets/                 # Static assets
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
├── uploads/                # File uploads
│   ├── tours/
│   ├── destinations/
│   ├── blogs/
│   ├── gallery/
│   ├── pages/
│   └── users/
├── includes/               # Include files
├── index.php              # Homepage
├── tours.php              # Tours listing
├── tour-details.php       # Tour details
├── destinations.php       # Destinations
├── blogs.php              # Blog listing
├── blog-details.php       # Blog details
├── gallery.php            # Gallery
├── testimonials.php       # Testimonials
├── faq.php                # FAQs
├── contact.php            # Contact
├── page.php               # Dynamic pages (CMS)
├── login.php              # User login
├── register.php           # User registration
├── 404.php                # 404 error page
├── sitemap.php            # XML sitemap
├── robots.txt             # Robots file
└── database.sql           # Database schema

```

## Database Schema

The project includes the following tables:
- `users` - User accounts
- `admins` - Admin accounts
- `destinations` - Tour destinations
- `tours` - Tour packages
- `tour_gallery` - Tour images
- `blog_categories` - Blog categories
- `blogs` - Blog posts
- `pages` - CMS pages
- `gallery` - Photo gallery
- `testimonials` - Customer testimonials
- `faqs` - Frequently asked questions
- `enquiries` - Customer enquiries
- `user_favorites` - User favorite tours
- `settings` - Website settings
- `media_library` - Media management

## Customization

### Update Site Settings
1. Login to Admin Panel
2. Go to Settings
3. Update site name, tagline, contact info, social media, SEO settings

### Add New Tours
1. Login to Admin Panel
2. Go to Tours
3. Click "Add Tour"
4. Fill in tour details and upload images

### Create Blog Posts
1. Login to Admin Panel
2. Go to Blogs
3. Click "Add Blog"
4. Use the rich text editor for content

### Manage Pages via CMS
1. Login to Admin Panel
2. Go to Pages
3. Create/edit pages dynamically

## SEO Features

- **XML Sitemap**: Automatically generated at `/sitemap.php`
- **Robots.txt**: Configured at `/robots.txt`
- **Meta Tags**: Dynamic meta titles and descriptions
- **Open Graph**: Social media sharing tags
- **Clean URLs**: SEO-friendly URL structure

## Security Notes

- Change default admin password immediately after installation
- Use strong passwords for all accounts
- Keep PHP and MySQL updated
- Use HTTPS in production
- Regularly backup your database
- Restrict file upload types and sizes in production

## Support

For issues or questions, please contact the development team.

## License

This project is proprietary software. All rights reserved.

---

**Aishley India Journeys** - Experience the Magic of India
