<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="TypeMaster - Modern typing speed test application. Improve your typing speed with real-time metrics, analytics, and leaderboards.">
    <meta name="keywords" content="typing test, wpm, typing speed, keyboard practice">
    <meta name="author" content="TypeMaster">

    <title>TypeMaster - Modern Typing Speed Test</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/icons/keyboard.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Theme Manager -->
    <script src="assets/js/theme.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="app-wrapper">
        <!-- Header - Pill Navbar -->
        <header class="header">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <img src="assets/icons/keyboard.png" alt="TypeMaster" class="icon-xl">
                    </div>
                    <span class="logo-text">TypeMaster</span>
                </a>

                <nav class="nav">
                    <a href="index.php" class="nav-link active">Test</a>
                    <a href="#leaderboard" class="nav-link">Leaderboard</a>
                    <a href="#" class="nav-link" id="statsLink">Stats</a>
                </nav>

                <div class="header-actions">
                    <!-- Sound Toggle -->
                    <button class="theme-toggle" id="soundToggle" title="Toggle Sound">
                        <img src="assets/icons/sound-on.png" alt="Sound" class="icon-md" id="soundIcon">
                    </button>

                    <!-- Theme Toggle -->
                    <button class="theme-toggle" id="themeToggle" title="Toggle Theme">
                        <img src="assets/icons/sun.png" alt="Theme" class="icon-md" id="themeIcon">
                    </button>

                    <!-- Auth Button - Login -->
                    <a href="login.php" class="btn btn-secondary" id="loginBtn">Login</a>

                    <!-- User Menu (hidden by default) -->
                    <div class="user-menu hidden" id="userMenu">
                        <button class="btn btn-ghost" id="userMenuBtn">
                            <span id="usernameDisplay">User</span>
                            <img src="assets/icons/user.png" alt="User" class="icon-sm">
                        </button>
                        <div class="user-dropdown hidden" id="userDropdown">
                            <a href="profile.php" class="dropdown-item" id="profileLink">
                                <img src="assets/icons/user.png" alt="Profile" class="icon-sm"> Profile
                            </a>
                            <li>
                                <a href="#" id="logoutBtn" class="dropdown-item text-danger">
                                    <img src="assets/icons/logout.png" class="icon-sm" alt="Logout"> Logout
                                </a>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- Test Settings -->
                <div class="test-settings">
                    <!-- Mode Selection -->
                    <div class="settings-group" data-type="mode">
                        <span class="settings-label">mode</span>
                        <button class="option active" data-mode="time">time</button>
                        <button class="option" data-mode="words">words</button>
                    </div>

                    <!-- Time Options -->
                    <div class="settings-group" data-type="time" id="timeOptions">
                        <span class="settings-label">time</span>
                        <button class="option" data-value="15">15</button>
                        <button class="option" data-value="30">30</button>
                        <button class="option active" data-value="60">60</button>
                        <button class="option" data-value="120">120</button>
                    </div>

                    <!-- Word Count Options -->
                    <div class="settings-group hidden" data-type="words" id="wordOptions">
                        <span class="settings-label">words</span>
                        <button class="option" data-value="25">25</button>
                        <button class="option active" data-value="50">50</button>
                        <button class="option" data-value="100">100</button>
                        <button class="option" data-value="200">200</button>
                    </div>

                    <!-- Difficulty -->
                    <div class="settings-group">
                        <span class="settings-label">difficulty</span>
                        <button class="option active" data-difficulty="easy">easy</button>
                        <button class="option" data-difficulty="medium">medium</button>
                        <button class="option" data-difficulty="hard">hard</button>
                    </div>
                </div>

                <!-- Typing Container -->
                <div class="typing-container">
                    <!-- Live Stats -->
                    <div class="stats-bar">
                        <div class="stat-item">
                            <div class="stat-value highlight" id="wpmDisplay">0</div>
                            <div class="stat-label">WPM</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="accuracyDisplay">100%</div>
                            <div class="stat-label">Accuracy</div>
                        </div>
                        <div class="stat-item">
                            <div class="timer-display" id="timerDisplay">1:00</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="cpmDisplay">0</div>
                            <div class="stat-label">CPM</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" id="errorsDisplay">0</div>
                            <div class="stat-label">Errors</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress-container">
                        <div class="progress-bar" id="progressBar">
                            <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Text Display -->
                    <div class="text-display blurred" id="textDisplay">
                        <div class="text-content" id="textContent">
                            <!-- Text will be rendered here -->
                            <div class="loading-spinner"></div>
                        </div>
                        <div class="focus-overlay" id="focusOverlay">
                            <div class="focus-overlay-text">
                                👆 Click here or press any key to start typing
                            </div>
                        </div>

                        <!-- Hidden Input -->
                        <input type="text" class="typing-input" id="typingInput" autocomplete="off" autocorrect="off"
                            autocapitalize="off" spellcheck="false" autofocus>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-center gap-4" style="margin-top: 1.5rem;">
                        <button class="btn btn-secondary" id="restartBtn">
                            🔄 Restart Test
                        </button>
                        <button class="btn btn-ghost" id="newTextBtn">
                            📝 New Text
                        </button>
                    </div>

                    <!-- Keyboard Shortcut Hint -->
                    <div class="text-center"
                        style="margin-top: 1rem; color: var(--text-tertiary); font-size: 0.875rem;">
                        <kbd
                            style="background: var(--bg-elevated); padding: 0.25rem 0.5rem; border-radius: 4px; margin: 0 0.25rem;">Tab</kbd>
                        to restart
                        <span style="margin: 0 0.5rem;">•</span>
                        <kbd
                            style="background: var(--bg-elevated); padding: 0.25rem 0.5rem; border-radius: 4px; margin: 0 0.25rem;">Esc</kbd>
                        to reset
                    </div>
                </div>

                <!-- Leaderboard Section -->
                <section class="leaderboard-section" id="leaderboard" style="margin-top: 4rem;">
                    <h2 style="text-align: center; margin-bottom: 2rem;">🏆 Leaderboard</h2>

                    <div class="leaderboard-container" style="margin: 0 auto;">
                        <!-- Leaderboard Tabs -->
                        <div class="leaderboard-tabs">
                            <button class="leaderboard-tab active" data-type="daily">Today</button>
                            <button class="leaderboard-tab" data-type="weekly">This Week</button>
                            <button class="leaderboard-tab" data-type="global">All Time</button>
                        </div>

                        <!-- Leaderboard List -->
                        <div class="leaderboard-list" id="leaderboardList">
                            <!-- Leaderboard items will be rendered here -->
                            <div style="text-align: center; padding: 2rem; color: var(--text-tertiary);">
                                <div class="loading-spinner" style="margin: 0 auto 1rem;"></div>
                                <p>Loading leaderboard...</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer style="padding: 2rem; text-align: center; color: var(--text-tertiary); font-size: 0.875rem;">
            <p>TypeMaster - Practice typing, improve your speed</p>
            <p style="margin-top: 0.5rem;">
                Made with ❤️ |
                <a href="#" style="color: var(--primary-400);">About</a> |
                <a href="#" style="color: var(--primary-400);">Privacy</a> |
                <a href="#" style="color: var(--primary-400);">Terms</a>
            </p>
        </footer>
    </div>

    <!-- Results Modal -->
    <div class="modal-backdrop" id="resultsModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">🎉 Test Complete!</h3>
                <button class="modal-close" id="modalClose">✕</button>
            </div>
            <div class="modal-body">
                <!-- Personal Best Badge (hidden by default) -->
                <div class="hidden" id="personalBest" style="text-align: center; margin-bottom: 1rem;">
                    <span
                        style="background: var(--gradient-warm); color: white; padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 600;">
                        🏆 New Personal Best!
                    </span>
                </div>

                <!-- Results Grid -->
                <div class="results-grid">
                    <div class="result-card primary">
                        <div class="value" id="resultWpm">0</div>
                        <div class="label">Words Per Minute</div>
                    </div>
                    <div class="result-card">
                        <div class="value" id="resultAccuracy">0%</div>
                        <div class="label">Accuracy</div>
                    </div>
                    <div class="result-card">
                        <div class="value" id="resultRawWpm">0</div>
                        <div class="label">Raw WPM</div>
                    </div>
                    <div class="result-card">
                        <div class="value" id="resultCpm">0</div>
                        <div class="label">Characters/min</div>
                    </div>
                    <div class="result-card">
                        <div class="value" id="resultTime">0s</div>
                        <div class="label">Time</div>
                    </div>
                    <div class="result-card">
                        <div class="value" id="resultCorrect">0</div>
                        <div class="label">Correct</div>
                    </div>
                    <div class="result-card">
                        <div class="value" id="resultIncorrect">0</div>
                        <div class="label">Incorrect</div>
                    </div>
                    <div class="result-card">
                        <div class="value" id="resultErrors">0</div>
                        <div class="label">Total Errors</div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="chart-container">
                    <h4 class="chart-title">WPM Over Time</h4>
                    <canvas id="wpmChart" class="chart-canvas"></canvas>
                </div>

                <div class="chart-container">
                    <h4 class="chart-title">Accuracy Over Time</h4>
                    <canvas id="accuracyChart" class="chart-canvas"></canvas>
                </div>

                <div class="chart-container">
                    <h4 class="chart-title">Most Missed Keys</h4>
                    <div id="errorHeatmap"></div>
                </div>

                <!-- Actions -->
                <div class="flex justify-center gap-4" style="margin-top: 2rem;">
                    <button class="btn btn-primary btn-lg" id="nextTestBtn">
                        Next Test →
                    </button>
                    <button class="btn btn-secondary" id="shareResultBtn">
                        📤 Share
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievement Toast Container -->
    <div class="achievement-toast" id="achievementToast"></div>

    <!-- Stats Modal -->
    <div class="modal-backdrop" id="statsModal">
        <div class="modal" style="max-width: 800px;">
            <div class="modal-header">
                <h3 class="modal-title">📊 Your Statistics</h3>
                <button class="modal-close"
                    onclick="document.getElementById('statsModal').classList.remove('active')">✕</button>
            </div>
            <div class="modal-body">
                <div id="statsContainer">
                    <!-- Stats will be rendered here -->
                </div>

                <div class="chart-container" style="margin-top: 2rem;">
                    <h4 class="chart-title">Performance History</h4>
                    <canvas id="historyChart" class="chart-canvas" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/charts.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        // Mode toggle visibility
        document.querySelectorAll('[data-mode]').forEach(btn => {
            btn.addEventListener('click', function () {
                const mode = this.dataset.mode;
                document.getElementById('timeOptions').classList.toggle('hidden', mode !== 'time');
                document.getElementById('wordOptions').classList.toggle('hidden', mode !== 'words');
            });
        });

        // User menu toggle
        document.getElementById('userMenuBtn')?.addEventListener('click', function () {
            document.getElementById('userDropdown')?.classList.toggle('hidden');
        });

        // Close dropdown on outside click
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.user-menu')) {
                document.getElementById('userDropdown')?.classList.add('hidden');
            }
        });

        // Logout
        document.getElementById('logoutBtn')?.addEventListener('click', async function (e) {
            e.preventDefault();
            await APIClient.logout();
            window.location.reload();
        });

        // Stats link
        document.getElementById('statsLink')?.addEventListener('click', async function (e) {
            e.preventDefault();

            const authCheck = await APIClient.checkAuth();
            if (!authCheck.authenticated) {
                window.location.href = 'login.php';
                return;
            }

            // Load and show stats
            const statsModal = document.getElementById('statsModal');
            statsModal.classList.add('active');

            // Fetch stats from API
            try {
                const response = await fetch('/api/auth.php?action=stats');
                const data = await response.json();

                if (data.success) {
                    const statsDisplay = new StatsDisplay('statsContainer');
                    statsDisplay.render(data.stats);

                    // Render history chart
                    if (data.wpm_progression && data.wpm_progression.length > 0) {
                        const historyChart = new LineChart('historyChart', { height: 250 });
                        historyChart.setData([{
                            label: 'WPM',
                            data: data.wpm_progression.map((r, i) => ({ x: i, y: r.wpm })),
                            color: '#6366f1',
                            areaColor: 'rgba(99, 102, 241, 0.2)'
                        }]);
                    }
                }
            } catch (error) {
                console.error('Failed to load stats:', error);
            }
        });

        // Share result
        document.getElementById('shareResultBtn')?.addEventListener('click', function () {
            const wpm = document.getElementById('resultWpm').textContent;
            const accuracy = document.getElementById('resultAccuracy').textContent;

            const shareText = `🎯 I just typed ${wpm} WPM with ${accuracy} accuracy on TypeMaster! Can you beat my score? 🚀`;

            if (navigator.share) {
                navigator.share({
                    title: 'TypeMaster Score',
                    text: shareText,
                    url: window.location.href
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(shareText).then(() => {
                    alert('Result copied to clipboard!');
                });
            }
        });

        // Initialize charts manager for results modal
        const chartsManager = new ResultsChartManager();

        // Override modal show to initialize charts
        const originalShowModal = UIManager.showModal.bind(UIManager);
        UIManager.showModal = function (results) {
            originalShowModal(results);

            // Initialize and update charts
            setTimeout(() => {
                chartsManager.init();
                chartsManager.updateWithResults({
                    metrics: results.metrics || App.state.metricsHistory,
                    errorMap: TypingEngine.state.errorMap
                });
            }, 100);
        };
    </script>
</body>

</html>