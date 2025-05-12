<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
echo json_encode(["status" => "success", "message" => "Logged out successfully"]);
exit();
?>