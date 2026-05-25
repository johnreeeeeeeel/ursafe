<?php
// Local Database Connection
$l_host = "localhost";
$l_user = "root";
$l_pass = "";
$l_db   = "ursafe_db";

$conn_local = new mysqli($l_host, $l_user, $l_pass, $l_db);

if ($conn_local->connect_error) {
    die("Local connection failed: " . $conn_local->connect_error);
}

// Remote Database Connection (School Database)
$r_host = "localhost";
$r_user = "root";
$r_pass = "";
$r_db   = "school_db";

$conn_remote = new mysqli($r_host, $r_user, $r_pass, $r_db);

if ($conn_remote->connect_error) {
    die("Remote connection failed: " . $conn_remote->connect_error);
}
?>