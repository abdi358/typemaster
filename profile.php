<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Your TypeMaster profile - View your typing statistics, achievements, and progress.">
    <title>Profile - TypeMaster</title>

    <!-- Favicon -->
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/icons/keyboard.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Theme Manager -->
    <script src="assets/js/theme.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Profile Page Specific Styles */
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--space-6);
        }

        /* Profile Header */
        .profile-header {
            display: flex;
            align-items: center;
            gap: var(--space-6);
            padding: var(--space-8);
            background: var(--bg-surface);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-default);
            margin-bottom: var(--space-8);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            flex-shrink: 0;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-info {
            flex: 1;
        }

        .profile-username {
            font-size: var(--text-3xl);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--space-2);
        }

        .profile-meta {
            display: flex;
            gap: var(--space-4);
            color: var(--text-secondary);
            font-size: var(--text-sm);
            flex-wrap: wrap;
        }

        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .profile-streak {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-4) var(--space-6);
            background: var(--bg-elevated);
            border-radius: var(--radius-lg);
            text-align: center;
        }

        .streak-value {
            font-size: var(--text-2xl);
            font-weight: 700;
            color: var(--warning-400);
        }

        .streak-label {
            font-size: var(--text-xs);
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-4);
            margin-bottom: var(--space-8);
        }

        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-default);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-500);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.15);
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            margin-bottom: var(--space-3);
        }

        .stat-card .stat-value {
            font-size: var(--text-3xl);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--space-1);
        }

        .stat-card .stat-value.highlight {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card .stat-label {
            font-size: var(--text-sm);
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-6);
            margin-bottom: var(--space-8);
        }

        .chart-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-default);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
        }

        .chart-card-title {
            font-size: var(--text-lg);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-4);
        }

        .chart-card canvas {
            max-height: 300px;
        }

        /* Error Heatmap */
        .error-heatmap {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-2);
            justify-content: center;
        }

        .error-key {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: var(--radius-md);
            font-family: var(--font-mono);
            font-size: var(--text-lg);
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .error-key:hover {
            transform: scale(1.1);
        }

        .error-key .count {
            font-size: var(--text-xs);
            opacity: 0.8;
        }

        .error-low {
            background: rgba(34, 197, 94, 0.2);
            color: var(--success-400);
        }

        .error-medium {
            background: rgba(250, 204, 21, 0.2);
            color: var(--warning-400);
        }

        .error-high {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger-400);
        }

        /* Achievements Section */
        .achievements-section {
            margin-bottom: var(--space-8);
        }

        .section-title {
            font-size: var(--text-xl);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-4);
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: var(--space-4);
        }

        .achievement-card {
            display: flex;
            align-items: center;
            gap: var(--space-4);
            padding: var(--space-4);
            background: var(--bg-surface);
            border: 1px solid var(--border-default);
            border-radius: var(--radius-lg);
            transition: all 0.3s ease;
        }

        .achievement-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .achievement-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
        }

        .achievement-icon.common {
            background: linear-gradient(135deg, #6b7280, #4b5563);
        }

        .achievement-icon.rare {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .achievement-icon.epic {
            background: linear-gradient(135deg, #a855f7, #7c3aed);
        }

        .achievement-icon.legendary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .achievement-info {
            flex: 1;
            min-width: 0;
        }

        .achievement-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--space-1);
        }

        .achievement-desc {
            font-size: var(--text-sm);
            color: var(--text-tertiary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .achievement-date {
            font-size: var(--text-xs);
            color: var(--text-tertiary);
            margin-top: var(--space-1);
        }

        /* Recent Tests Table */
        .recent-tests-section {
            background: var(--bg-surface);
            border: 1px solid var(--border-default);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .recent-tests-header {
            padding: var(--space-4) var(--space-6);
            border-bottom: 1px solid var(--border-default);
        }

        .tests-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tests-table th,
        .tests-table td {
            padding: var(--space-4) var(--space-6);
            text-align: left;
        }

        .tests-table th {
            font-size: var(--text-xs);
            font-weight: 600;
            color: var(--text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--bg-elevated);
        }

        .tests-table tr:not(:last-child) td {
            border-bottom: 1px solid var(--border-default);
        }

        .tests-table tr:hover td {
            background: var(--bg-hover);
        }

        .tests-table .wpm-cell {
            font-weight: 600;
            color: var(--primary-400);
        }

        .tests-table .accuracy-cell {
            color: var(--success-400);
        }

        .tests-table .mode-badge {
            display: inline-block;
            padding: var(--space-1) var(--space-2);
            background: var(--bg-elevated);
            border-radius: var(--radius-sm);
            font-size: var(--text-xs);
            text-transform: uppercase;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: var(--space-12);
            color: var(--text-tertiary);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: var(--space-4);
            opacity: 0.5;
        }

        .empty-state-text {
            font-size: var(--text-lg);
            margin-bottom: var(--space-4);
        }

        /* Loading State */
        .loading-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: var(--space-12);
            color: var(--text-tertiary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-meta {
                justify-content: center;
            }

            .charts-section {
                grid-template-columns: 1fr;
            }

            .profile-container {
                padding: var(--space-4);
            }

            .tests-table {
                font-size: var(--text-sm);
            }

            .tests-table th,
            .tests-table td {
                padding: var(--space-3) var(--space-4);
            }
        }
    </style>
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
                    <a href="index.php" class="nav-link">Test</a>
                    <a href="index.php#leaderboard" class="nav-link">Leaderboard</a>
                    <a href="profile.php" class="nav-link active">Profile</a>
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

                    <!-- User Menu -->
                    <div class="user-menu" id="userMenu">
                        <button class="btn btn-ghost" id="userMenuBtn">
                            <span id="usernameDisplay">User</span>
                            <img src="assets/icons/user.png" alt="User" class="icon-sm">
                        </button>
                        <div class="user-dropdown hidden" id="userDropdown">
                            <a href="profile.php" class="dropdown-item">
                                <img src="assets/icons/user.png" alt="Profile" class="icon-sm"> Profile
                            </a>
                            <hr>
                            <a href="#" class="dropdown-item" id="logoutBtn">
                                <img src="assets/icons/logout.png" class="icon-sm" alt="Logout"> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="profile-container">
                <!-- Loading State -->
                <div class="loading-state" id="loadingState">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 1rem;">Loading your profile...</p>
                </div>

                <!-- Profile Content (hidden until loaded) -->
                <div id="profileContent" style="display: none;">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-avatar" id="profileAvatar">
                            <!-- Will show initial or avatar -->
                        </div>
                        <div class="profile-info">
                            <h1 class="profile-username" id="profileUsername">Username</h1>
                            <div class="profile-meta">
                                <span class="profile-meta-item">
                                    📅 Joined <span id="joinDate">-</span>
                                </span>
                                <span class="profile-meta-item">
                                    📧 <span id="profileEmail">-</span>
                                </span>
                            </div>
                        </div>
                        <div class="profile-streak">
                            <div>
                                <div class="streak-value" id="currentStreak">0</div>
                                <div class="streak-label">🔥 Day Streak</div>
                            </div>
                            <div style="border-left: 1px solid var(--border-default); padding-left: var(--space-4);">
                                <div class="streak-value" id="maxStreak">0</div>
                                <div class="streak-label">Best Streak</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <img src="assets/icons/test.png" class="icon-lg" alt="Tests">
                            </div>
                            <div class="stat-value" id="totalTests">0</div>
                            <div class="stat-label">Tests Completed</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <img src="assets/icons/star.png" class="icon-lg" alt="Best">
                            </div>
                            <div class="stat-value highlight" id="bestWpm">0</div>
                            <div class="stat-label">Best WPM</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <img src="assets/icons/chart.png" class="icon-lg" alt="Avg">
                            </div>
                            <div class="stat-value" id="avgWpm">0</div>
                            <div class="stat-label">Average WPM</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <img src="assets/icons/target.png" class="icon-lg" alt="Accuracy">
                            </div>
                            <div class="stat-value" id="avgAccuracy">0%</div>
                            <div class="stat-label">Average Accuracy</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <img src="assets/icons/time.png" class="icon-lg" alt="Time">
                            </div>
                            <div class="stat-value" id="totalTime">0</div>
                            <div class="stat-label">Time Typing</div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="charts-section">
                        <div class="chart-card">
                            <h3 class="chart-card-title">
                                <img src="assets/icons/chart.png" class="icon-md" alt="WPM"> WPM Progress
                            </h3>
                            <canvas id="wpmProgressChart"></canvas>
                        </div>
                        <div class="chart-card">
                            <h3 class="chart-card-title">
                                <img src="assets/icons/target.png" class="icon-md" alt="Errors"> Problem Keys
                            </h3>
                            <div class="error-heatmap" id="errorHeatmap">
                                <div class="empty-state" style="padding: 2rem;">
                                    <p>No error data yet. Complete some tests!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievements Section -->
                    <div class="achievements-section">
                        <h2 class="section-title">
                            <img src="assets/icons/trophy.png" class="icon-md" alt="Trophy"> Achievements
                        </h2>
                        <div class="achievements-grid" id="achievementsGrid">
                            <!-- Achievements will be rendered here -->
                        </div>
                    </div>

                    <!-- Recent Tests -->
                    <div class="recent-tests-section">
                        <div class="recent-tests-header">
                            <h2 class="section-title" style="margin: 0;">
                                <img src="assets/icons/test.png" class="icon-md" alt="Recent"> Recent Tests
                            </h2>
                        </div>
                        <table class="tests-table">
                            <thead>
                                <tr>
                                    <th>WPM</th>
                                    <th>Accuracy</th>
                                    <th>Mode</th>
                                    <th>Difficulty</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentTestsBody">
                                <!-- Tests will be rendered here -->
                            </tbody>
                        </table>
                    </div>
                </div>
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

    <script>
        // Theme management handled by theme.js through #themeToggle click listener

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

        // Logout handler
        document.getElementById('logoutBtn')?.addEventListener('click', async function (e) {
            e.preventDefault();
            await fetch('/typingTest/api/auth.php?action=logout', { method: 'POST' });
            window.location.href = 'login.php';
        });

        // Format date helper
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Format time helper (seconds to readable)
        function formatTime(seconds) {
            if (seconds < 60) return `${seconds}s`;
            if (seconds < 3600) return `${Math.floor(seconds / 60)}m`;
            const hours = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            return `${hours}h ${mins}m`;
        }

        // Load profile data
        async function loadProfile() {
            try {
                // Check auth first
                const authResponse = await fetch('/typingTest/api/auth.php?action=check');
                const authData = await authResponse.json();

                if (!authData.success || !authData.authenticated) {
                    window.location.href = 'login.php';
                    return;
                }

                // Update username in navbar
                document.getElementById('usernameDisplay').textContent = authData.user.username;

                // Handle Admin Link
                if (authData.user.role === 'admin') {
                    const dropdown = document.getElementById('userDropdown');
                    if (dropdown && !document.getElementById('adminLink')) {
                        const link = document.createElement('a');
                        link.href = 'admin/dashboard.php';
                        link.className = 'dropdown-item';
                        link.id = 'adminLink';
                        link.textContent = '🛡️ Admin Panel';

                        const hr = dropdown.querySelector('hr');
                        if (hr) dropdown.insertBefore(link, hr);
                    }
                }

                // Fetch full profile
                const profileResponse = await fetch('/typingTest/api/auth.php?action=profile');
                const profileData = await profileResponse.json();

                if (!profileData.success) {
                    throw new Error('Failed to load profile');
                }

                // Fetch stats
                const statsResponse = await fetch('/typingTest/api/auth.php?action=stats');
                const statsData = await statsResponse.json();

                // Hide loading, show content
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('profileContent').style.display = 'block';

                // Populate profile data
                populateProfile(profileData.profile, statsData);

            } catch (error) {
                console.error('Error loading profile:', error);
                document.getElementById('loadingState').innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">😕</div>
                        <div class="empty-state-text">Failed to load profile</div>
                        <a href="index.php" class="btn btn-primary">Back to Home</a>
                    </div>
                `;
            }
        }

        // Populate profile with data
        function populateProfile(profile, statsData) {
            // Avatar
            const avatarEl = document.getElementById('profileAvatar');
            if (profile.avatar_url) {
                avatarEl.innerHTML = `<img src="${profile.avatar_url}" alt="Avatar">`;
            } else {
                avatarEl.textContent = profile.username.charAt(0).toUpperCase();
            }

            // Basic info
            document.getElementById('profileUsername').textContent = profile.username;
            document.getElementById('profileEmail').textContent = profile.email;
            document.getElementById('joinDate').textContent = formatDate(profile.created_at);

            // Stats
            const stats = profile.stats;
            document.getElementById('currentStreak').textContent = stats.current_streak || 0;
            document.getElementById('maxStreak').textContent = stats.max_streak || 0;
            document.getElementById('totalTests').textContent = stats.total_tests || 0;
            document.getElementById('bestWpm').textContent = Math.round(stats.best_wpm || 0);
            document.getElementById('avgWpm').textContent = Math.round(stats.avg_wpm || 0);
            document.getElementById('avgAccuracy').textContent = `${Math.round(stats.avg_accuracy || 0)}%`;
            document.getElementById('totalTime').textContent = formatTime(stats.total_time || 0);

            // Achievements
            renderAchievements(profile.achievements || []);

            // Recent tests
            renderRecentTests(profile.recent_tests || []);

            // Charts
            if (statsData.success) {
                renderWpmChart(statsData.wpm_progression || []);
                renderErrorHeatmap(statsData.error_heatmap || {});
            }
        }

        // Render achievements
        function renderAchievements(achievements) {
            const container = document.getElementById('achievementsGrid');

            if (!achievements || achievements.length === 0) {
                container.innerHTML = `
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <div class="empty-state-icon">🏆</div>
                        <div class="empty-state-text">No achievements yet</div>
                        <p>Complete tests to unlock achievements!</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = achievements.map(a => `
                <div class="achievement-card">
                    <div class="achievement-icon ${a.rarity || 'common'}">
                        ${a.icon || '🏅'}
                    </div>
                    <div class="achievement-info">
                        <div class="achievement-name">${a.name}</div>
                        <div class="achievement-desc">${a.description}</div>
                        <div class="achievement-date">Unlocked ${formatDate(a.unlocked_at)}</div>
                    </div>
                </div>
            `).join('');
        }

        // Render recent tests table
        function renderRecentTests(tests) {
            const tbody = document.getElementById('recentTestsBody');

            if (!tests || tests.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-tertiary);">
                            No tests completed yet. <a href="index.php" style="color: var(--primary-400);">Take your first test!</a>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = tests.map(test => `
                <tr>
                    <td class="wpm-cell">${Math.round(test.wpm)} WPM</td>
                    <td class="accuracy-cell">${Math.round(test.accuracy)}%</td>
                    <td><span class="mode-badge">${test.test_mode} ${test.test_value}</span></td>
                    <td>${test.difficulty}</td>
                    <td>${formatDate(test.completed_at)}</td>
                </tr>
            `).join('');
        }

        // Render WPM progress chart
        function renderWpmChart(data) {
            if (!data || data.length === 0) return;

            const ctx = document.getElementById('wpmProgressChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map((_, i) => `Test ${i + 1}`),
                    datasets: [{
                        label: 'WPM',
                        data: data.map(d => d.wpm),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Accuracy',
                        data: data.map(d => d.accuracy),
                        borderColor: '#22c55e',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#9ca3af'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: { color: '#9ca3af' }
                        },
                        y: {
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            ticks: { color: '#9ca3af' },
                            title: { display: true, text: 'WPM', color: '#9ca3af' }
                        },
                        y1: {
                            position: 'right',
                            grid: { display: false },
                            ticks: { color: '#9ca3af' },
                            title: { display: true, text: 'Accuracy %', color: '#9ca3af' },
                            min: 0,
                            max: 100
                        }
                    }
                }
            });
        }

        // Render error heatmap
        function renderErrorHeatmap(errors) {
            const container = document.getElementById('errorHeatmap');

            if (!errors || Object.keys(errors).length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: var(--text-tertiary); width: 100%;">
                        <p>No error data yet. Complete some tests!</p>
                    </div>
                `;
                return;
            }

            // Sort by count
            const sorted = Object.entries(errors).sort((a, b) => b[1] - a[1]).slice(0, 15);
            const maxCount = sorted[0][1];

            container.innerHTML = sorted.map(([char, count]) => {
                const ratio = count / maxCount;
                let level = 'low';
                if (ratio > 0.66) level = 'high';
                else if (ratio > 0.33) level = 'medium';

                const displayChar = char === ' ' ? '␣' : char;
                return `
                    <div class="error-key error-${level}" title="${count} errors">
                        ${displayChar}
                        <span class="count">${count}</span>
                    </div>
                `;
            }).join('');
        }

        // Initialize
        loadProfile();
    </script>
</body>

</html>