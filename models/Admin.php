<?php
/**
 * Admin Model
 * Aishley India Journeys
 */

class Admin {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // ==================== TOURS ====================
    
    public function getAllTours($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("
            SELECT t.*, d.name as destination_name 
            FROM tours t 
            LEFT JOIN destinations d ON t.destination_id = d.id 
            ORDER BY t.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getTourCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM tours");
        return $stmt->fetch()['count'];
    }
    
    public function getTourById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createTour($data) {
        $sql = "INSERT INTO tours (title, slug, destination_id, duration, price, discount_price, overview, highlights, itinerary, inclusions, exclusions, featured_image, featured, popular, status, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['destination_id'],
            $data['duration'],
            $data['price'],
            $data['discount_price'],
            $data['overview'],
            $data['highlights'],
            $data['itinerary'],
            $data['inclusions'],
            $data['exclusions'],
            $data['featured_image'],
            $data['featured'],
            $data['popular'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description']
        ]);
        
        return $result ? $this->pdo->lastInsertId() : false;
    }
    
    public function updateTour($id, $data) {
        $sql = "UPDATE tours SET title = ?, slug = ?, destination_id = ?, duration = ?, price = ?, discount_price = ?, overview = ?, highlights = ?, itinerary = ?, inclusions = ?, exclusions = ?, featured_image = ?, featured = ?, popular = ?, status = ?, meta_title = ?, meta_description = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['destination_id'],
            $data['duration'],
            $data['price'],
            $data['discount_price'],
            $data['overview'],
            $data['highlights'],
            $data['itinerary'],
            $data['inclusions'],
            $data['exclusions'],
            $data['featured_image'],
            $data['featured'],
            $data['popular'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description'],
            $id
        ]);
    }
    
    public function deleteTour($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tours WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== DESTINATIONS ====================
    
    public function getAllDestinations($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("SELECT * FROM destinations ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getDestinationCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM destinations");
        return $stmt->fetch()['count'];
    }
    
    public function getDestinationById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM destinations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createDestination($data) {
        $sql = "INSERT INTO destinations (name, slug, description, image, banner_image, state, country, featured, popular, status, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['image'],
            $data['banner_image'],
            $data['state'],
            $data['country'],
            $data['featured'],
            $data['popular'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description']
        ]);
    }
    
    public function updateDestination($id, $data) {
        $sql = "UPDATE destinations SET name = ?, slug = ?, description = ?, image = ?, banner_image = ?, state = ?, country = ?, featured = ?, popular = ?, status = ?, meta_title = ?, meta_description = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['image'],
            $data['banner_image'],
            $data['state'],
            $data['country'],
            $data['featured'],
            $data['popular'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description'],
            $id
        ]);
    }
    
    public function deleteDestination($id) {
        $stmt = $this->pdo->prepare("DELETE FROM destinations WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== BLOGS ====================
    
    public function getAllBlogs($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("
            SELECT b.*, c.name as category_name 
            FROM blogs b 
            LEFT JOIN blog_categories c ON b.category_id = c.id 
            ORDER BY b.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getBlogCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM blogs");
        return $stmt->fetch()['count'];
    }
    
    public function getBlogById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createBlog($data) {
        $sql = "INSERT INTO blogs (category_id, title, slug, excerpt, content, featured_image, author, status, featured, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['slug'],
            $data['excerpt'],
            $data['content'],
            $data['featured_image'],
            $data['author'],
            $data['status'],
            $data['featured'],
            $data['meta_title'],
            $data['meta_description']
        ]);
    }
    
    public function updateBlog($id, $data) {
        $sql = "UPDATE blogs SET category_id = ?, title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, author = ?, status = ?, featured = ?, meta_title = ?, meta_description = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['category_id'],
            $data['title'],
            $data['slug'],
            $data['excerpt'],
            $data['content'],
            $data['featured_image'],
            $data['author'],
            $data['status'],
            $data['featured'],
            $data['meta_title'],
            $data['meta_description'],
            $id
        ]);
    }
    
    public function deleteBlog($id) {
        $stmt = $this->pdo->prepare("DELETE FROM blogs WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getAllBlogCategories() {
        $stmt = $this->pdo->query("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name");
        return $stmt->fetchAll();
    }
    
    // ==================== PAGES ====================
    
    public function getAllPages($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("SELECT * FROM pages ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getPageCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM pages");
        return $stmt->fetch()['count'];
    }
    
    public function getPageById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getPageBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'active'");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    public function createPage($data) {
        $sql = "INSERT INTO pages (title, slug, content, featured_image, status, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['featured_image'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description']
        ]);
    }
    
    public function updatePage($id, $data) {
        $sql = "UPDATE pages SET title = ?, slug = ?, content = ?, featured_image = ?, status = ?, meta_title = ?, meta_description = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['featured_image'],
            $data['status'],
            $data['meta_title'],
            $data['meta_description'],
            $id
        ]);
    }
    
    public function deletePage($id) {
        $stmt = $this->pdo->prepare("DELETE FROM pages WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== GALLERY ====================
    
    public function getAllGallery($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getGalleryCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM gallery");
        return $stmt->fetch()['count'];
    }
    
    public function getGalleryById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createGalleryItem($data) {
        $sql = "INSERT INTO gallery (title, image, category, description, status, sort_order) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['image'],
            $data['category'],
            $data['description'],
            $data['status'],
            $data['sort_order']
        ]);
    }
    
    public function updateGalleryItem($id, $data) {
        $sql = "UPDATE gallery SET title = ?, image = ?, category = ?, description = ?, status = ?, sort_order = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['image'],
            $data['category'],
            $data['description'],
            $data['status'],
            $data['sort_order'],
            $id
        ]);
    }
    
    public function deleteGalleryItem($id) {
        $stmt = $this->pdo->prepare("DELETE FROM gallery WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== HERO SLIDER ====================
    
    public function getAllHeroSlides() {
        $stmt = $this->pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll();
    }
    
    public function getActiveHeroSlides() {
        $stmt = $this->pdo->query("SELECT * FROM hero_slides WHERE status = 'active' ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll();
    }
    
    public function getHeroSlideById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createHeroSlide($data) {
        $sql = "INSERT INTO hero_slides (title, subtitle, image, button_text, button_link, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['subtitle'],
            $data['image'],
            $data['button_text'],
            $data['button_link'],
            $data['sort_order'],
            $data['status']
        ]);
    }
    
    public function updateHeroSlide($id, $data) {
        $sql = "UPDATE hero_slides SET title = ?, subtitle = ?, image = ?, button_text = ?, button_link = ?, sort_order = ?, status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['subtitle'],
            $data['image'],
            $data['button_text'],
            $data['button_link'],
            $data['sort_order'],
            $data['status'],
            $id
        ]);
    }
    
    public function deleteHeroSlide($id) {
        $stmt = $this->pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== TESTIMONIALS ====================
    
    public function getAllTestimonials($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("
            SELECT t.*, tour.title as tour_title 
            FROM testimonials t 
            LEFT JOIN tours tour ON t.tour_id = tour.id 
            ORDER BY t.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getTestimonialCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM testimonials");
        return $stmt->fetch()['count'];
    }
    
    public function getTestimonialById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createTestimonial($data) {
        $sql = "INSERT INTO testimonials (name, email, location, tour_id, rating, review, image, featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['location'],
            $data['tour_id'],
            $data['rating'],
            $data['review'],
            $data['image'],
            $data['featured'],
            $data['status']
        ]);
    }
    
    public function updateTestimonial($id, $data) {
        $sql = "UPDATE testimonials SET name = ?, email = ?, location = ?, tour_id = ?, rating = ?, review = ?, image = ?, featured = ?, status = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['location'],
            $data['tour_id'],
            $data['rating'],
            $data['review'],
            $data['image'],
            $data['featured'],
            $data['status'],
            $id
        ]);
    }
    
    public function deleteTestimonial($id) {
        $stmt = $this->pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== FAQs ====================
    
    public function getAllFAQs($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("SELECT * FROM faqs ORDER BY sort_order ASC, created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getFAQCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM faqs");
        return $stmt->fetch()['count'];
    }
    
    public function getFAQById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM faqs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createFAQ($data) {
        $sql = "INSERT INTO faqs (question, answer, category, sort_order, status) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['question'],
            $data['answer'],
            $data['category'],
            $data['sort_order'],
            $data['status']
        ]);
    }
    
    public function updateFAQ($id, $data) {
        $sql = "UPDATE faqs SET question = ?, answer = ?, category = ?, sort_order = ?, status = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['question'],
            $data['answer'],
            $data['category'],
            $data['sort_order'],
            $data['status'],
            $id
        ]);
    }
    
    public function deleteFAQ($id) {
        $stmt = $this->pdo->prepare("DELETE FROM faqs WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== ENQUIRIES ====================
    
    public function getAllEnquiries($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("
            SELECT e.*, t.title as tour_title 
            FROM enquiries e 
            LEFT JOIN tours t ON e.tour_id = t.id 
            ORDER BY e.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getEnquiryCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM enquiries");
        return $stmt->fetch()['count'];
    }
    
    public function getEnquiryById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM enquiries WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function updateEnquiryStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    public function deleteEnquiry($id) {
        $stmt = $this->pdo->prepare("DELETE FROM enquiries WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // ==================== SETTINGS ====================
    
    public function getSettings() {
        $stmt = $this->pdo->query("SELECT * FROM settings LIMIT 1");
        return $stmt->fetch();
    }
    
    public function updateSettings($data) {
        $sql = "UPDATE settings SET site_name = ?, site_tagline = ?, site_logo = ?, site_favicon = ?, contact_email = ?, contact_phone = ?, contact_address = ?, social_facebook = ?, social_twitter = ?, social_instagram = ?, social_linkedin = ?, social_youtube = ?, seo_keywords = ?, seo_description = ?, google_analytics = ? WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['site_name'],
            $data['site_tagline'],
            $data['site_logo'],
            $data['site_favicon'],
            $data['contact_email'],
            $data['contact_phone'],
            $data['contact_address'],
            $data['social_facebook'],
            $data['social_twitter'],
            $data['social_instagram'],
            $data['social_linkedin'],
            $data['social_youtube'],
            $data['seo_keywords'],
            $data['seo_description'],
            $data['google_analytics'],
            $data['id']
        ]);
    }
    
    // ==================== MEDIA LIBRARY ====================
    
    public function getAllMedia($page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->pdo->prepare("SELECT * FROM media_library ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getMediaCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM media_library");
        return $stmt->fetch()['count'];
    }
    
    public function addMedia($filename, $originalName, $filePath, $fileSize, $fileType, $altText = null) {
        $sql = "INSERT INTO media_library (filename, original_name, file_path, file_size, file_type, alt_text, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $filename,
            $originalName,
            $filePath,
            $fileSize,
            $fileType,
            $altText,
            $_SESSION['admin_name'] ?? 'Admin'
        ]);
    }
    
    public function deleteMedia($id) {
        $stmt = $this->pdo->prepare("SELECT file_path FROM media_library WHERE id = ?");
        $stmt->execute([$id]);
        $media = $stmt->fetch();
        
        if ($media) {
            deleteFile($media['file_path']);
            $stmt = $this->pdo->prepare("DELETE FROM media_library WHERE id = ?");
            return $stmt->execute([$id]);
        }
        
        return false;
    }
    
    // ==================== DASHBOARD STATS ====================
    
    public function getDashboardStats() {
        $stats = [];
        
        $stats['total_tours'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM tours")->fetch()['count'];
        $stats['active_tours'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM tours WHERE status = 'active'")->fetch()['count'];
        $stats['total_destinations'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM destinations")->fetch()['count'];
        $stats['total_blogs'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM blogs")->fetch()['count'];
        $stats['total_enquiries'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM enquiries")->fetch()['count'];
        $stats['new_enquiries'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'new'")->fetch()['count'];
        $stats['contacted_enquiries'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'contacted'")->fetch()['count'];
        $stats['closed_enquiries'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM enquiries WHERE status = 'closed'")->fetch()['count'];
        $stats['today_enquiries'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM enquiries WHERE DATE(created_at) = CURDATE()")->fetch()['count'];
        $stats['week_enquiries'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM enquiries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()['count'];
        $stats['homepage_leads'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM enquiries WHERE subject = 'Homepage Lead'")->fetch()['count'];
        $stats['total_users'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
        $stats['total_favorites'] = (int)$this->pdo->query("SELECT COUNT(*) as count FROM user_favorites")->fetch()['count'];
        
        return $stats;
    }
    
    public function getEnquiryTrend($days = 14) {
        $stmt = $this->pdo->prepare("
            SELECT DATE(created_at) as day, COUNT(*) as total
            FROM enquiries
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ");
        $stmt->execute([$days - 1]);
        $rows = $stmt->fetchAll();
        $byDay = [];
        foreach ($rows as $row) {
            $byDay[$row['day']] = (int)$row['total'];
        }
        
        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-{$i} day"));
            $trend[] = [
                'day' => $day,
                'label' => date('d M', strtotime($day)),
                'total' => $byDay[$day] ?? 0
            ];
        }
        return $trend;
    }
    
    public function getLeadSources() {
        $stmt = $this->pdo->query("
            SELECT
                CASE
                    WHEN subject = 'Homepage Lead' THEN 'Homepage popup'
                    WHEN tour_id IS NOT NULL THEN 'Tour page'
                    ELSE 'Contact / other'
                END AS source,
                COUNT(*) AS total
            FROM enquiries
            GROUP BY
                CASE
                    WHEN subject = 'Homepage Lead' THEN 'Homepage popup'
                    WHEN tour_id IS NOT NULL THEN 'Tour page'
                    ELSE 'Contact / other'
                END
            ORDER BY total DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function getTopEnquiryTours($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT t.title, COUNT(e.id) AS total
            FROM enquiries e
            INNER JOIN tours t ON e.tour_id = t.id
            GROUP BY t.id, t.title
            ORDER BY total DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    public function getContentHealth() {
        return [
            'tours_without_image' => (int)$this->pdo->query("SELECT COUNT(*) as count FROM tours WHERE featured_image IS NULL OR featured_image = ''")->fetch()['count'],
            'inactive_tours' => (int)$this->pdo->query("SELECT COUNT(*) as count FROM tours WHERE status = 'inactive'")->fetch()['count'],
            'destinations_without_image' => (int)$this->pdo->query("SELECT COUNT(*) as count FROM destinations WHERE (image IS NULL OR image = '') AND (banner_image IS NULL OR banner_image = '')")->fetch()['count'],
            'unpublished_blogs' => (int)$this->pdo->query("SELECT COUNT(*) as count FROM blogs WHERE status != 'published'")->fetch()['count'],
            'featured_tours' => (int)$this->pdo->query("SELECT COUNT(*) as count FROM tours WHERE featured = 'yes' AND status = 'active'")->fetch()['count']
        ];
    }
    
    public function getRecentEnquiries($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT e.*, t.title as tour_title 
            FROM enquiries e 
            LEFT JOIN tours t ON e.tour_id = t.id 
            ORDER BY e.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
