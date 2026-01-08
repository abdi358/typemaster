<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to TypeMaster - Track your typing progress and compete on leaderboards">
    <title>Login - TypeMaster</title>

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

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .checkbox-label input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-500);
        }

        .forgot-link {
            font-size: 0.875rem;
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
                <h1 class="auth-title">Welcome back</h1>
                <p class="auth-subtitle">Sign in to track your progress</p>
            </div>

            <!-- Error/Success Messages -->
            <div class="alert alert-error hidden" id="errorAlert"></div>
            <div class="alert alert-success hidden" id="successAlert"></div>

            <!-- Login Form -->
            <form id="loginForm">
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
                            required autocomplete="current-password">
                        <button type="button" class="password-toggle" id="togglePassword">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-full" id="submitBtn">
                    Sign In
                </button>
            </form>

            <p class="auth-footer">
                Don't have an account? <a href="register.php">Sign up for free</a>
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

        // Form submission
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const errorAlert = document.getElementById('errorAlert');
            const successAlert = document.getElementById('successAlert');

            // Reset alerts
            errorAlert.classList.add('hidden');
            successAlert.classList.add('hidden');

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner" style="width: 20px; height: 20px; margin-right: 0.5rem;"></span> Signing in...';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/typingTest/api/auth.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.success) {
                    successAlert.textContent = 'Login successful! Redirecting...';
                    successAlert.classList.remove('hidden');

                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    errorAlert.textContent = data.error || 'Login failed. Please try again.';
                    errorAlert.classList.remove('hidden');

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Sign In';
                }
            } catch (error) {
                errorAlert.textContent = 'Connection error. Please try again.';
                errorAlert.classList.remove('hidden');

                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Sign In';
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