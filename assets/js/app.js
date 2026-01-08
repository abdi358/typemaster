/**
 * TypeMaster - Main Application JavaScript
 * Premium Typing Speed Test Engine
 * 
 * Modules:
 * - App: Main application controller
 * - TypingEngine: Core typing logic
 * - Timer: Time management
 * - MetricsCalculator: Real-time statistics
 * - UIManager: UI updates and animations
 * - SoundManager: Audio feedback
 * - APIClient: Server communication
 */

'use strict';

// ============================================
// CONFIGURATION
// ============================================

const CONFIG = {
    API_BASE: '/api',
    WORD_LENGTH_AVERAGE: 5, // Standard for WPM calculation
    METRICS_INTERVAL: 1000, // Update metrics every second
    SOUNDS: {
        KEYSTROKE: 'keystroke',
        ERROR: 'error',
        SUCCESS: 'success',
        ACHIEVEMENT: 'achievement'
    },
    TEST_MODES: {
        TIME: 'time',
        WORDS: 'words'
    },
    DIFFICULTIES: ['easy', 'medium', 'hard']
};

// ============================================
// SOUND MANAGER
// ============================================

const SoundManager = {
    enabled: true,
    audioContext: null,
    sounds: {},

    init() {
        if (this.initialized) return;
        this.initialized = true;

        this.enabled = JSON.parse(localStorage.getItem('soundEnabled') ?? 'true');
        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();

        // Initialize toggle
        const toggle = document.getElementById('soundToggle');
        if (toggle) {
            // Remove any existing listeners first to be safe (though cloneNode is better for that, this is fine with guard)
            // But since we can't remove anonymous functions, we stick to the guard.
            toggle.addEventListener('click', () => this.toggle());
        }
        this.updateUI();
    },

    toggle() {
        this.enabled = !this.enabled;
        localStorage.setItem('soundEnabled', JSON.stringify(this.enabled));
        this.updateUI();
        return this.enabled;
    },

    setEnabled(enabled) {
        this.enabled = enabled;
        localStorage.setItem('soundEnabled', JSON.stringify(enabled));
        this.updateUI();
    },

    updateUI() {
        const toggle = document.getElementById('soundToggle');
        const icon = document.getElementById('soundIcon');

        if (toggle) {
            toggle.classList.toggle('active', this.enabled);
        }

        if (icon) {
            icon.src = this.enabled ? 'assets/icons/sound-on.png' : 'assets/icons/sound-off.png';
        }
    },

    play(soundType) {
        if (!this.enabled) return;

        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);

            switch (soundType) {
                case CONFIG.SOUNDS.KEYSTROKE:
                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';
                    gainNode.gain.value = 0.03;
                    oscillator.start();
                    oscillator.stop(this.audioContext.currentTime + 0.03);
                    break;

                case CONFIG.SOUNDS.ERROR:
                    oscillator.frequency.value = 200;
                    oscillator.type = 'square';
                    gainNode.gain.value = 0.05;
                    oscillator.start();
                    oscillator.stop(this.audioContext.currentTime + 0.08);
                    break;

                case CONFIG.SOUNDS.SUCCESS:
                    oscillator.frequency.setValueAtTime(523.25, this.audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(659.25, this.audioContext.currentTime + 0.1);
                    oscillator.frequency.setValueAtTime(783.99, this.audioContext.currentTime + 0.2);
                    oscillator.type = 'sine';
                    gainNode.gain.value = 0.08;
                    oscillator.start();
                    oscillator.stop(this.audioContext.currentTime + 0.3);
                    break;

                case CONFIG.SOUNDS.ACHIEVEMENT:
                    const notes = [523.25, 659.25, 783.99, 1046.50];
                    notes.forEach((freq, i) => {
                        const osc = this.audioContext.createOscillator();
                        const gain = this.audioContext.createGain();
                        osc.connect(gain);
                        gain.connect(this.audioContext.destination);
                        osc.frequency.value = freq;
                        osc.type = 'sine';
                        gain.gain.value = 0.1;
                        osc.start(this.audioContext.currentTime + i * 0.1);
                        osc.stop(this.audioContext.currentTime + i * 0.1 + 0.15);
                    });
                    break;
            }
        } catch (e) {
            console.log('Sound playback failed:', e);
        }
    },

    // Resume audio context (required after user interaction)
    resume() {
        if (this.audioContext && this.audioContext.state === 'suspended') {
            this.audioContext.resume();
        }
    }
};

// ============================================
// API CLIENT
// ============================================

const APIClient = {
    async fetchText(options = {}) {
        const params = new URLSearchParams({
            difficulty: options.difficulty || 'easy',
            mode: options.mode || 'words',
            count: options.count || 50
        });

        try {
            const response = await fetch(`${CONFIG.API_BASE}/fetch_text.php?${params}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to fetch text');
            }

            return data;
        } catch (error) {
            console.error('Fetch text error:', error);
            // Return fallback text
            return {
                success: true,
                text: this.getFallbackText(options.difficulty || 'easy'),
                difficulty: options.difficulty || 'easy',
                mode: options.mode || 'words'
            };
        }
    },

    getFallbackText(difficulty) {
        const texts = {
            easy: 'the be to of and a in that have I it for not on with he as you do at this but his by from they we say her she or an will my one all would there their what so up out if about who get which go me when make can like time no just him know take people into year your good some could them see other than then now look only come its over think also back after use two how our work first well way even new want because any these give day most us',
            medium: 'The quick brown fox jumps over the lazy dog. Pack my box with five dozen liquor jugs. How vexingly quick daft zebras jump! The five boxing wizards jump quickly.',
            hard: 'function calculate() { return 42; } const API_URL = "https://example.com/api"; let total = price * quantity * 1.15;'
        };
        return texts[difficulty] || texts.easy;
    },

    async saveResult(resultData) {
        try {
            const response = await fetch(`${CONFIG.API_BASE}/save_result.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(resultData)
            });

            return await response.json();
        } catch (error) {
            console.error('Save result error:', error);
            return { success: false, error: error.message };
        }
    },

    async getLeaderboard(options = {}) {
        const params = new URLSearchParams({
            type: options.type || 'global',
            mode: options.mode || 'all',
            limit: options.limit || 50
        });

        try {
            const response = await fetch(`${CONFIG.API_BASE}/leaderboard.php?${params}`);
            return await response.json();
        } catch (error) {
            console.error('Leaderboard error:', error);
            return { success: false, error: error.message };
        }
    },

    async checkAuth() {
        try {
            const response = await fetch(`${CONFIG.API_BASE}/auth.php?action=check`);
            return await response.json();
        } catch (error) {
            console.error('Auth check error:', error);
            return { success: false, authenticated: false };
        }
    },

    async login(email, password) {
        try {
            const response = await fetch(`${CONFIG.API_BASE}/auth.php?action=login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            return await response.json();
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: error.message };
        }
    },

    async register(username, email, password) {
        try {
            const response = await fetch(`${CONFIG.API_BASE}/auth.php?action=register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, email, password })
            });
            return await response.json();
        } catch (error) {
            console.error('Register error:', error);
            return { success: false, error: error.message };
        }
    },

    async logout() {
        try {
            const response = await fetch(`${CONFIG.API_BASE}/auth.php?action=logout`, {
                method: 'POST'
            });
            return await response.json();
        } catch (error) {
            console.error('Logout error:', error);
            return { success: false, error: error.message };
        }
    }
};

// ============================================
// METRICS CALCULATOR
// ============================================

const MetricsCalculator = {
    calculate(state) {
        const { correctChars, incorrectChars, totalChars, elapsedTime, totalErrors } = state;

        // Prevent division by zero
        const timeInMinutes = Math.max(elapsedTime / 60, 0.001);

        // Gross WPM: (all characters / 5) / time in minutes
        const grossWPM = (totalChars / CONFIG.WORD_LENGTH_AVERAGE) / timeInMinutes;

        // Net WPM: Gross WPM - (errors / time in minutes)
        const netWPM = Math.max(0, grossWPM - (totalErrors / timeInMinutes));

        // Raw WPM: Characters typed / 5 / time
        const rawWPM = (correctChars / CONFIG.WORD_LENGTH_AVERAGE) / timeInMinutes;

        // CPM: Characters per minute
        const cpm = correctChars / timeInMinutes;

        // Accuracy: (correct / total) * 100
        const accuracy = totalChars > 0
            ? (correctChars / totalChars) * 100
            : 100;

        return {
            wpm: Math.round(rawWPM),
            rawWpm: Math.round(grossWPM),
            netWpm: Math.round(netWPM),
            cpm: Math.round(cpm),
            accuracy: Math.round(accuracy * 10) / 10,
            correctChars,
            incorrectChars,
            totalChars,
            totalErrors,
            elapsedTime: Math.round(elapsedTime)
        };
    },

    calculateProgress(state) {
        const { testMode, testValue, elapsedTime, currentIndex, totalLength } = state;

        if (testMode === CONFIG.TEST_MODES.TIME) {
            // Time-based: progress = elapsed / total
            return Math.min((elapsedTime / testValue) * 100, 100);
        } else {
            // Word-based: progress = current position / total
            return Math.min((currentIndex / totalLength) * 100, 100);
        }
    }
};

// ============================================
// UI MANAGER
// ============================================

const UIManager = {
    elements: {},

    init() {
        // Cache DOM elements
        this.elements = {
            // Typing area
            textDisplay: document.getElementById('textDisplay'),
            textContent: document.getElementById('textContent'),
            typingInput: document.getElementById('typingInput'),
            focusOverlay: document.getElementById('focusOverlay'),

            // Stats
            wpmDisplay: document.getElementById('wpmDisplay'),
            cpmDisplay: document.getElementById('cpmDisplay'),
            accuracyDisplay: document.getElementById('accuracyDisplay'),
            errorsDisplay: document.getElementById('errorsDisplay'),
            timerDisplay: document.getElementById('timerDisplay'),

            // Progress
            progressBar: document.getElementById('progressBar'),
            progressFill: document.getElementById('progressFill'),

            // Controls
            restartBtn: document.getElementById('restartBtn'),
            settingsGroups: document.querySelectorAll('.settings-group'),

            // Theme
            themeToggle: document.getElementById('themeToggle'),

            // Sound
            soundToggle: document.getElementById('soundToggle'),

            // Modal
            resultsModal: document.getElementById('resultsModal'),
            modalClose: document.getElementById('modalClose'),

            // Leaderboard
            leaderboardContainer: document.getElementById('leaderboardContainer'),
            leaderboardTabs: document.querySelectorAll('.leaderboard-tab'),
            leaderboardList: document.getElementById('leaderboardList'),

            // Auth elements
            userMenu: document.getElementById('userMenu'),
            loginBtn: document.getElementById('loginBtn'),
            username: document.getElementById('usernameDisplay'),

            // Achievements
            achievementToast: document.getElementById('achievementToast')
        };

        this.initTheme();
        this.initEventListeners();
    },

    initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        this.updateThemeIcon(savedTheme);
    },

    toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        this.updateThemeIcon(newTheme);
    },

    updateThemeIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if (icon) {
            icon.src = theme === 'dark' ? 'assets/icons/sun.png' : 'assets/icons/moon.png';
        }
    },

    initEventListeners() {
        // Theme toggle
        if (this.elements.themeToggle) {
            this.elements.themeToggle.addEventListener('click', () => this.toggleTheme());
        }

        // Sound toggle is handled by SoundManager.init()

        // Modal close
        if (this.elements.modalClose) {
            this.elements.modalClose.addEventListener('click', () => this.hideModal());
        }

        // Click outside modal to close
        if (this.elements.resultsModal) {
            this.elements.resultsModal.addEventListener('click', (e) => {
                if (e.target === this.elements.resultsModal) {
                    this.hideModal();
                }
            });
        }

        // Keyboard shortcut for modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.elements.resultsModal?.classList.contains('active')) {
                this.hideModal();
            }
        });
    },

    renderText(text) {
        const content = this.elements.textContent;
        if (!content) return;

        // Store text data for line management
        this.textData = {
            text: text,
            words: [],
            lines: [],
            charToLine: [], // Maps character index to line index
            currentLine: 0
        };

        // Split text into words with their character ranges
        let charIndex = 0;
        const words = text.split(' ');
        words.forEach((word, wordIndex) => {
            this.textData.words.push({
                word: word,
                startIndex: charIndex,
                endIndex: charIndex + word.length - 1
            });
            charIndex += word.length + 1; // +1 for space
        });

        // Create a wrapper for the 2-line display
        content.innerHTML = '';
        const wrapper = document.createElement('div');
        wrapper.className = 'text-content-wrapper';
        content.appendChild(wrapper);

        // Create container for text lines
        const linesContainer = document.createElement('div');
        linesContainer.className = 'text-lines-container';
        linesContainer.id = 'textLinesContainer';
        wrapper.appendChild(linesContainer);

        // Measure container width to determine how many words fit per line
        const containerWidth = content.parentElement?.offsetWidth || 800;
        const charWidth = 14; // Approximate width of monospace character at text-xl
        const maxCharsPerLine = Math.floor((containerWidth - 80) / charWidth); // 80px padding

        // Build lines based on word wrapping
        let currentLineChars = 0;
        let currentLineWords = [];
        let lineIndex = 0;

        this.textData.words.forEach((wordData, wordIndex) => {
            const wordLength = wordData.word.length + 1; // +1 for space

            if (currentLineChars + wordLength > maxCharsPerLine && currentLineWords.length > 0) {
                // Start new line
                this.textData.lines.push([...currentLineWords]);
                lineIndex++;
                currentLineWords = [];
                currentLineChars = 0;
            }

            currentLineWords.push(wordData);

            // Map each character to its line
            for (let i = wordData.startIndex; i <= wordData.endIndex; i++) {
                this.textData.charToLine[i] = lineIndex;
            }
            // Space after word (if not last word)
            if (wordIndex < this.textData.words.length - 1) {
                this.textData.charToLine[wordData.endIndex + 1] = lineIndex;
            }

            currentLineChars += wordLength;
        });

        // Add last line
        if (currentLineWords.length > 0) {
            this.textData.lines.push(currentLineWords);
        }

        // Render initial lines (show first 2)
        this.renderLines(linesContainer);

        // Set first character as current
        const firstChar = linesContainer.querySelector('.char');
        if (firstChar) {
            firstChar.classList.add('current');
        }
    },

    renderLines(container) {
        if (!container || !this.textData) return;

        container.innerHTML = '';
        const { lines, currentLine, text } = this.textData;

        // Show current line and next line
        const linesToShow = [currentLine, currentLine + 1];

        linesToShow.forEach((lineIdx, displayIdx) => {
            if (lineIdx >= lines.length) return;

            const lineDiv = document.createElement('div');
            lineDiv.className = `text-line line-${displayIdx + 1}`;
            lineDiv.dataset.lineIndex = lineIdx;

            const lineWords = lines[lineIdx];

            lineWords.forEach((wordData, wordIdx) => {
                // Add word characters
                for (let i = wordData.startIndex; i <= wordData.endIndex; i++) {
                    const char = text[i];
                    const span = document.createElement('span');
                    span.className = 'char';
                    span.textContent = char;
                    span.dataset.index = i;
                    span.dataset.char = char;
                    lineDiv.appendChild(span);
                }

                // Add space after word (except last word of last line)
                if (wordIdx < lineWords.length - 1 || lineIdx < lines.length - 1) {
                    const spaceIndex = wordData.endIndex + 1;
                    if (spaceIndex < text.length) {
                        const spaceSpan = document.createElement('span');
                        spaceSpan.className = 'char';
                        spaceSpan.textContent = '\u00A0'; // Non-breaking space
                        spaceSpan.dataset.index = spaceIndex;
                        spaceSpan.dataset.char = ' ';
                        lineDiv.appendChild(spaceSpan);
                    }
                }
            });

            container.appendChild(lineDiv);
        });
    },

    updateCharacter(index, state) {
        const container = document.getElementById('textLinesContainer');
        if (!container) return;

        const charEl = container.querySelector(`.char[data-index="${index}"]`);
        if (!charEl) return;

        // Remove all states
        charEl.classList.remove('current', 'correct', 'incorrect');

        // Add new state
        if (state) {
            charEl.classList.add(state);
        }
    },

    setCurrentCharacter(index) {
        if (!this.textData) return;

        const container = document.getElementById('textLinesContainer');
        if (!container) return;

        // Check if we need to scroll to next line
        const charLine = this.textData.charToLine[index];

        if (charLine !== undefined && charLine > this.textData.currentLine) {
            // Move to next line - scroll effect
            this.textData.currentLine = charLine;

            // Re-render lines with smooth transition
            container.style.opacity = '0';
            container.style.transform = 'translateY(-10px)';

            setTimeout(() => {
                this.renderLines(container);

                // Restore character states for visible characters
                const chars = container.querySelectorAll('.char');
                chars.forEach(charEl => {
                    const charIndex = parseInt(charEl.dataset.index);
                    const state = TypingEngine.state.charStates[charIndex];
                    if (state) {
                        charEl.classList.add(state);
                    }
                });

                // Set current character
                const currentChar = container.querySelector(`.char[data-index="${index}"]`);
                if (currentChar) {
                    currentChar.classList.add('current');
                }

                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 150);
        } else {
            // Same line - just update current marker
            const chars = container.querySelectorAll('.char');
            chars.forEach(char => char.classList.remove('current'));

            const currentChar = container.querySelector(`.char[data-index="${index}"]`);
            if (currentChar) {
                currentChar.classList.add('current');
            }
        }
    },

    updateStats(metrics) {
        if (this.elements.wpmDisplay) {
            this.animateNumber(this.elements.wpmDisplay, metrics.wpm);
        }
        if (this.elements.cpmDisplay) {
            this.animateNumber(this.elements.cpmDisplay, metrics.cpm);
        }
        if (this.elements.accuracyDisplay) {
            this.elements.accuracyDisplay.textContent = `${metrics.accuracy}%`;
        }
        if (this.elements.errorsDisplay) {
            this.animateNumber(this.elements.errorsDisplay, metrics.totalErrors);
        }
    },

    updateTimer(seconds, isCountdown = true) {
        if (!this.elements.timerDisplay) return;

        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        this.elements.timerDisplay.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;

        // Add warning colors
        this.elements.timerDisplay.classList.remove('warning', 'danger');
        if (isCountdown) {
            if (seconds <= 5) {
                this.elements.timerDisplay.classList.add('danger');
            } else if (seconds <= 10) {
                this.elements.timerDisplay.classList.add('warning');
            }
        }
    },

    updateProgress(percentage) {
        if (this.elements.progressFill) {
            this.elements.progressFill.style.width = `${percentage}%`;
        }
    },

    animateNumber(element, value) {
        const currentValue = parseInt(element.textContent) || 0;

        if (currentValue === value) return;

        element.textContent = value;
        element.classList.add('number-animate');

        setTimeout(() => {
            element.classList.remove('number-animate');
        }, 200);
    },

    showBlurOverlay() {
        if (this.elements.textDisplay) {
            this.elements.textDisplay.classList.add('blurred');
        }
    },

    hideBlurOverlay() {
        if (this.elements.textDisplay) {
            this.elements.textDisplay.classList.remove('blurred');
        }
    },

    focusInput() {
        if (this.elements.typingInput) {
            this.elements.typingInput.focus();
            this.hideBlurOverlay();
        }
    },

    showModal(results) {
        if (!this.elements.resultsModal) return;

        // Populate results
        const resultElements = {
            'resultWpm': results.wpm,
            'resultRawWpm': results.rawWpm || results.wpm,
            'resultAccuracy': `${results.accuracy}%`,
            'resultCpm': results.cpm,
            'resultTime': `${results.elapsedTime}s`,
            'resultErrors': results.totalErrors,
            'resultCorrect': results.correctChars,
            'resultIncorrect': results.incorrectChars
        };

        Object.entries(resultElements).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });

        // Show modal with animation
        this.elements.resultsModal.classList.add('active');

        // Play success sound
        SoundManager.play(CONFIG.SOUNDS.SUCCESS);
    },

    hideModal() {
        if (this.elements.resultsModal) {
            this.elements.resultsModal.classList.remove('active');
        }
    },

    showAchievement(achievement) {
        if (!this.elements.achievementToast) return;

        const item = document.createElement('div');
        item.className = 'achievement-item';
        item.innerHTML = `
            <div class="achievement-icon">${achievement.icon}</div>
            <div class="achievement-info">
                <h4>${achievement.name}</h4>
                <p>${achievement.description}</p>
            </div>
        `;

        this.elements.achievementToast.appendChild(item);
        SoundManager.play(CONFIG.SOUNDS.ACHIEVEMENT);

        // Remove after 5 seconds
        setTimeout(() => {
            item.style.animation = 'slideOutRight 0.3s ease-out forwards';
            setTimeout(() => item.remove(), 300);
        }, 5000);
    },

    renderLeaderboard(data) {
        if (!this.elements.leaderboardList) return;

        if (!data.leaderboard || data.leaderboard.length === 0) {
            this.elements.leaderboardList.innerHTML = `
                <div class="text-center" style="padding: 2rem; color: var(--text-tertiary);">
                    <p>No results yet. Be the first!</p>
                </div>
            `;
            return;
        }

        this.elements.leaderboardList.innerHTML = data.leaderboard.map((entry, index) => {
            const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
            const isCurrentUser = data.currentUser && entry.user?.id === data.currentUser?.id;

            return `
                <div class="leaderboard-item ${isCurrentUser ? 'current-user' : ''}">
                    <div class="leaderboard-rank ${rankClass}">${entry.rank}</div>
                    <div class="leaderboard-user">
                        <div class="leaderboard-username">${entry.user?.username || 'Guest'}</div>
                        <div class="leaderboard-meta">${entry.accuracy}% accuracy</div>
                    </div>
                    <div class="leaderboard-wpm">${entry.wpm} WPM</div>
                </div>
            `;
        }).join('');
    },

    updateAuthUI(user) {
        if (user && user.username) {
            // User is logged in
            if (this.elements.loginBtn) this.elements.loginBtn.classList.add('hidden');
            if (this.elements.userMenu) this.elements.userMenu.classList.remove('hidden');
            if (this.elements.username) this.elements.username.textContent = user.username;

            // Handle Admin Link
            const dropdown = document.getElementById('userDropdown');
            const adminLink = document.getElementById('adminLink');

            if (user.role === 'admin') {
                if (!adminLink && dropdown) {
                    const link = document.createElement('a');
                    link.href = 'admin/dashboard.php';
                    link.className = 'dropdown-item';
                    link.id = 'adminLink';
                    link.innerHTML = '<img src="assets/icons/admin.png" class="icon-sm" alt="Admin"> Admin Panel';

                    // Insert before the divider (hr)
                    const hr = dropdown.querySelector('hr');
                    if (hr) {
                        dropdown.insertBefore(link, hr);
                    } else {
                        dropdown.prepend(link);
                    }
                }
            } else {
                if (adminLink) adminLink.remove();
            }
        } else {
            // User is logged out
            if (this.elements.loginBtn) this.elements.loginBtn.classList.remove('hidden');
            if (this.elements.userMenu) this.elements.userMenu.classList.add('hidden');
        }
    },

    showLoading() {
        if (this.elements.textContent) {
            this.elements.textContent.innerHTML = `
                <div class="loading-spinner" style="margin: 2rem auto;"></div>
            `;
        }
    },

    showError(message) {
        if (this.elements.textContent) {
            this.elements.textContent.innerHTML = `
                <div style="text-align: center; color: var(--error-500); padding: 2rem;">
                    <p>${message}</p>
                    <button class="btn btn-primary" onclick="App.restart()" style="margin-top: 1rem;">Try Again</button>
                </div>
            `;
        }
    }
};

// ============================================
// TYPING ENGINE
// ============================================

const TypingEngine = {
    state: {
        text: '',
        currentIndex: 0,
        correctChars: 0,
        incorrectChars: 0,
        totalErrors: 0,
        errorMap: {},
        charStates: [], // 'correct', 'incorrect', null
        isActive: false,
        isComplete: false,
        strictMode: false
    },

    init(text, strictMode = false) {
        this.state = {
            text: text,
            currentIndex: 0,
            correctChars: 0,
            incorrectChars: 0,
            totalErrors: 0,
            errorMap: {},
            charStates: new Array(text.length).fill(null),
            isActive: false,
            isComplete: false,
            strictMode: strictMode
        };

        UIManager.renderText(text);

        return this;
    },

    handleInput(inputChar) {
        if (this.state.isComplete) return null;

        const { text, currentIndex, charStates, strictMode, errorMap } = this.state;
        const expectedChar = text[currentIndex];

        // Determine if correct
        const isCorrect = inputChar === expectedChar;

        if (isCorrect) {
            // Correct character
            charStates[currentIndex] = 'correct';
            this.state.correctChars++;
            UIManager.updateCharacter(currentIndex, 'correct');
            SoundManager.play(CONFIG.SOUNDS.KEYSTROKE);
        } else {
            // Incorrect character
            charStates[currentIndex] = 'incorrect';
            this.state.incorrectChars++;
            this.state.totalErrors++;

            // Track error for heatmap
            errorMap[expectedChar] = (errorMap[expectedChar] || 0) + 1;

            UIManager.updateCharacter(currentIndex, 'incorrect');
            SoundManager.play(CONFIG.SOUNDS.ERROR);
        }

        // Move to next character
        this.state.currentIndex++;

        // Check if complete
        if (this.state.currentIndex >= text.length) {
            this.state.isComplete = true;
            return { complete: true, metrics: this.getMetrics() };
        }

        // Update current character
        UIManager.setCurrentCharacter(this.state.currentIndex);

        return {
            complete: false,
            isCorrect,
            currentIndex: this.state.currentIndex,
            metrics: this.getMetrics()
        };
    },

    handleBackspace() {
        const { currentIndex, strictMode, charStates } = this.state;

        // Can't go back if at start
        if (currentIndex === 0) return null;

        // In strict mode, can't backspace on errors (optional behavior)
        // For now, allow backspace in all modes

        // Move back
        this.state.currentIndex--;
        const prevState = charStates[this.state.currentIndex];

        // Update counts
        if (prevState === 'correct') {
            this.state.correctChars--;
        } else if (prevState === 'incorrect') {
            this.state.incorrectChars--;
        }

        // Reset character state
        charStates[this.state.currentIndex] = null;
        UIManager.updateCharacter(this.state.currentIndex, null);
        UIManager.setCurrentCharacter(this.state.currentIndex);

        return {
            currentIndex: this.state.currentIndex,
            metrics: this.getMetrics()
        };
    },

    getMetrics() {
        return {
            correctChars: this.state.correctChars,
            incorrectChars: this.state.incorrectChars,
            totalChars: this.state.correctChars + this.state.incorrectChars,
            totalErrors: this.state.totalErrors,
            currentIndex: this.state.currentIndex,
            totalLength: this.state.text.length,
            errorMap: this.state.errorMap,
            isComplete: this.state.isComplete
        };
    },

    start() {
        this.state.isActive = true;
    },

    stop() {
        this.state.isActive = false;
    },

    reset() {
        if (this.state.text) {
            this.init(this.state.text, this.state.strictMode);
        }
    }
};

// ============================================
// TIMER
// ============================================

const Timer = {
    state: {
        startTime: null,
        elapsed: 0,
        remaining: 0,
        duration: 60,
        interval: null,
        isRunning: false,
        mode: 'countdown' // 'countdown' or 'countup'
    },
    callbacks: {
        onTick: null,
        onComplete: null
    },

    init(duration, mode = 'countdown') {
        this.state.duration = duration;
        this.state.mode = mode;
        this.state.elapsed = 0;
        this.state.remaining = duration;
        this.state.isRunning = false;

        UIManager.updateTimer(duration, mode === 'countdown');

        return this;
    },

    start(onTick, onComplete) {
        if (this.state.isRunning) return;

        this.state.isRunning = true;
        this.state.startTime = Date.now();
        this.callbacks.onTick = onTick;
        this.callbacks.onComplete = onComplete;

        this.state.interval = setInterval(() => this.tick(), 100);
    },

    tick() {
        const now = Date.now();
        this.state.elapsed = (now - this.state.startTime) / 1000;

        if (this.state.mode === 'countdown') {
            this.state.remaining = Math.max(0, this.state.duration - this.state.elapsed);
            UIManager.updateTimer(this.state.remaining, true);

            if (this.state.remaining <= 0) {
                this.complete();
            }
        } else {
            UIManager.updateTimer(this.state.elapsed, false);
        }

        if (this.callbacks.onTick) {
            this.callbacks.onTick(this.state.elapsed);
        }
    },

    complete() {
        this.stop();
        if (this.callbacks.onComplete) {
            this.callbacks.onComplete(this.state.elapsed);
        }
    },

    stop() {
        this.state.isRunning = false;
        if (this.state.interval) {
            clearInterval(this.state.interval);
            this.state.interval = null;
        }
    },

    getElapsed() {
        return this.state.elapsed;
    },

    reset() {
        this.stop();
        this.state.elapsed = 0;
        this.state.remaining = this.state.duration;
        UIManager.updateTimer(this.state.duration, this.state.mode === 'countdown');
    }
};

// ============================================
// MAIN APPLICATION
// ============================================

const App = {
    settings: {
        testMode: 'time',
        testValue: 60,
        difficulty: 'easy',
        textType: 'words',
        strictMode: false
    },

    state: {
        isTestStarted: false,
        isTestComplete: false,
        metricsHistory: [],
        currentUser: null
    },

    async init() {
        console.log('🚀 TypeMaster initialized');

        // Initialize modules
        SoundManager.init();
        UIManager.init();

        // Load settings from localStorage
        this.loadSettings();

        // Check authentication
        await this.checkAuth();

        // Load initial text
        await this.loadText();

        // Load leaderboard
        this.loadLeaderboard();

        // Set up event listeners
        this.setupEventListeners();

        // Focus input initially
        setTimeout(() => {
            if (!this.state.isTestStarted) {
                UIManager.showBlurOverlay();
            }
        }, 100);
    },

    loadSettings() {
        const saved = localStorage.getItem('typemaster_settings');
        if (saved) {
            try {
                const parsed = JSON.parse(saved);
                this.settings = { ...this.settings, ...parsed };
            } catch (e) {
                console.error('Failed to load settings:', e);
            }
        }

        // Update UI to reflect settings
        this.updateSettingsUI();
    },

    saveSettings() {
        localStorage.setItem('typemaster_settings', JSON.stringify(this.settings));
    },

    updateSettingsUI() {
        // Update mode selection
        document.querySelectorAll('[data-mode]').forEach(el => {
            el.classList.toggle('active', el.dataset.mode === this.settings.testMode);
        });

        // Update value selection
        document.querySelectorAll('[data-value]').forEach(el => {
            const isActive = parseInt(el.dataset.value) === this.settings.testValue &&
                el.closest('.settings-group')?.dataset.type === this.settings.testMode;
            el.classList.toggle('active', isActive);
        });

        // Update difficulty
        document.querySelectorAll('[data-difficulty]').forEach(el => {
            el.classList.toggle('active', el.dataset.difficulty === this.settings.difficulty);
        });

        // Update sound toggle
        const soundToggle = document.getElementById('soundToggle');
        if (soundToggle) {
            soundToggle.classList.toggle('active', SoundManager.enabled);
            // Icon updated by SoundManager.updateUI()
        }
    },

    setupEventListeners() {
        // Typing input
        const typingInput = document.getElementById('typingInput');
        if (typingInput) {
            // Focus on click anywhere in text display
            document.getElementById('textDisplay')?.addEventListener('click', () => {
                UIManager.focusInput();
                SoundManager.resume();
            });

            // Handle input
            typingInput.addEventListener('input', (e) => this.handleTypingInput(e));

            // Handle keydown for special keys
            typingInput.addEventListener('keydown', (e) => this.handleKeyDown(e));

            // Focus/blur handling
            typingInput.addEventListener('focus', () => UIManager.hideBlurOverlay());
            typingInput.addEventListener('blur', () => {
                if (!this.state.isTestComplete) {
                    UIManager.showBlurOverlay();
                }
            });
        }

        // Prevent paste
        document.getElementById('textDisplay')?.addEventListener('paste', (e) => e.preventDefault());

        // Settings: Test mode
        document.querySelectorAll('[data-mode]').forEach(el => {
            el.addEventListener('click', () => {
                this.settings.testMode = el.dataset.mode;
                this.updateSettingsUI();
                this.saveSettings();
                this.restart();
            });
        });

        // Settings: Test value (time or word count)
        document.querySelectorAll('[data-value]').forEach(el => {
            el.addEventListener('click', () => {
                const type = el.closest('.settings-group')?.dataset.type;
                if (type === this.settings.testMode) {
                    this.settings.testValue = parseInt(el.dataset.value);
                    this.updateSettingsUI();
                    this.saveSettings();
                    this.restart();
                }
            });
        });

        // Settings: Difficulty
        document.querySelectorAll('[data-difficulty]').forEach(el => {
            el.addEventListener('click', () => {
                this.settings.difficulty = el.dataset.difficulty;
                this.updateSettingsUI();
                this.saveSettings();
                this.restart();
            });
        });

        // Restart button
        document.getElementById('restartBtn')?.addEventListener('click', () => this.restart());

        // Next test button (in modal)
        document.getElementById('nextTestBtn')?.addEventListener('click', () => {
            UIManager.hideModal();
            this.restart();
        });

        // Leaderboard tabs
        document.querySelectorAll('.leaderboard-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.leaderboard-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                this.loadLeaderboard(tab.dataset.type);
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Tab to restart
            if (e.key === 'Tab' && (this.state.isTestComplete || !this.state.isTestStarted)) {
                e.preventDefault();
                this.restart();
            }

            // Escape to reset
            if (e.key === 'Escape') {
                this.restart();
            }
        });
    },

    handleTypingInput(e) {
        const input = e.target;
        const value = input.value;

        if (!value) return;

        // Get the last character typed
        const typedChar = value[value.length - 1];

        // Start test on first keystroke
        if (!this.state.isTestStarted) {
            this.startTest();
        }

        // Process the character
        const result = TypingEngine.handleInput(typedChar);

        if (result) {
            // Update metrics
            this.updateMetrics(result.metrics);

            // Check if complete
            if (result.complete) {
                this.completeTest();
            }
        }

        // Clear input (we track state internally)
        input.value = '';
    },

    handleKeyDown(e) {
        // Handle backspace
        if (e.key === 'Backspace') {
            e.preventDefault();

            if (this.state.isTestStarted && !this.state.isTestComplete) {
                const result = TypingEngine.handleBackspace();
                if (result) {
                    this.updateMetrics(result.metrics);
                }
            }
        }

        // Prevent tab from leaving input
        if (e.key === 'Tab') {
            e.preventDefault();
        }
    },

    startTest() {
        this.state.isTestStarted = true;
        this.state.metricsHistory = [];

        TypingEngine.start();

        // Start timer based on mode
        const isCountdown = this.settings.testMode === CONFIG.TEST_MODES.TIME;
        const duration = isCountdown ? this.settings.testValue : 3600; // Max 1 hour for word mode

        Timer.init(duration, isCountdown ? 'countdown' : 'countup');
        Timer.start(
            (elapsed) => this.onTimerTick(elapsed),
            () => this.completeTest()
        );
    },

    onTimerTick(elapsed) {
        // Record metrics every second
        if (Math.floor(elapsed) > this.state.metricsHistory.length) {
            const baseMetrics = TypingEngine.getMetrics();
            const calculated = MetricsCalculator.calculate({
                ...baseMetrics,
                elapsedTime: elapsed
            });

            this.state.metricsHistory.push({
                second: Math.floor(elapsed),
                wpm: calculated.wpm,
                accuracy: calculated.accuracy,
                characters: baseMetrics.totalChars,
                errors: baseMetrics.totalErrors
            });
        }

        // Update progress
        const progress = MetricsCalculator.calculateProgress({
            testMode: this.settings.testMode,
            testValue: this.settings.testValue,
            elapsedTime: elapsed,
            currentIndex: TypingEngine.state.currentIndex,
            totalLength: TypingEngine.state.text.length
        });

        UIManager.updateProgress(progress);
    },

    updateMetrics(engineMetrics) {
        const elapsed = Timer.getElapsed();
        const metrics = MetricsCalculator.calculate({
            ...engineMetrics,
            elapsedTime: elapsed
        });

        UIManager.updateStats(metrics);

        // Update progress for word mode
        if (this.settings.testMode === CONFIG.TEST_MODES.WORDS) {
            const progress = MetricsCalculator.calculateProgress({
                testMode: this.settings.testMode,
                testValue: this.settings.testValue,
                elapsedTime: elapsed,
                currentIndex: engineMetrics.currentIndex,
                totalLength: engineMetrics.totalLength
            });
            UIManager.updateProgress(progress);
        }
    },

    async completeTest() {
        this.state.isTestComplete = true;

        Timer.stop();
        TypingEngine.stop();

        const elapsed = Timer.getElapsed();
        const engineMetrics = TypingEngine.getMetrics();
        const finalMetrics = MetricsCalculator.calculate({
            ...engineMetrics,
            elapsedTime: elapsed
        });

        // Add additional info
        const results = {
            ...finalMetrics,
            rawWpm: finalMetrics.rawWpm || finalMetrics.wpm,
            testMode: this.settings.testMode,
            testValue: this.settings.testValue,
            difficulty: this.settings.difficulty,
            textType: this.settings.textType,
            metrics: this.state.metricsHistory
        };

        // Show results modal
        UIManager.showModal(results);

        // Save to server
        const saveResponse = await APIClient.saveResult({
            wpm: results.wpm,
            rawWpm: results.rawWpm,
            cpm: results.cpm,
            accuracy: results.accuracy,
            totalCharacters: results.totalChars,
            correctCharacters: results.correctChars,
            incorrectCharacters: results.incorrectChars,
            totalErrors: results.totalErrors,
            testDuration: results.elapsedTime,
            testMode: results.testMode,
            testValue: results.testValue,
            difficulty: results.difficulty,
            textType: results.textType,
            errorMap: engineMetrics.errorMap,
            metrics: results.metrics
        });

        if (saveResponse.success) {
            // Show new achievements
            if (saveResponse.newAchievements && saveResponse.newAchievements.length > 0) {
                saveResponse.newAchievements.forEach(achievement => {
                    UIManager.showAchievement(achievement);
                });
            }

            // Update personal best indicator
            if (saveResponse.isPersonalBest) {
                const pbIndicator = document.getElementById('personalBest');
                if (pbIndicator) {
                    pbIndicator.classList.remove('hidden');
                }
            }

            // Refresh leaderboard
            this.loadLeaderboard();
        }
    },

    async loadText() {
        UIManager.showLoading();

        // Calculate word count based on test mode
        // For time mode: estimate based on ~80 WPM (fast typist) + 50% buffer
        // This ensures enough text for the entire duration
        const wordCount = this.settings.testMode === CONFIG.TEST_MODES.WORDS
            ? this.settings.testValue
            : Math.ceil((this.settings.testValue / 60) * 80 * 1.5); // seconds * WPM * buffer

        const data = await APIClient.fetchText({
            difficulty: this.settings.difficulty,
            mode: this.settings.textType,
            count: wordCount
        });

        if (data.success && data.text) {
            TypingEngine.init(data.text, this.settings.strictMode);

            // Initialize timer display
            if (this.settings.testMode === CONFIG.TEST_MODES.TIME) {
                Timer.init(this.settings.testValue, 'countdown');
            } else {
                Timer.init(0, 'countup');
            }

            // Reset stats display
            UIManager.updateStats({
                wpm: 0,
                cpm: 0,
                accuracy: 100,
                totalErrors: 0
            });
            UIManager.updateProgress(0);
        } else {
            UIManager.showError('Failed to load text. Please try again.');
        }
    },

    async loadLeaderboard(type = 'daily') {
        const data = await APIClient.getLeaderboard({
            type: type,
            mode: this.settings.testMode,
            value: this.settings.testValue
        });

        if (data.success) {
            UIManager.renderLeaderboard(data);
        }
    },

    async checkAuth() {
        const result = await APIClient.checkAuth();
        if (result.success && result.authenticated) {
            this.state.currentUser = result.user;
            UIManager.updateAuthUI(result.user);

            // Apply user preferences
            if (result.user.preferences) {
                if (result.user.preferences.theme) {
                    document.documentElement.setAttribute('data-theme', result.user.preferences.theme);
                    UIManager.updateThemeIcon(result.user.preferences.theme);
                }
                if (typeof result.user.preferences.sound !== 'undefined') {
                    SoundManager.setEnabled(result.user.preferences.sound);
                }
            }
        }
    },

    async restart() {
        // Reset state
        this.state.isTestStarted = false;
        this.state.isTestComplete = false;

        // Hide modal if open
        UIManager.hideModal();

        // Reset timer
        Timer.reset();

        // Load new text
        await this.loadText();

        // Focus input
        setTimeout(() => {
            UIManager.focusInput();
        }, 100);
    }
};

// ============================================
// INITIALIZE ON DOM READY
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

// Export for external use
window.App = App;
window.SoundManager = SoundManager;
window.APIClient = APIClient;
