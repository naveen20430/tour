<?php
require_once 'config/config.php';

$errors = [];
$success = false;

if ($_POST) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    
    // Validation
    if (empty($first_name)) $errors[] = 'First name is required';
    if (empty($last_name)) $errors[] = 'Last name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $existing_user = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing_user) {
            $errors[] = 'Email already registered';
        }
    }
    
    // Create user if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_token = bin2hex(random_bytes(16));
        
        try {
            $user_id = $db->execute(
                "INSERT INTO users (first_name, last_name, email, password, phone, country, verification_token, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
                [$first_name, $last_name, $email, $hashed_password, $phone, $country, $verification_token]
            );
            
            if ($user_id) {
                // Registration successful - redirect to login
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo getSetting('site_name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #1bbc9b; min-height: 100vh; padding: 50px 0; }
        .register-card { background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .register-header { background: #1bbc9b; color: white; border-radius: 15px 15px 0 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="register-card">
                    <?php if ($success): ?>
                        <div class="register-header p-4 text-center">
                            <h2><i class="fas fa-check-circle me-2"></i>Registration Successful!</h2>
                        </div>
                        <div class="p-4 text-center">
                            <div class="alert alert-success">
                                <h4>Welcome to <?php echo getSetting('site_name'); ?>!</h4>
                                <p>Your account has been created successfully. You can now login and start booking amazing tours.</p>
                            </div>
                            <div class="mt-4">
                                <a href="login" class="btn btn-primary me-3">Login Now</a>
                                <a href="index" class="btn btn-outline-secondary">Back to Home</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="register-header p-4 text-center">
                            <h2><i class="fas fa-user-plus me-2"></i>Create Account</h2>
                            <p class="mb-0">Join us for amazing travel experiences</p>
                        </div>
                        
                        <div class="p-4">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" name="first_name" class="form-control" required
                                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" name="last_name" class="form-control" required
                                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" name="email" class="form-control" required
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Password *</label>
                                        <input type="password" name="password" class="form-control" required minlength="6">
                                        <small class="text-muted">Minimum 6 characters</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm Password *</label>
                                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" name="phone" class="form-control"
                                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Country</label>
                                        <select name="country" class="form-select">
                                            <option value="">Select Country</option>
                                            <option value="US" <?php echo ($_POST['country'] ?? '') == 'US' ? 'selected' : ''; ?>>United States</option>
                                            <option value="UK" <?php echo ($_POST['country'] ?? '') == 'UK' ? 'selected' : ''; ?>>United Kingdom</option>
                                            <option value="CA" <?php echo ($_POST['country'] ?? '') == 'CA' ? 'selected' : ''; ?>>Canada</option>
                                            <option value="AU" <?php echo ($_POST['country'] ?? '') == 'AU' ? 'selected' : ''; ?>>Australia</option>
                                            <option value="IN" <?php echo ($_POST['country'] ?? '') == 'IN' ? 'selected' : ''; ?>>India</option>
                                            <option value="SG" <?php echo ($_POST['country'] ?? '') == 'SG' ? 'selected' : ''; ?>>Singapore</option>
                                            <option value="AE" <?php echo ($_POST['country'] ?? '') == 'AE' ? 'selected' : ''; ?>>UAE</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </button>
                                </div>
                            </form>
                            
                            <hr class="my-4">
                            
                            <div class="text-center">
                                <p class="mb-0">Already have an account? <a href="login" class="text-primary">Login here</a></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.querySelector('input[name="password"]');
            const confirmPassword = document.querySelector('input[name="confirm_password"]');
            
            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Passwords don't match");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            password.addEventListener('input', validatePassword);
            confirmPassword.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>
