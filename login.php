
<?php
include_once 'conn.php'; // Ensure this file establishes a valid connection
include_once "session_start.php";
$error_message = ''; // Variable to display error messages

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs
    $login = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Do not sanitize passwords

    try {
        // Prepare SQL query to check user in the database
        $query = "SELECT * FROM users WHERE email = :login";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the user exists
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the hashed password
            if ( password_verify($password, $user['password'])) {
                // Authentication successful, store user in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                switch ($user['role']) {
                    case 'stagiaire':
                        header("Location: stagiaire.php");
                        exit();
                    case 'admin':
                        header("Location: admin.php");
                        exit();
                    case 'formateur':
                        header("Location: formateur.php");
                        exit();
                    default:
                        $error_message = "Unrecognized role.";
                }
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "User not found. check your email";
        }
    } catch (PDOException $e) {
        $error_message = "Connection error: " . $e->getMessage();
       
    }
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StagiaireHub</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../sql/bootstrap-5.0.2-dist/bootstrap.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
        }
        .login-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }
    </style>
</head>
<body>
<div class="container-fluid min-vh-100 d-flex flex-column justify-content-center">
    <!-- Login Form -->
    <div class="login-container">
        <h1 class="text-center text-dark mb-4">Welcome to <span class="text-primary">StagiaireHub</span></h1>
        <form action="" method="post" class="needs-validation" novalidate>
            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <!-- Remember Me -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember Me</label>
            </div>
            <!-- Forget Password -->
            <div class="mb-3 text-end">
                <a href="./forgetpassword/forgot_password.php" class="text-decoration-none text-muted">Forgot Password?</a>
            </div>
            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" id="loginButton">
                    <span id="loginText">Login</span>
                    <span id="loginSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </div>
            <!-- Error Messages -->
            <?php if (!empty($error_message)) : ?>
                <div class="mt alert alert-danger text-center text-dark"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </form>
        <!-- Sign Up Link -->
        <div class="mt-3 text-center">
            <p class="text-muted">Don't have an account? <a href="ajouterUsers.php" class="text-decoration-none">Sign Up</a></p>
        </div>
    </div>
    
</div>

<!-- Footer -->
<footer class="text-center text-white py-3">
    &copy; 2023 StagiaireHub. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="../sql/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
<!-- Loading Spinner Script -->
<script>
    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('loginText').classList.add('d-none');
        document.getElementById('loginSpinner').classList.remove('d-none');
    });
</script>
</body>
</html>

