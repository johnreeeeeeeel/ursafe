<?php
require 'db_connection.php';

$search = $_GET['searchUser'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM view_users WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (fullname LIKE ? OR email LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>