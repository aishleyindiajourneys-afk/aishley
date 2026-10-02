<?php
/**
 * Admin Controller
 * Aishley India Journeys
 */

require_once '../config/database.php';
require_once '../config/functions.php';
require_once '../models/Admin.php';

// Check admin authentication
if (!isAdminLoggedIn()) {
    setFlash('error', 'Please login to access admin panel');
    redirect(SITE_URL . '/admin/login.php');
}

$admin = new Admin($pdo);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'tour-create':
        handleTourCreate($admin);
        break;
    case 'tour-update':
        handleTourUpdate($admin);
        break;
    case 'tour-delete':
        handleTourDelete($admin);
        break;
    case 'destination-create':
        handleDestinationCreate($admin);
        break;
    case 'destination-update':
        handleDestinationUpdate($admin);
        break;
    case 'destination-delete':
        handleDestinationDelete($admin);
        break;
    case 'blog-create':
        handleBlogCreate($admin);
        break;
    case 'blog-update':
        handleBlogUpdate($admin);
        break;
    case 'blog-delete':
        handleBlogDelete($admin);
        break;
    case 'page-create':
        handlePageCreate($admin);
        break;
    case 'page-update':
        handlePageUpdate($admin);
        break;
    case 'page-delete':
        handlePageDelete($admin);
        break;
    case 'gallery-create':
        handleGalleryCreate($admin);
        break;
    case 'gallery-update':
        handleGalleryUpdate($admin);
        break;
    case 'gallery-delete':
        handleGalleryDelete($admin);
        break;
    case 'slider-create':
        handleSliderCreate($admin);
        break;
    case 'slider-update':
        handleSliderUpdate($admin);
        break;
    case 'slider-delete':
        handleSliderDelete($admin);
        break;
    case 'testimonial-create':
        handleTestimonialCreate($admin);
        break;
    case 'testimonial-update':
        handleTestimonialUpdate($admin);
        break;
    case 'testimonial-delete':
        handleTestimonialDelete($admin);
        break;
    case 'faq-create':
        handleFAQCreate($admin);
        break;
    case 'faq-update':
        handleFAQUpdate($admin);
        break;
    case 'faq-delete':
        handleFAQDelete($admin);
        break;
    case 'enquiry-update':
        handleEnquiryUpdate($admin);
        break;
    case 'enquiry-delete':
        handleEnquiryDelete($admin);
        break;
    case 'settings-update':
        handleSettingsUpdate($admin);
        break;
    case 'media-upload':
        handleMediaUpload($admin);
        break;
    case 'media-delete':
        handleMediaDelete($admin);
        break;
    default:
        redirect(SITE_URL . '/admin/dashboard.php');
}

// ==================== TOUR HANDLERS ====================

function handleTourCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/tours-create.php');
    }
    
    $data = [
        'title' => sanitize($_POST['title']),
        'slug' => generateUniqueSlug('tours', $_POST['title']),
        'destination_id' => $_POST['destination_id'] ?: null,
        'duration' => sanitize($_POST['duration']),
        'price' => $_POST['price'],
        'discount_price' => $_POST['discount_price'] ?: null,
        'overview' => $_POST['overview'],
        'highlights' => $_POST['highlights'],
        'itinerary' => $_POST['itinerary'],
        'inclusions' => $_POST['inclusions'],
        'exclusions' => $_POST['exclusions'],
        'featured' => $_POST['featured'] ?? 'no',
        'popular' => $_POST['popular'] ?? 'no',
        'status' => $_POST['status'] ?? 'active',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle featured image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['featured_image'], UPLOAD_PATH . 'tours/');
        if ($uploadResult['success']) {
            $data['featured_image'] = 'tours/' . $uploadResult['filename'];
        }
    }
    
    $tourId = $admin->createTour($data);
    
    if ($tourId) {
        setFlash('success', 'Tour created successfully');
        redirect(SITE_URL . '/admin/tours.php');
    } else {
        setFlash('error', 'Failed to create tour');
        redirect(SITE_URL . '/admin/tours-create.php');
    }
}

function handleTourUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/tours.php');
    }
    
    $id = $_POST['id'];
    $existingTour = $admin->getTourById($id);
    
    $data = [
        'title' => sanitize($_POST['title']),
        'slug' => generateUniqueSlug('tours', $_POST['title'], $id),
        'destination_id' => $_POST['destination_id'] ?: null,
        'duration' => sanitize($_POST['duration']),
        'price' => $_POST['price'],
        'discount_price' => $_POST['discount_price'] ?: null,
        'overview' => $_POST['overview'],
        'highlights' => $_POST['highlights'],
        'itinerary' => $_POST['itinerary'],
        'inclusions' => $_POST['inclusions'],
        'exclusions' => $_POST['exclusions'],
        'featured' => $_POST['featured'] ?? 'no',
        'popular' => $_POST['popular'] ?? 'no',
        'status' => $_POST['status'] ?? 'active',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle featured image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['featured_image'], UPLOAD_PATH . 'tours/');
        if ($uploadResult['success']) {
            // Delete old image
            if ($existingTour['featured_image']) {
                deleteFile(UPLOAD_PATH . $existingTour['featured_image']);
            }
            $data['featured_image'] = 'tours/' . $uploadResult['filename'];
        }
    } else {
        $data['featured_image'] = $existingTour['featured_image'];
    }
    
    if ($admin->updateTour($id, $data)) {
        setFlash('success', 'Tour updated successfully');
        redirect(SITE_URL . '/admin/tours.php');
    } else {
        setFlash('error', 'Failed to update tour');
        redirect(SITE_URL . '/admin/tours-edit.php?id=' . $id);
    }
}

function handleTourDelete($admin) {
    $id = $_GET['id'] ?? 0;
    $tour = $admin->getTourById($id);
    
    if ($tour) {
        // Delete image
        if ($tour['featured_image']) {
            deleteFile(UPLOAD_PATH . $tour['featured_image']);
        }
        $admin->deleteTour($id);
        setFlash('success', 'Tour deleted successfully');
    } else {
        setFlash('error', 'Tour not found');
    }
    
    redirect(SITE_URL . '/admin/tours.php');
}

// ==================== DESTINATION HANDLERS ====================

function handleDestinationCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/destinations-create.php');
    }
    
    $data = [
        'name' => sanitize($_POST['name']),
        'slug' => generateUniqueSlug('destinations', $_POST['name']),
        'description' => $_POST['description'],
        'state' => sanitize($_POST['state']),
        'country' => sanitize($_POST['country'] ?? 'India'),
        'featured' => $_POST['featured'] ?? 'no',
        'popular' => $_POST['popular'] ?? 'no',
        'status' => $_POST['status'] ?? 'active',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'destinations/');
        if ($uploadResult['success']) {
            $data['image'] = 'destinations/' . $uploadResult['filename'];
        }
    }
    
    // Handle banner image
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['banner_image'], UPLOAD_PATH . 'destinations/');
        if ($uploadResult['success']) {
            $data['banner_image'] = 'destinations/' . $uploadResult['filename'];
        }
    }
    
    if ($admin->createDestination($data)) {
        setFlash('success', 'Destination created successfully');
        redirect(SITE_URL . '/admin/destinations.php');
    } else {
        setFlash('error', 'Failed to create destination');
        redirect(SITE_URL . '/admin/destinations-create.php');
    }
}

function handleDestinationUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/destinations.php');
    }
    
    $id = $_POST['id'];
    $existingDestination = $admin->getDestinationById($id);
    
    $data = [
        'name' => sanitize($_POST['name']),
        'slug' => generateUniqueSlug('destinations', $_POST['name'], $id),
        'description' => $_POST['description'],
        'state' => sanitize($_POST['state']),
        'country' => sanitize($_POST['country'] ?? 'India'),
        'featured' => $_POST['featured'] ?? 'no',
        'popular' => $_POST['popular'] ?? 'no',
        'status' => $_POST['status'] ?? 'active',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'destinations/');
        if ($uploadResult['success']) {
            if ($existingDestination['image']) {
                deleteFile(UPLOAD_PATH . $existingDestination['image']);
            }
            $data['image'] = 'destinations/' . $uploadResult['filename'];
        }
    } else {
        $data['image'] = $existingDestination['image'];
    }
    
    // Handle banner image
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['banner_image'], UPLOAD_PATH . 'destinations/');
        if ($uploadResult['success']) {
            if ($existingDestination['banner_image']) {
                deleteFile(UPLOAD_PATH . $existingDestination['banner_image']);
            }
            $data['banner_image'] = 'destinations/' . $uploadResult['filename'];
        }
    } else {
        $data['banner_image'] = $existingDestination['banner_image'];
    }
    
    if ($admin->updateDestination($id, $data)) {
        setFlash('success', 'Destination updated successfully');
        redirect(SITE_URL . '/admin/destinations.php');
    } else {
        setFlash('error', 'Failed to update destination');
        redirect(SITE_URL . '/admin/destinations-edit.php?id=' . $id);
    }
}

function handleDestinationDelete($admin) {
    $id = $_GET['id'] ?? 0;
    $destination = $admin->getDestinationById($id);
    
    if ($destination) {
        if ($destination['image']) {
            deleteFile(UPLOAD_PATH . $destination['image']);
        }
        if ($destination['banner_image']) {
            deleteFile(UPLOAD_PATH . $destination['banner_image']);
        }
        $admin->deleteDestination($id);
        setFlash('success', 'Destination deleted successfully');
    } else {
        setFlash('error', 'Destination not found');
    }
    
    redirect(SITE_URL . '/admin/destinations.php');
}

// ==================== BLOG HANDLERS ====================

function handleBlogCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/blogs-create.php');
    }
    
    $data = [
        'category_id' => $_POST['category_id'] ?: null,
        'title' => sanitize($_POST['title']),
        'slug' => generateUniqueSlug('blogs', $_POST['title']),
        'excerpt' => $_POST['excerpt'],
        'content' => $_POST['content'],
        'author' => sanitize($_POST['author']),
        'status' => $_POST['status'] ?? 'draft',
        'featured' => $_POST['featured'] ?? 'no',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle featured image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['featured_image'], UPLOAD_PATH . 'blogs/');
        if ($uploadResult['success']) {
            $data['featured_image'] = 'blogs/' . $uploadResult['filename'];
        }
    }
    
    if ($admin->createBlog($data)) {
        setFlash('success', 'Blog created successfully');
        redirect(SITE_URL . '/admin/blogs.php');
    } else {
        setFlash('error', 'Failed to create blog');
        redirect(SITE_URL . '/admin/blogs-create.php');
    }
}

function handleBlogUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/blogs.php');
    }
    
    $id = $_POST['id'];
    $existingBlog = $admin->getBlogById($id);
    
    $data = [
        'category_id' => $_POST['category_id'] ?: null,
        'title' => sanitize($_POST['title']),
        'slug' => generateUniqueSlug('blogs', $_POST['title'], $id),
        'excerpt' => $_POST['excerpt'],
        'content' => $_POST['content'],
        'author' => sanitize($_POST['author']),
        'status' => $_POST['status'] ?? 'draft',
        'featured' => $_POST['featured'] ?? 'no',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle featured image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['featured_image'], UPLOAD_PATH . 'blogs/');
        if ($uploadResult['success']) {
            if ($existingBlog['featured_image']) {
                deleteFile(UPLOAD_PATH . $existingBlog['featured_image']);
            }
            $data['featured_image'] = 'blogs/' . $uploadResult['filename'];
        }
    } else {
        $data['featured_image'] = $existingBlog['featured_image'];
    }
    
    if ($admin->updateBlog($id, $data)) {
        setFlash('success', 'Blog updated successfully');
        redirect(SITE_URL . '/admin/blogs.php');
    } else {
        setFlash('error', 'Failed to update blog');
        redirect(SITE_URL . '/admin/blogs-edit.php?id=' . $id);
    }
}

function handleBlogDelete($admin) {
    $id = $_GET['id'] ?? 0;
    $blog = $admin->getBlogById($id);
    
    if ($blog) {
        if ($blog['featured_image']) {
            deleteFile(UPLOAD_PATH . $blog['featured_image']);
        }
        $admin->deleteBlog($id);
        setFlash('success', 'Blog deleted successfully');
    } else {
        setFlash('error', 'Blog not found');
    }
    
    redirect(SITE_URL . '/admin/blogs.php');
}

// ==================== PAGE HANDLERS ====================

function handlePageCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/pages-create.php');
    }
    
    $data = [
        'title' => sanitize($_POST['title']),
        'slug' => generateUniqueSlug('pages', $_POST['title']),
        'content' => $_POST['content'],
        'status' => $_POST['status'] ?? 'active',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle featured image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['featured_image'], UPLOAD_PATH . 'pages/');
        if ($uploadResult['success']) {
            $data['featured_image'] = 'pages/' . $uploadResult['filename'];
        }
    }
    
    if ($admin->createPage($data)) {
        setFlash('success', 'Page created successfully');
        redirect(SITE_URL . '/admin/pages.php');
    } else {
        setFlash('error', 'Failed to create page');
        redirect(SITE_URL . '/admin/pages-create.php');
    }
}

function handlePageUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/pages.php');
    }
    
    $id = $_POST['id'];
    $existingPage = $admin->getPageById($id);
    
    $data = [
        'title' => sanitize($_POST['title']),
        'slug' => generateUniqueSlug('pages', $_POST['title'], $id),
        'content' => $_POST['content'],
        'status' => $_POST['status'] ?? 'active',
        'meta_title' => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? '')
    ];
    
    // Handle featured image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['featured_image'], UPLOAD_PATH . 'pages/');
        if ($uploadResult['success']) {
            if ($existingPage['featured_image']) {
                deleteFile(UPLOAD_PATH . $existingPage['featured_image']);
            }
            $data['featured_image'] = 'pages/' . $uploadResult['filename'];
        }
    } else {
        $data['featured_image'] = $existingPage['featured_image'];
    }
    
    if ($admin->updatePage($id, $data)) {
        setFlash('success', 'Page updated successfully');
        redirect(SITE_URL . '/admin/pages.php');
    } else {
        setFlash('error', 'Failed to update page');
        redirect(SITE_URL . '/admin/pages-edit.php?id=' . $id);
    }
}

function handlePageDelete($admin) {
    $id = $_GET['id'] ?? 0;
    $page = $admin->getPageById($id);
    
    if ($page) {
        if ($page['featured_image']) {
            deleteFile(UPLOAD_PATH . $page['featured_image']);
        }
        $admin->deletePage($id);
        setFlash('success', 'Page deleted successfully');
    } else {
        setFlash('error', 'Page not found');
    }
    
    redirect(SITE_URL . '/admin/pages.php');
}

// ==================== GALLERY HANDLERS ====================

function handleGalleryCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/gallery.php');
    }
    
    $data = [
        'title' => sanitize($_POST['title']),
        'category' => sanitize($_POST['category']),
        'description' => $_POST['description'],
        'status' => $_POST['status'] ?? 'active',
        'sort_order' => $_POST['sort_order'] ?? 0
    ];
    
    // Handle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'gallery/');
        if ($uploadResult['success']) {
            $data['image'] = 'gallery/' . $uploadResult['filename'];
        }
    }
    
    if (empty($data['image'])) {
        setFlash('error', 'Please upload a gallery image');
        redirect(SITE_URL . '/admin/gallery.php');
    }
    
    if ($admin->createGalleryItem($data)) {
        setFlash('success', 'Gallery item added successfully');
    } else {
        setFlash('error', 'Failed to add gallery item');
    }
    
    redirect(SITE_URL . '/admin/gallery.php');
}

function handleGalleryUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/gallery.php');
    }
    
    $id = $_POST['id'];
    $existingItem = $admin->getGalleryById($id);
    if (!$existingItem) {
        setFlash('error', 'Gallery item not found');
        redirect(SITE_URL . '/admin/gallery.php');
    }
    
    $data = [
        'title' => sanitize($_POST['title']),
        'category' => sanitize($_POST['category']),
        'description' => $_POST['description'],
        'status' => $_POST['status'] ?? 'active',
        'sort_order' => $_POST['sort_order'] ?? 0
    ];
    
    // Handle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'gallery/');
        if ($uploadResult['success']) {
            if ($existingItem['image']) {
                deleteFile(UPLOAD_PATH . $existingItem['image']);
            }
            $data['image'] = 'gallery/' . $uploadResult['filename'];
        }
    } else {
        $data['image'] = $existingItem['image'];
    }
    
    if ($admin->updateGalleryItem($id, $data)) {
        setFlash('success', 'Gallery item updated successfully');
    } else {
        setFlash('error', 'Failed to update gallery item');
    }
    
    redirect(SITE_URL . '/admin/gallery.php');
}

function handleGalleryDelete($admin) {
    $id = $_GET['id'] ?? 0;
    $item = $admin->getGalleryById($id);
    
    if ($item) {
        if ($item['image']) {
            deleteFile(UPLOAD_PATH . $item['image']);
        }
        $admin->deleteGalleryItem($id);
        setFlash('success', 'Gallery item deleted successfully');
    } else {
        setFlash('error', 'Gallery item not found');
    }
    
    redirect(SITE_URL . '/admin/gallery.php');
}

// ==================== HERO SLIDER HANDLERS ====================

function handleSliderCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/slider.php');
    }
    
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        setFlash('error', 'Please upload a slider image');
        redirect(SITE_URL . '/admin/slider.php');
    }
    
    $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'slider/');
    if (!$uploadResult['success']) {
        setFlash('error', $uploadResult['message'] ?? 'Failed to upload image');
        redirect(SITE_URL . '/admin/slider.php');
    }
    
    $data = [
        'title' => sanitize($_POST['title'] ?? ''),
        'subtitle' => sanitize($_POST['subtitle'] ?? ''),
        'image' => 'slider/' . $uploadResult['filename'],
        'button_text' => sanitize($_POST['button_text'] ?? 'Explore Tours') ?: 'Explore Tours',
        'button_link' => sanitize($_POST['button_link'] ?? 'tours.php') ?: 'tours.php',
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active'
    ];
    
    if ($admin->createHeroSlide($data)) {
        setFlash('success', 'Slider image added successfully');
    } else {
        setFlash('error', 'Failed to add slider image');
    }
    
    redirect(SITE_URL . '/admin/slider.php');
}

function handleSliderUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/slider.php');
    }
    
    $id = (int)($_POST['id'] ?? 0);
    $existing = $admin->getHeroSlideById($id);
    if (!$existing) {
        setFlash('error', 'Slider image not found');
        redirect(SITE_URL . '/admin/slider.php');
    }
    
    $data = [
        'title' => sanitize($_POST['title'] ?? ''),
        'subtitle' => sanitize($_POST['subtitle'] ?? ''),
        'image' => $existing['image'],
        'button_text' => sanitize($_POST['button_text'] ?? 'Explore Tours') ?: 'Explore Tours',
        'button_link' => sanitize($_POST['button_link'] ?? 'tours.php') ?: 'tours.php',
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active'
    ];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'slider/');
        if ($uploadResult['success']) {
            if (!empty($existing['image']) && strpos($existing['image'], 'slider/') === 0) {
                deleteFile(UPLOAD_PATH . $existing['image']);
            }
            $data['image'] = 'slider/' . $uploadResult['filename'];
        }
    }
    
    if ($admin->updateHeroSlide($id, $data)) {
        setFlash('success', 'Slider image updated successfully');
    } else {
        setFlash('error', 'Failed to update slider image');
    }
    
    redirect(SITE_URL . '/admin/slider.php');
}

function handleSliderDelete($admin) {
    $id = $_GET['id'] ?? 0;
    $item = $admin->getHeroSlideById($id);
    
    if ($item) {
        if (!empty($item['image']) && strpos($item['image'], 'slider/') === 0) {
            deleteFile(UPLOAD_PATH . $item['image']);
        }
        $admin->deleteHeroSlide($id);
        setFlash('success', 'Slider image deleted successfully');
    } else {
        setFlash('error', 'Slider image not found');
    }
    
    redirect(SITE_URL . '/admin/slider.php');
}

// ==================== TESTIMONIAL HANDLERS ====================

function handleTestimonialCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/testimonials.php');
    }
    
    $data = [
        'name' => sanitize($_POST['name']),
        'email' => sanitize($_POST['email']),
        'location' => sanitize($_POST['location']),
        'tour_id' => $_POST['tour_id'] ?: null,
        'rating' => $_POST['rating'] ?? 5,
        'review' => $_POST['review'],
        'featured' => $_POST['featured'] ?? 'no',
        'status' => $_POST['status'] ?? 'active'
    ];
    
    // Handle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'testimonials/');
        if ($uploadResult['success']) {
            $data['image'] = 'testimonials/' . $uploadResult['filename'];
        }
    }
    
    if ($admin->createTestimonial($data)) {
        setFlash('success', 'Testimonial added successfully');
    } else {
        setFlash('error', 'Failed to add testimonial');
    }
    
    redirect(SITE_URL . '/admin/testimonials.php');
}

function handleTestimonialUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/testimonials.php');
    }
    
    $id = $_POST['id'];
    $existingTestimonial = $admin->getTestimonialById($id);
    
    $data = [
        'name' => sanitize($_POST['name']),
        'email' => sanitize($_POST['email']),
        'location' => sanitize($_POST['location']),
        'tour_id' => $_POST['tour_id'] ?: null,
        'rating' => $_POST['rating'] ?? 5,
        'review' => $_POST['review'],
        'featured' => $_POST['featured'] ?? 'no',
        'status' => $_POST['status'] ?? 'active'
    ];
    
    // Handle image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['image'], UPLOAD_PATH . 'testimonials/');
        if ($uploadResult['success']) {
            if ($existingTestimonial['image']) {
                deleteFile(UPLOAD_PATH . $existingTestimonial['image']);
            }
            $data['image'] = 'testimonials/' . $uploadResult['filename'];
        }
    } else {
        $data['image'] = $existingTestimonial['image'];
    }
    
    if ($admin->updateTestimonial($id, $data)) {
        setFlash('success', 'Testimonial updated successfully');
    } else {
        setFlash('error', 'Failed to update testimonial');
    }
    
    redirect(SITE_URL . '/admin/testimonials.php');
}

function handleTestimonialDelete($admin) {
    $id = $_GET['id'] ?? 0;
    $testimonial = $admin->getTestimonialById($id);
    
    if ($testimonial) {
        if ($testimonial['image']) {
            deleteFile(UPLOAD_PATH . $testimonial['image']);
        }
        $admin->deleteTestimonial($id);
        setFlash('success', 'Testimonial deleted successfully');
    } else {
        setFlash('error', 'Testimonial not found');
    }
    
    redirect(SITE_URL . '/admin/testimonials.php');
}

// ==================== FAQ HANDLERS ====================

function handleFAQCreate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/faqs.php');
    }
    
    $data = [
        'question' => sanitize($_POST['question']),
        'answer' => $_POST['answer'],
        'category' => sanitize($_POST['category']),
        'sort_order' => $_POST['sort_order'] ?? 0,
        'status' => $_POST['status'] ?? 'active'
    ];
    
    if ($admin->createFAQ($data)) {
        setFlash('success', 'FAQ added successfully');
    } else {
        setFlash('error', 'Failed to add FAQ');
    }
    
    redirect(SITE_URL . '/admin/faqs.php');
}

function handleFAQUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/faqs.php');
    }
    
    $id = $_POST['id'];
    
    $data = [
        'question' => sanitize($_POST['question']),
        'answer' => $_POST['answer'],
        'category' => sanitize($_POST['category']),
        'sort_order' => $_POST['sort_order'] ?? 0,
        'status' => $_POST['status'] ?? 'active'
    ];
    
    if ($admin->updateFAQ($id, $data)) {
        setFlash('success', 'FAQ updated successfully');
    } else {
        setFlash('error', 'Failed to update FAQ');
    }
    
    redirect(SITE_URL . '/admin/faqs.php');
}

function handleFAQDelete($admin) {
    $id = $_GET['id'] ?? 0;
    
    if ($admin->deleteFAQ($id)) {
        setFlash('success', 'FAQ deleted successfully');
    } else {
        setFlash('error', 'FAQ not found');
    }
    
    redirect(SITE_URL . '/admin/faqs.php');
}

// ==================== ENQUIRY HANDLERS ====================

function handleEnquiryUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/enquiries.php');
    }
    
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    if ($admin->updateEnquiryStatus($id, $status)) {
        setFlash('success', 'Enquiry status updated');
    } else {
        setFlash('error', 'Failed to update enquiry');
    }
    
    redirect(SITE_URL . '/admin/enquiries.php');
}

function handleEnquiryDelete($admin) {
    $id = $_GET['id'] ?? 0;
    
    if ($admin->deleteEnquiry($id)) {
        setFlash('success', 'Enquiry deleted successfully');
    } else {
        setFlash('error', 'Enquiry not found');
    }
    
    redirect(SITE_URL . '/admin/enquiries.php');
}

// ==================== SETTINGS HANDLERS ====================

function handleSettingsUpdate($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/settings.php');
    }
    
    $settings = $admin->getSettings();
    
    $data = [
        'id' => $settings['id'],
        'site_name' => sanitize($_POST['site_name']),
        'site_tagline' => sanitize($_POST['site_tagline']),
        'contact_email' => sanitize($_POST['contact_email']),
        'contact_phone' => sanitize($_POST['contact_phone']),
        'contact_address' => $_POST['contact_address'],
        'social_facebook' => sanitize($_POST['social_facebook'] ?? ''),
        'social_twitter' => sanitize($_POST['social_twitter'] ?? ''),
        'social_instagram' => sanitize($_POST['social_instagram'] ?? ''),
        'social_linkedin' => sanitize($_POST['social_linkedin'] ?? ''),
        'social_youtube' => sanitize($_POST['social_youtube'] ?? ''),
        'seo_keywords' => $_POST['seo_keywords'],
        'seo_description' => $_POST['seo_description'],
        'google_analytics' => $_POST['google_analytics']
    ];
    
    // Handle logo
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['site_logo'], UPLOAD_PATH);
        if ($uploadResult['success']) {
            if ($settings['site_logo']) {
                deleteFile(UPLOAD_PATH . $settings['site_logo']);
            }
            $data['site_logo'] = $uploadResult['filename'];
        }
    } else {
        $data['site_logo'] = $settings['site_logo'];
    }
    
    // Handle favicon
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === 0) {
        $uploadResult = uploadFile($_FILES['site_favicon'], UPLOAD_PATH);
        if ($uploadResult['success']) {
            if ($settings['site_favicon']) {
                deleteFile(UPLOAD_PATH . $settings['site_favicon']);
            }
            $data['site_favicon'] = $uploadResult['filename'];
        }
    } else {
        $data['site_favicon'] = $settings['site_favicon'];
    }
    
    if ($admin->updateSettings($data)) {
        setFlash('success', 'Settings updated successfully');
    } else {
        setFlash('error', 'Failed to update settings');
    }
    
    redirect(SITE_URL . '/admin/settings.php');
}

// ==================== MEDIA HANDLERS ====================

function handleMediaUpload($admin) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(SITE_URL . '/admin/media.php');
    }
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        
        $uploadResult = uploadFile($file, UPLOAD_PATH . 'media/');
        
        if ($uploadResult['success']) {
            $filePath = UPLOAD_PATH . 'media/' . $uploadResult['filename'];
            $admin->addMedia(
                $uploadResult['filename'],
                $fileName,
                $filePath,
                $fileSize,
                $fileType
            );
            setFlash('success', 'File uploaded successfully');
        } else {
            setFlash('error', $uploadResult['message']);
        }
    }
    
    redirect(SITE_URL . '/admin/media.php');
}

function handleMediaDelete($admin) {
    $id = $_GET['id'] ?? 0;
    
    if ($admin->deleteMedia($id)) {
        setFlash('success', 'Media deleted successfully');
    } else {
        setFlash('error', 'Media not found');
    }
    
    redirect(SITE_URL . '/admin/media.php');
}
