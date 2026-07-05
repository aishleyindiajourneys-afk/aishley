<?php
/**
 * Authentication Model
 * Aishley India Journeys
 */

class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Admin Login
     */
    public function adminLogin($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Update last login
            $updateStmt = $this->pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$admin['id']]);
            
            return $admin;
        }
        
        return false;
    }
    
    /**
     * User Registration
     */
    public function registerUser($name, $email, $password, $phone = null, $address = null) {
        // Check if email already exists
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        
        try {
            $stmt->execute([$name, $email, $hashedPassword, $phone, $address]);
            return ['success' => true, 'user_id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    /**
     * User Login
     */
    public function userLogin($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get User by ID
     */
    public function getUserById($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Get Admin by ID
     */
    public function getAdminById($adminId) {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        return $stmt->fetch();
    }
    
    /**
     * Update User Profile
     */
    public function updateUserProfile($userId, $name, $phone, $address, $profileImage = null) {
        $sql = "UPDATE users SET name = ?, phone = ?, address = ?";
        $params = [$name, $phone, $address];
        
        if ($profileImage) {
            $sql .= ", profile_image = ?";
            $params[] = $profileImage;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $userId;
        
        $stmt = $this->pdo->prepare($sql);
        
        try {
            $stmt->execute($params);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Update failed'];
        }
    }
    
    /**
     * Change User Password
     */
    public function changeUserPassword($userId, $currentPassword, $newPassword) {
        // Get current password hash
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        
        try {
            $stmt->execute([$hashedPassword, $userId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Password update failed'];
        }
    }
    
    /**
     * Add Tour to Favorites
     */
    public function addToFavorites($userId, $tourId) {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO user_favorites (user_id, tour_id) VALUES (?, ?)");
        
        try {
            $stmt->execute([$userId, $tourId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to add to favorites'];
        }
    }
    
    /**
     * Remove Tour from Favorites
     */
    public function removeFromFavorites($userId, $tourId) {
        $stmt = $this->pdo->prepare("DELETE FROM user_favorites WHERE user_id = ? AND tour_id = ?");
        
        try {
            $stmt->execute([$userId, $tourId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to remove from favorites'];
        }
    }
    
    /**
     * Get User Favorites
     */
    public function getUserFavorites($userId) {
        $stmt = $this->pdo->prepare("
            SELECT t.*, uf.created_at as added_date 
            FROM user_favorites uf
            JOIN tours t ON uf.tour_id = t.id
            WHERE uf.user_id = ? AND t.status = 'active'
            ORDER BY uf.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if Tour is in Favorites
     */
    public function isFavorite($userId, $tourId) {
        $stmt = $this->pdo->prepare("SELECT id FROM user_favorites WHERE user_id = ? AND tour_id = ?");
        $stmt->execute([$userId, $tourId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get User Enquiries
     */
    public function getUserEnquiries($userId) {
        $stmt = $this->pdo->prepare("
            SELECT e.*, t.title as tour_title 
            FROM enquiries e
            LEFT JOIN tours t ON e.tour_id = t.id
            WHERE e.email = (SELECT email FROM users WHERE id = ?)
            ORDER BY e.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
