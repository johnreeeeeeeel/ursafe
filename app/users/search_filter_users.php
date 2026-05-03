<?php
require '../app/db_connection.php';

$search = $_GET['searchUser'] ?? '';
$filter = $_GET['filter'] ?? 'new';

$stmt = $conn_local->prepare("CALL searchFilterUsers(?, ?)");
$stmt->bind_param("ss", $search, $filter);

$stmt->execute();
$result = $stmt->get_result();
?>