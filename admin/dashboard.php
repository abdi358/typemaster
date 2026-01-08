<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TypeMaster Admin</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Theme & Main Styles -->
    <script src="../assets/js/theme.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="admin-body">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">🛡️</div>
            <div class="sidebar-title">Admin Panel</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item active" onclick="showSection('dashboard')">
                <span>📊</span> Dashboard
            </div>
            <div class="nav-item" onclick="showSection('users')">
                <span>👥</span> Users
            </div>
            <div class="nav-item" onclick="showSection('content')">
                <span>📝</span> Content
            </div>
            <div class="nav-item" onclick="showSection('settings')">
                <span>⚙️</span> Settings
            </div>
            <div style="flex:1"></div>
            <div class="nav-item" onclick="window.location.href='../index.php'">
                <span>🏠</span> Back to App
            </div>
            <div class="nav-item danger" id="logoutBtn">
                <span>🚪</span> Logout
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">

        <!-- DASHBOARD SECTION -->
        <section id="dashboard" class="section-view active">
            <div class="admin-header">
                <h1 class="page-title">Overview</h1>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-secondary" onclick="loadStats()">Refresh</button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value" id="totalUsers">-</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Tests Completed</div>
                    <div class="stat-value" id="totalTests">-</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Active Today</div>
                    <div class="stat-value" id="activeToday">-</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">New Registrations</div>
                    <div class="stat-value" id="newUsers">-</div>
                </div>
            </div>

            <div class="data-table-container" style="padding: 1.5rem;">
                <h3 style="margin-bottom: 1rem;">Registrations (Last 7 Days)</h3>
                <canvas id="registrationChart" height="100"></canvas>
            </div>
        </section>

        <!-- USERS SECTION -->
        <section id="users" class="section-view">
            <div class="admin-header">
                <h1 class="page-title">User Management</h1>
            </div>

            <div class="admin-form">
                <input type="text" id="userSearch" class="form-input" placeholder="Search by username or email..."
                    onkeyup="searchUsers()">
            </div>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Tests</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </section>

        <!-- CONTENT SECTION -->
        <section id="content" class="section-view">
            <div class="admin-header">
                <h1 class="page-title">Content Manager</h1>
                <button class="btn btn-primary" onclick="showAddTextModal()">+ Add New Text</button>
            </div>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Preview</th>
                            <th>Difficulty</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="contentTableBody">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Add Text Form (Hidden by default) -->
            <div id="addTextForm" class="admin-form" style="display:none; flex-direction:column;">
                <h3>Add New Text</h3>
                <textarea id="textContent" class="form-input" rows="4" placeholder="Enter text content..."></textarea>
                <div style="display:flex; gap:1rem;">
                    <select id="textDifficulty" class="form-input">
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                    <select id="textCategory" class="form-input">
                        <option value="general">General</option>
                        <option value="code">Code</option>
                        <option value="story">Story</option>
                    </select>
                    <button class="btn btn-primary" onclick="submitNewText()">Save</button>
                    <button class="btn btn-secondary"
                        onclick="document.getElementById('addTextForm').style.display='none'">Cancel</button>
                </div>
            </div>
        </section>

        <!-- SETTINGS SECTION -->
        <section id="settings" class="section-view">
            <div class="admin-header">
                <h1 class="page-title">System Settings</h1>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Maintenance Mode</div>
                    <div style="display:flex; align-items:center; gap:1rem; margin-top:0.5rem;">
                        <input type="checkbox" id="maintenanceToggle"
                            onchange="toggleSetting('maintenance_mode', this.checked)">
                        <span id="maintenanceStatus">Off</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Allow Registrations</div>
                    <div style="display:flex; align-items:center; gap:1rem; margin-top:0.5rem;">
                        <input type="checkbox" id="regToggle"
                            onchange="toggleSetting('allow_registrations', this.checked)">
                        <span id="regStatus">On</span>
                    </div>
                </div>
            </div>

            <div class="admin-form" style="flex-direction:column;">
                <h3>Global Announcement</h3>
                <p style="font-size:0.875rem; color:var(--text-secondary); margin-bottom:1rem;">This message will appear
                    at the top of the app for all users.</p>
                <div style="display:flex; gap:1rem;">
                    <input type="text" id="globalAnnouncement" class="form-input"
                        placeholder="e.g. Server maintenance at 10 PM...">
                    <button class="btn btn-primary" onclick="saveAnnouncement()">Update</button>
                </div>
            </div>
        </section>

    </main>

    <script>
        // ==========================================
        // ADMIN LOGIC
        // ==========================================
        const API_URL = '../api/admin.php';

        // Navigation
        function showSection(id) {
            document.querySelectorAll('.section-view').forEach(el => el.classList.remove('active'));
            document.getElementById(id).classList.add('active');

            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            event.currentTarget.classList.add('active');

            if (id === 'dashboard') loadStats();
            if (id === 'users') loadUsers();
            if (id === 'content') loadContent();
            if (id === 'settings') loadSettings();
        }

        // Dashboard Stats
        async function loadStats() {
            try {
                const res = await fetch(`${API_URL}?action=stats`);
                const data = await res.json();
                if (!data.success) throw new Error(data.error);

                document.getElementById('totalUsers').textContent = data.stats.totalUsers;
                document.getElementById('totalTests').textContent = data.stats.totalTests;
                document.getElementById('activeToday').textContent = data.stats.activeToday;
                document.getElementById('newUsers').textContent = data.stats.newUsersToday;

                renderChart(data.stats.registrations);
            } catch (e) {
                if (e.message.includes('Forbidden')) window.location.href = '../login.php';
                console.error(e);
            }
        }

        let regChart = null;
        function renderChart(data) {
            const ctx = document.getElementById('registrationChart').getContext('2d');
            if (regChart) regChart.destroy();

            regChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'New Users',
                        data: data.map(d => d.count),
                        borderColor: '#6366f1',
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(99, 102, 241, 0.1)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        }

        // Users
        async function loadUsers(search = '') {
            const res = await fetch(`${API_URL}?action=get_users&search=${search}`);
            const data = await res.json();

            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = data.users.map(u => `
                <tr>
                    <td>
                        <div style="font-weight:600">${u.username}</div>
                        <div style="font-size:0.75rem; opacity:0.7">${u.email}</div>
                    </td>
                    <td><span class="badge ${u.role === 'admin' ? 'badge-admin' : 'badge-user'}">${u.role}</span></td>
                    <td><span class="badge ${u.is_banned == 1 ? 'badge-banned' : 'badge-active'}">${u.is_banned == 1 ? 'Banned' : 'Active'}</span></td>
                    <td>${u.total_tests || 0}</td>
                    <td>${new Date(u.created_at).toLocaleDateString()}</td>
                    <td>
                        ${u.is_banned == 1
                    ? `<button class="btn btn-sm btn-success" onclick="toggleBan(${u.id}, false)">Unban</button>`
                    : `<button class="btn btn-sm danger" style="color:var(--error-500)" onclick="toggleBan(${u.id}, true)">Ban</button>`
                }
                    </td>
                </tr>
            `).join('');
        }

        function searchUsers() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                loadUsers(document.getElementById('userSearch').value);
            }, 300);
        }

        async function toggleBan(userId, ban) {
            if (!confirm(`Are you sure you want to ${ban ? 'BAN' : 'UNBAN'} this user?`)) return;

            await fetch(`${API_URL}?action=${ban ? 'ban_user' : 'unban_user'}`, {
                method: 'POST',
                body: JSON.stringify({ user_id: userId })
            });
            loadUsers();
        }

        // Content
        async function loadContent() {
            const res = await fetch(`${API_URL}?action=get_texts`);
            const data = await res.json();

            const tbody = document.getElementById('contentTableBody');
            tbody.innerHTML = data.texts.map(t => `
                <tr>
                    <td>${t.id}</td>
                    <td style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${t.content}</td>
                    <td><span class="badge">${t.difficulty}</span></td>
                    <td>${t.category}</td>
                    <td>
                        <button class="btn btn-sm danger" style="color:var(--error-500)" onclick="deleteText(${t.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        function showAddTextModal() {
            document.getElementById('addTextForm').style.display = 'flex';
        }

        async function submitNewText() {
            const text = document.getElementById('textContent').value;
            if (!text) return;

            await fetch(`${API_URL}?action=add_text`, {
                method: 'POST',
                body: JSON.stringify({
                    content: text,
                    difficulty: document.getElementById('textDifficulty').value,
                    category: document.getElementById('textCategory').value
                })
            });

            document.getElementById('textContent').value = '';
            document.getElementById('addTextForm').style.display = 'none';
            loadContent();
        }

        async function deleteText(id) {
            if (!confirm('Delete this text?')) return;
            await fetch(`${API_URL}?action=delete_text`, {
                method: 'POST',
                body: JSON.stringify({ id })
            });
            loadContent();
        }

        // Settings
        async function loadSettings() {
            const res = await fetch(`${API_URL}?action=get_settings`);
            const data = await res.json();
            const s = data.settings;

            document.getElementById('maintenanceToggle').checked = s.maintenance_mode == '1';
            document.getElementById('maintenanceStatus').textContent = s.maintenance_mode == '1' ? 'On' : 'Off';

            document.getElementById('regToggle').checked = s.allow_registrations == '1';
            document.getElementById('regStatus').textContent = s.allow_registrations == '1' ? 'On' : 'Off';

            document.getElementById('globalAnnouncement').value = s.global_announcement || '';
        }

        async function toggleSetting(key, checked) {
            const val = checked ? '1' : '0';
            await fetch(`${API_URL}?action=update_setting`, {
                method: 'POST',
                body: JSON.stringify({ key, value: val })
            });
            loadSettings();
        }

        async function saveAnnouncement() {
            const val = document.getElementById('globalAnnouncement').value;
            await fetch(`${API_URL}?action=update_setting`, {
                method: 'POST',
                body: JSON.stringify({ key: 'global_announcement', value: val })
            });
            alert('Announcement updated!');
        }

        // Initialize
        loadStats();

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            await fetch('../api/auth.php?action=logout', { method: 'POST' });
            window.location.href = '../login.php';
        });
    </script>
</body>

</html>