<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Create your TypeMaster account - Track your typing progress and compete on leaderboards">
    <title>Sign Up - TypeMaster</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⌨️</text></svg>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Theme Manager -->
    <script src="assets/js/theme.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* Additional auth page styles */
        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .auth-logo-icon {
            width: 56px;
            height: 56px;
            background: var(--gradient-primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        .auth-logo-text {
            font-size: 2rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-tertiary);
            font-size: 1.25rem;
        }

        .input-group .form-input {
            padding-left: 3rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-tertiary);
            cursor: pointer;
            font-size: 1.25rem;
            padding: 0.25rem;
        }

        .password-toggle:hover {
            color: var(--text-secondary);
        }

        .password-requirements {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-tertiary);
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0.5rem 0 0 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.25rem;
        }

        .password-requirements li {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .password-requirements li.valid {
            color: var(--success-500);
        }

        .password-requirements li.invalid {
            color: var(--text-tertiary);
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .terms-checkbox input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-500);
            margin-top: 0.125rem;
        }

        .terms-checkbox label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .terms-checkbox a {
            color: var(--primary-400);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius-lg);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--error-400);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: var(--success-400);
        }

        .back-link {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
            transition: color var(--transition-fast);
        }

        .back-link:hover {
            color: var(--text-primary);
        }

        .theme-toggle-fixed {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
        }

        .benefits-list {
            background: var(--bg-elevated);
            border-radius: var(--radius-lg);
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .benefits-list h4 {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
        }

        .benefits-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .benefits-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-tertiary);
        }

        .benefits-list li span {
            color: var(--success-500);
        }
    </style>
</head>

<body>
    <!-- Back Link -->
    <a href="index.php" class="back-link">
        <span>←</span>
        <span>Back to typing test</span>
    </a>

    <!-- Theme Toggle -->
    <button class="theme-toggle theme-toggle-fixed" id="themeToggle" title="Toggle Theme">
        <img src="assets/icons/sun.png" alt="Theme" class="icon-md" id="themeIcon">
    </button>

    <!-- Auth Page -->
    <div class="auth-page">
        <div class="auth-card card-glass slide-up">
            <!-- Logo -->
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <img src="assets/icons/keyboard.png" alt="TypeMaster" class="icon-xl">
                </div>
                <span class="auth-logo-text">TypeMaster</span>
            </div>

            <div class="auth-header">
                <h1 class="auth-title">Create account</h1>
                <p class="auth-subtitle">Join thousands of typists improving daily</p>
            </div>

            <!-- Benefits -->
            <div class="benefits-list">
                <h4>What you get:</h4>
                <ul>
                    <li><span>✓</span> Track your progress over time</li>
                    <li><span>✓</span> Compete on global leaderboards</li>
                    <li><span>✓</span> Earn achievements and badges</li>
                    <li><span>✓</span> Detailed analytics & insights</li>
                </ul>
            </div>

            <!-- Error/Success Messages -->
            <div class="alert alert-error hidden" id="errorAlert"></div>
            <div class="alert alert-success hidden" id="successAlert"></div>

            <!-- Register Form -->
            <form id="registerForm">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-group">
                        <span class="input-icon">👤</span>
                        <input type="text" class="form-input" id="username" name="username" placeholder="speedtyper123"
                            required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+" autocomplete="username">
                    </div>
                    <div class="form-hint"
                        style="font-size: 0.75rem; color: var(--text-tertiary); margin-top: 0.25rem;">
                        Letters, numbers, and underscores only
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-group">
                        <span class="input-icon">📧</span>
                        <input type="email" class="form-input" id="email" name="email" placeholder="you@example.com"
                            required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <span class="input-icon">🔒</span>
                        <input type="password" class="form-input" id="password" name="password" placeholder="••••••••"
                            required minlength="8" autocomplete="new-password">
                        <button type="button" class="password-toggle" id="togglePassword">
                            👁️
                        </button>
                    </div>

                    <!-- Password Strength -->
                    <div class="password-strength" id="passwordStrength">
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                    </div>

                    <div class="password-requirements">
                        Password must contain:
                        <ul>
                            <li id="req-length" class="invalid">✓ 8+ characters</li>
                            <li id="req-upper" class="invalid">✓ Uppercase letter</li>
                            <li id="req-lower" class="invalid">✓ Lowercase letter</li>
                            <li id="req-number" class="invalid">✓ Number</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirmPassword">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-icon">🔒</span>
                        <input type="password" class="form-input" id="confirmPassword" name="confirmPassword"
                            placeholder="••••••••" required autocomplete="new-password">
                    </div>
                    <div class="form-error hidden" id="passwordMatchError"
                        style="color: var(--error-500); font-size: 0.75rem; margin-top: 0.25rem;">
                        Passwords do not match
                    </div>
                </div>

                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full" id="submitBtn">
                    Create Account
                </button>
            </form>

            <p class="auth-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </p>
        </div>
    </div>

    <script>
        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        if (themeIcon) {
            themeIcon.src = savedTheme === 'dark' ? 'assets/icons/sun.png' : 'assets/icons/moon.png';
        }

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            if (themeIcon) {
                themeIcon.src = newTheme === 'dark' ? 'assets/icons/sun.png' : 'assets/icons/moon.png';
            }
        });

        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.innerHTML = type === 'password' ? '👁️' : '🙈';
        });

        // Password validation
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordStrength = document.getElementById('passwordStrength');
        const passwordMatchError = document.getElementById('passwordMatchError');

        passwordInput.addEventListener('input', function () {
            const password = this.value;

            // Check requirements
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);

            // Update requirement indicators
            document.getElementById('req-length').className = hasLength ? 'valid' : 'invalid';
            document.getElementById('req-upper').className = hasUpper ? 'valid' : 'invalid';
            document.getElementById('req-lower').className = hasLower ? 'valid' : 'invalid';
            document.getElementById('req-number').className = hasNumber ? 'valid' : 'invalid';

            // Calculate strength
            const strength = [hasLength, hasUpper, hasLower, hasNumber].filter(Boolean).length;

            // Update strength bar
            passwordStrength.className = 'password-strength';
            if (strength === 1) passwordStrength.classList.add('weak');
            else if (strength === 2) passwordStrength.classList.add('medium');
            else if (strength === 3) passwordStrength.classList.add('strong');
            else if (strength === 4) passwordStrength.classList.add('very-strong');

            // Check match if confirm password has value
            if (confirmPasswordInput.value) {
                checkPasswordMatch();
            }
        });

        confirmPasswordInput.addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const match = passwordInput.value === confirmPasswordInput.value;
            passwordMatchError.classList.toggle('hidden', match);
        }

        // Form submission
        document.getElementById('registerForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const errorAlert = document.getElementById('errorAlert');
            const successAlert = document.getElementById('successAlert');

            // Reset alerts
            errorAlert.classList.add('hidden');
            successAlert.classList.add('hidden');

            // Validate passwords match
            if (passwordInput.value !== confirmPasswordInput.value) {
                errorAlert.textContent = 'Passwords do not match';
                errorAlert.classList.remove('hidden');
                return;
            }

            // Validate password strength
            const password = passwordInput.value;
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);

            if (!hasLength || !hasUpper || !hasLower || !hasNumber) {
                errorAlert.textContent = 'Password does not meet requirements';
                errorAlert.classList.remove('hidden');
                return;
            }

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner" style="width: 20px; height: 20px; margin-right: 0.5rem;"></span> Creating account...';

            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;

            try {
                const response = await fetch('/typingTest/api/auth.php?action=register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, email, password })
                });

                const data = await response.json();

                if (data.success) {
                    successAlert.textContent = 'Account created! Redirecting...';
                    successAlert.classList.remove('hidden');

                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    const errorMessage = data.errors ? data.errors.join('. ') : (data.error || 'Registration failed');
                    errorAlert.textContent = errorMessage;
                    errorAlert.classList.remove('hidden');

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Create Account';
                }
            } catch (error) {
                errorAlert.textContent = 'Connection error. Please try again.';
                errorAlert.classList.remove('hidden');

                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create Account';
            }
        });

        // Check if already logged in
        (async () => {
            try {
                const response = await fetch('/typingTest/api/auth.php?action=check');
                const data = await response.json();

                if (data.authenticated) {
                    window.location.href = 'index.php';
                }
            } catch (e) {
                // Not logged in, stay on page
            }
        })();
    </script>
</body>

</html>