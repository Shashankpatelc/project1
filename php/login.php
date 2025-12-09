<?php

session_start();
$conn = new mysqli("localhost","root","","wellness_tracker_db");

if($conn->connect_error){
    die("Connection failed".$conn->connect_error);
}


$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if($_SERVER["REQUEST_METHOD"] === "POST"){

    if ($username === '' || $password === '') {
        echo "Please enter both username and password.";
        $conn->close();
        exit;
    } 
}

$stmt = $conn->prepare("SELECT username, password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows == 1){
    $stmt->bind_result($fetched_username, $fetched_hashed_password);
    $stmt->fetch();
    
    if($fetched_hashed_password !== null && password_verify($password, $fetched_hashed_password)){
        // --- SUCCESS: Create Session Variables ---
        $_SESSION["loggedin"] = true;
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $fetched_username;
        
        // Redirect to the Dashboard controller
        header("location: ../php/dashboard.php");
        exit;
    } else {
        echo "Invalid username or password."; // Generic message
    }
} else {
    echo "Invalid username or password."; // Generic message
}

$stmt->close();
$conn->close();
?>
