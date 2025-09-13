<?php
require_once 'config/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success_message = '';

// Handle login form submission
if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    // Authenticate user
    if (empty($errors)) {
        try {
            $user = $db->fetch("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email]);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_first_name'] = $user['first_name'];
                
                // Update last login
                $db->execute("UPDATE users SET updated_at = NOW() WHERE id = ?", [$user['id']]);
                
                // Handle remember me
                if ($remember) {
                    $token = bin2hex(random_bytes(16));
                    setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true); // 30 days
                    // In a real app, store this token in database with expiry
                }
                
                // Redirect to intended page or dashboard
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
                exit;
            } else {
                $errors[] = 'Invalid email or password';
            }
        } catch (Exception $e) {
            $errors[] = 'Login failed. Please try again.';
        }
    }
}

// Check for registration success message
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success_message = 'Registration successful! You can now login with your credentials.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo getSetting('site_name') ?: 'Travel Hub'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            padding: 20px 0;
        }
        .login-card { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.15); 
            overflow: hidden;
        }
        .login-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .social-login {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="login-card">
                    <div class="login-header p-4 text-center">
                        <h2 class="mb-1">
                            <i class="fas fa-sign-in-alt me-2"></i>Welcome Back
                        </h2>
                        <p class="mb-0 opacity-75">Sign in to your travel account</p>
                    </div>
                    
                    <div class="p-4">
                        <!-- Success Message -->
                        <?php if ($success_message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Error Messages -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php if (count($errors) == 1): ?>
                                    <?php echo htmlspecialchars($errors[0]); ?>
                                <?php else: ?>
                                    <ul class="mb-0 mt-2">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Login Form -->
                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope me-2 text-muted"></i>Email Address
                                </label>
                                <input type="email" name="email" class="form-control form-control-lg" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                       required placeholder="Enter your email">
                                <div class="invalid-feedback">Please enter a valid email address</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock me-2 text-muted"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control form-control-lg" 
                                           required placeholder="Enter your password" id="password">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Password is required</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="forgot-password.php" class="text-decoration-none">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                            </div>
                        </form>
                        
                        <!-- Social Login Options -->
                        <div class="social-login text-center">
                            <p class="text-muted mb-3">
                                <small>Or continue with</small>
                            </p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <button class="btn btn-outline-danger btn-sm flex-fill" onclick="alert('Google login coming soon!')">
                                    <i class="fab fa-google me-2"></i>Google
                                </button>
                                <button class="btn btn-outline-primary btn-sm flex-fill" onclick="alert('Facebook login coming soon!')">
                                    <i class="fab fa-facebook-f me-2"></i>Facebook
                                </button>
                            </div>
                        </div>
                        
                        <!-- Register Link -->
                        <hr class="my-4">
                        <div class="text-center">
                            <p class="mb-0">
                                Don't have an account? 
                                <a href="register.php" class="text-primary text-decoration-none fw-bold">
                                    Create Account
                                </a>
                            </p>
                        </div>
                        
                        <!-- Back to Home -->
                        <div class="text-center mt-3">
                            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-home me-2"></i>Back to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByTagName('form');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Auto-hide success messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-success');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
