<?php
// include/functions.php

function getSettings($pdo) {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function getPage($pdo, $page_name) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE page_name = ?");
    $stmt->execute([$page_name]);
    return $stmt->fetch();
}

function getPackages($pdo, $limit = null, $featured = null) {
    $sql = "SELECT * FROM packages WHERE availability = 1";
    if($featured !== null) {
        $sql .= " AND featured = " . ($featured ? 1 : 0);
    }
    $sql .= " ORDER BY created_at DESC";
    if($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getGallery($pdo, $limit = null) {
    $sql = "SELECT * FROM gallery WHERE active = 1 ORDER BY sort_order, upload_date DESC";
    if($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getContactCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contacts WHERE status = 'unread'");
    return $stmt->fetch()['count'];
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function truncateText($text, $length = 100) {
    if(strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

function getSightseeing($pdo, $limit = null) {
    $sql = "SELECT * FROM sightseeing WHERE active = 1 ORDER BY sort_order ASC";
    if($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get package by ID
 */
function getPackageById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ? AND availability = 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get related packages (same destination or featured)
 * FIXED VERSION - No parameter binding for LIMIT
 */
function getRelatedPackages($pdo, $excludeId, $destination, $limit = 3) {
    // Cast limit to integer for safety
    $limit = intval($limit);
    $excludeId = intval($excludeId);
    
    // First try to get packages with same destination
    $sql = "
        SELECT * FROM packages 
        WHERE id != {$excludeId} AND destination = '{$destination}' AND availability = 1
        ORDER BY featured DESC, id DESC 
        LIMIT {$limit}
    ";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If not enough related packages, get some featured ones
    if (count($results) < $limit) {
        $remaining = $limit - count($results);
        $sql2 = "
            SELECT * FROM packages 
            WHERE id != {$excludeId} AND destination != '{$destination}' AND availability = 1 AND featured = 1
            ORDER BY RAND() 
            LIMIT {$remaining}
        ";
        $stmt2 = $pdo->query($sql2);
        $more = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $results = array_merge($results, $more);
    }
    
    return $results;
}

/**
 * Alternative safer version using prepared statements
 */
function getRelatedPackagesSafe($pdo, $excludeId, $destination, $limit = 3) {
    // First try to get packages with same destination
    $stmt = $pdo->prepare("
        SELECT * FROM packages 
        WHERE id != ? AND destination = ? AND availability = 1
        ORDER BY featured DESC, id DESC 
        LIMIT ?
    ");
    $stmt->bindParam(1, $excludeId, PDO::PARAM_INT);
    $stmt->bindParam(2, $destination, PDO::PARAM_STR);
    $stmt->bindParam(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If not enough related packages, get some featured ones
    if (count($results) < $limit) {
        $remaining = $limit - count($results);
        $stmt2 = $pdo->prepare("
            SELECT * FROM packages 
            WHERE id != ? AND destination != ? AND availability = 1 AND featured = 1
            ORDER BY RAND() 
            LIMIT ?
        ");
        $stmt2->bindParam(1, $excludeId, PDO::PARAM_INT);
        $stmt2->bindParam(2, $destination, PDO::PARAM_STR);
        $stmt2->bindParam(3, $remaining, PDO::PARAM_INT);
        $stmt2->execute();
        $more = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $results = array_merge($results, $more);
    }
    
    return $results;
}

/**
 * Get featured packages
 */
function getFeaturedPackages($pdo, $limit = 3) {
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE availability = 1 AND featured = 1 ORDER BY id DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all destinations (for filter dropdown)
 */
function getDestinations($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT destination FROM packages WHERE availability = 1 ORDER BY destination");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get package count
 */
function getPackageCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM packages WHERE availability = 1");
    return $stmt->fetchColumn();
}

/**
 * Get destination count (unique destinations)
 */
function getDestinationCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(DISTINCT destination) FROM packages WHERE availability = 1");
    return $stmt->fetchColumn();
}

/**
 * Get featured package count
 */
function getFeaturedCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM packages WHERE availability = 1 AND featured = 1");
    return $stmt->fetchColumn();
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = '?') {
    return $currency . number_format($amount, 0);
}

/**
 * Calculate discount percentage
 */
function calculateDiscount($originalPrice, $discountedPrice) {
    if ($originalPrice <= 0) return 0;
    return round((($originalPrice - $discountedPrice) / $originalPrice) * 100);
}

/**
 * Get all active packages with pagination
 */
function getPackagesPaginated($pdo, $offset = 0, $limit = 6) {
    $stmt = $pdo->prepare("
        SELECT * FROM packages 
        WHERE availability = 1 
        ORDER BY featured DESC, created_at DESC 
        LIMIT ?, ?
    ");
    $stmt->execute([$offset, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Search packages by keyword or destination
 */
function searchPackages($pdo, $keyword) {
    $keyword = '%' . $keyword . '%';
    $stmt = $pdo->prepare("
        SELECT * FROM packages 
        WHERE availability = 1 
        AND (name LIKE ? OR destination LIKE ? OR description LIKE ?)
        ORDER BY featured DESC, created_at DESC
    ");
    $stmt->execute([$keyword, $keyword, $keyword]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}