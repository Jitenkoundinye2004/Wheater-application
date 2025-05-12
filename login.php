<?php
// session_start();
// include 'db.php';

// if (isset($_POST['login'])) {
//     // Sanitize user inputs
//     $email = htmlspecialchars($_POST['email']);
//     $password = $_POST['password'];

//     // Prepare and execute SQL statement
//     $sql = "SELECT id, email, password FROM users WHERE email = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//         $user = $result->fetch_assoc();
        
//         // Verify the password
//         if (password_verify($password, $user['password'])) {
//             // Store email in session
//             $_SESSION['email'] = $user['email'];

//             // Redirect to dashboard
//             header("Location: home.php");
//             exit();
//         } else {
//             $error = "Invalid email or password. Please try again.";
//         }
//     } else {
//         $error = "Invalid email or password. Please try again.";
//     }
// }
session_start();
include 'db.php'; // Ensure this file establishes a database connection and sets $conn

if (isset($_POST['login'])) {
    // Sanitize user inputs
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Prepare the SQL statement to match your table structure
    $sql = "SELECT username, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    // Bind the parameter and execute the query
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify()
        if (password_verify($password, $user['password'])) {
            // Store user details in the session
            $_SESSION['user'] = [
                'name' => $user['name'],
                'email' => $user['email']
            ];

            // Redirect to the home page or dashboard
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    } else {
        $error = "Invalid email or password. Please try again.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="loginpages.css">
</head>
<style>
/* Include your CSS styles here */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #121212;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #2b2e4a, #24243e, #0f0f1b); 
    background-size: cover;
    margin: 0;
    overflow: hidden; 
}
.login-container {
    background-color: #1e1e2f;
    padding: 20px 30px;
    border-radius: 10px;
    width: 350px;
    text-align: center;
}
h2 {
    margin-bottom: 15px;
    color: #fff;
}

p {
    font-size: 14px;
    color: #aaa;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #bbb;
}

input {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    background-color: #2d2d3f;
    color: #fff;
    font-size: 14px;
}

input::placeholder {
    color: #888;
}

.btn {
    background-color: #6c63ff;
    color: #fff;
    border: none;
    padding: 10px;
    width: 100%;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.btn:hover {
    background-color: #5249c5;
}

.back-to-signup {
    margin-top: 15px;
}

.back-to-signup a {
    color: #6c63ff;
    text-decoration: none;
}

.back-to-signup a:hover {
    text-decoration: underline;
}


.login-container {
    background-color: #1e1e2f;
    padding: 20px 30px;
    border-radius: 10px;
    width: 350px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5); 
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, rgba(255, 255, 255, 0.1), transparent);
    animation: background-move 10s infinite;
    z-index: -1;
}

@keyframes background-move {
    0% {
        transform: scale(1) rotate(0deg);
    }
    50% {
        transform: scale(1.2) rotate(180deg);
    }
    100% {
        transform: scale(1) rotate(360deg);
    }
}


</style>
<body>
    <form method="POST" action="login.php">
        <div class="login-container">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login" class="btn">Sign In</button>
            <div class="back-to-signup">
            <p>Don't have an account?<a href="signup.html">Sign up</a></p>
        </div>
        </div>
    </form>
    
</body>
</html>
