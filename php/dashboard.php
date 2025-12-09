<?php
// dashboard.php (Controller)

session_start();

// Access Control: If the user is NOT logged in, redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../html/login.html");
    exit;
}

// Variables defined for the HTML view
$username = htmlspecialchars($_SESSION["username"]);
$user_id = $_SESSION["user_id"];
$submission_message = "";
$entries = []; // Placeholder for fetched data

// Include connection details
require_once '../php/connect_db.php'; 

// ... (Code before the connection)
require_once 'php/connect_db.php'; 

// === 1. Data Insertion Logic (Handles POST Request from the form) ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate inputs
    $mood_score = filter_input(INPUT_POST, 'mood_score', FILTER_VALIDATE_INT);
    $stress_score = filter_input(INPUT_POST, 'stress_score', FILTER_VALIDATE_INT);
    $notes = trim($_POST['notes']);
    $user_id = $_SESSION['user_id'];
    $entry_date = date("Y-m-d"); // Get today's date

    $mood_err = $stress_err = "";

    // Basic validation check (must be between 0 and 10)
    if ($mood_score === false || $mood_score < 0 || $mood_score > 10) {
        $mood_err = "Invalid mood score.";
    }
    if ($stress_score === false || $stress_score < 0 || $stress_score > 10) {
        $stress_err = "Invalid stress score.";
    }

    if (empty($mood_err) && empty($stress_err)) {
        
        // 2. Check for duplicate entry (Prevents multiple submissions on the same day)
        $check_sql = "SELECT entry_id FROM mood_entries WHERE user_id = ? AND entry_date = ?";
        if ($check_stmt = mysqli_prepare($conn, $check_sql)) {
            mysqli_stmt_bind_param($check_stmt, "is", $user_id, $entry_date);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $submission_message = "You have already tracked your mood today. Try again tomorrow!";
            } else {
                // 3. Insert the new entry
                $insert_sql = "INSERT INTO mood_entries (user_id, mood_score, stress_score, notes, entry_date) VALUES (?, ?, ?, ?, ?)";
                if ($insert_stmt = mysqli_prepare($conn, $insert_sql)) {
                    mysqli_stmt_bind_param($insert_stmt, "iiiss", $user_id, $mood_score, $stress_score, $notes, $entry_date);
                    if (mysqli_stmt_execute($insert_stmt)) {
                        $submission_message = "Thank you! Your entry has been securely saved.";
                    } else {
                        $submission_message = "Error: Could not save entry. " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($insert_stmt);
                }
            }
            mysqli_stmt_close($check_stmt);
        }
    }
}


// === 2. Data Retrieval Logic (B5) ===
// (We will add the code for fetching data in the next step)
$sql = "SELECT entry_date, mood_score, stress_score, notes FROM mood_entries WHERE user_id = ? ORDER BY entry_date DESC LIMIT 7";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $entries[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);


// Load the Dashboard HTML View
require_once '../html/dashboard.html'; 
?>
