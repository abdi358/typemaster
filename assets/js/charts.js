/**
 * TypeMaster - Charts & Analytics
 * Canvas-based chart rendering (no external libraries)
 * 
 * Charts:
 * - LineChart: WPM/Accuracy over time
 * - BarChart: Performance comparison
 * - HeatMap: Error character frequency
 */

'use strict';

// ============================================
// CHART CONFIGURATION
// ============================================

const ChartConfig = {
    colors: {
        primary: '#6366f1',
        primaryLight: 'rgba(99, 102, 241, 0.2)',
        accent: '#22d3ee',
        accentLight: 'rgba(34, 211, 238, 0.2)',
        success: '#22c55e',
        successLight: 'rgba(34, 197, 94, 0.2)',
        error: '#ef4444',
        errorLight: 'rgba(239, 68, 68, 0.2)',
        grid: 'rgba(148, 163, 184, 0.1)',
        text: '#94a3b8',
        textDark: '#64748b'
    },
    fonts: {
        family: 'Inter, -apple-system, sans-serif',
        size: 12,
        sizeLarge: 14
    },
    animation: {
        duration: 500,
        easing: (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2
    }
};

// ============================================
// BASE CHART CLASS
// ============================================

class BaseChart {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error(`Canvas not found: ${canvasId}`);
            return;
        }

        this.ctx = this.canvas.getContext('2d');
        this.options = {
            padding: { top: 20, right: 20, bottom: 40, left: 50 },
            ...options
        };

        this.data = [];
        this.animationProgress = 0;
        this.animationFrame = null;

        this.resize();
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        const rect = this.canvas.parentElement.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;

        this.canvas.width = rect.width * dpr;
        this.canvas.height = (this.options.height || 200) * dpr;
        this.canvas.style.width = `${rect.width}px`;
        this.canvas.style.height = `${this.options.height || 200}px`;

        this.ctx.scale(dpr, dpr);

        this.width = rect.width;
        this.height = this.options.height || 200;

        this.chartWidth = this.width - this.options.padding.left - this.options.padding.right;
        this.chartHeight = this.height - this.options.padding.top - this.options.padding.bottom;

        if (this.data.length > 0) {
            this.render();
        }
    }

    clear() {
        this.ctx.clearRect(0, 0, this.width, this.height);
    }

    animate(callback) {
        const startTime = performance.now();
        const duration = ChartConfig.animation.duration;

        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            this.animationProgress = Math.min(elapsed / duration, 1);
            this.animationProgress = ChartConfig.animation.easing(this.animationProgress);

            callback(this.animationProgress);

            if (elapsed < duration) {
                this.animationFrame = requestAnimationFrame(step);
            }
        };

        if (this.animationFrame) {
            cancelAnimationFrame(this.animationFrame);
        }

        this.animationFrame = requestAnimationFrame(step);
    }

    drawGrid(xLabels, yMax, yStep = 5) {
        const ctx = this.ctx;
        const { left, top } = this.options.padding;

        ctx.strokeStyle = ChartConfig.colors.grid;
        ctx.lineWidth = 1;
        ctx.font = `${ChartConfig.fonts.size}px ${ChartConfig.fonts.family}`;
        ctx.fillStyle = ChartConfig.colors.text;
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        // Y-axis grid lines and labels
        const ySteps = Math.ceil(yMax / yStep);
        for (let i = 0; i <= ySteps; i++) {
            const value = i * yStep;
            const y = top + this.chartHeight - (value / yMax) * this.chartHeight;

            // Grid line
            ctx.beginPath();
            ctx.moveTo(left, y);
            ctx.lineTo(left + this.chartWidth, y);
            ctx.stroke();

            // Label
            ctx.fillText(value.toString(), left - 10, y);
        }

        // X-axis labels
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';

        const labelStep = Math.ceil(xLabels.length / 10); // Max 10 labels
        xLabels.forEach((label, i) => {
            if (i % labelStep === 0 || i === xLabels.length - 1) {
                const x = left + (i / (xLabels.length - 1 || 1)) * this.chartWidth;
                ctx.fillText(label, x, top + this.chartHeight + 10);
            }
        });
    }

    render() {
        // To be implemented by subclasses
    }
}

// ============================================
// LINE CHART
// ============================================

class LineChart extends BaseChart {
    constructor(canvasId, options = {}) {
        super(canvasId, {
            showArea: true,
            showPoints: true,
            smooth: true,
            ...options
        });

        this.datasets = [];
    }

    setData(datasets) {
        // datasets: [{ label, data: [{x, y}], color, areaColor }]
        this.datasets = datasets;

        // Calculate bounds
        let maxY = 0;
        datasets.forEach(ds => {
            ds.data.forEach(point => {
                if (point.y > maxY) maxY = point.y;
            });
        });

        this.maxY = Math.ceil(maxY / 10) * 10 + 10; // Round up with buffer
        this.data = datasets;

        this.animate((progress) => this.render(progress));
    }

    render(progress = 1) {
        this.clear();

        if (this.datasets.length === 0 || this.datasets[0].data.length === 0) {
            this.drawEmptyState();
            return;
        }

        const ctx = this.ctx;
        const { left, top } = this.options.padding;

        // Draw grid
        const xLabels = this.datasets[0].data.map((_, i) => `${i}s`);
        const yStep = this.maxY <= 50 ? 10 : this.maxY <= 100 ? 20 : 50;
        this.drawGrid(xLabels, this.maxY, yStep);

        // Draw each dataset
        this.datasets.forEach(dataset => {
            const points = dataset.data.map((point, i) => ({
                x: left + (i / (dataset.data.length - 1 || 1)) * this.chartWidth,
                y: top + this.chartHeight - ((point.y * progress) / this.maxY) * this.chartHeight
            }));

            if (points.length < 2) return;

            // Draw area
            if (this.options.showArea) {
                ctx.beginPath();
                ctx.moveTo(points[0].x, top + this.chartHeight);

                if (this.options.smooth) {
                    this.drawSmoothLine(points, true);
                } else {
                    points.forEach(p => ctx.lineTo(p.x, p.y));
                }

                ctx.lineTo(points[points.length - 1].x, top + this.chartHeight);
                ctx.closePath();

                const gradient = ctx.createLinearGradient(0, top, 0, top + this.chartHeight);
                gradient.addColorStop(0, dataset.areaColor || ChartConfig.colors.primaryLight);
                gradient.addColorStop(1, 'transparent');
                ctx.fillStyle = gradient;
                ctx.fill();
            }

            // Draw line
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);

            if (this.options.smooth) {
                this.drawSmoothLine(points);
            } else {
                points.slice(1).forEach(p => ctx.lineTo(p.x, p.y));
            }

            ctx.strokeStyle = dataset.color || ChartConfig.colors.primary;
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();

            // Draw points
            if (this.options.showPoints) {
                points.forEach((p, i) => {
                    // Only draw some points for cleaner look
                    if (points.length <= 10 || i % Math.ceil(points.length / 10) === 0 || i === points.length - 1) {
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, 5, 0, Math.PI * 2);
                        ctx.fillStyle = '#fff';
                        ctx.fill();
                        ctx.strokeStyle = dataset.color || ChartConfig.colors.primary;
                        ctx.lineWidth = 2;
                        ctx.stroke();
                    }
                });
            }
        });

        // Draw legend
        this.drawLegend();
    }

    drawSmoothLine(points, closePath = false) {
        const ctx = this.ctx;

        for (let i = 0; i < points.length - 1; i++) {
            const p0 = points[i - 1] || points[i];
            const p1 = points[i];
            const p2 = points[i + 1];
            const p3 = points[i + 2] || p2;

            const cp1x = p1.x + (p2.x - p0.x) / 6;
            const cp1y = p1.y + (p2.y - p0.y) / 6;
            const cp2x = p2.x - (p3.x - p1.x) / 6;
            const cp2y = p2.y - (p3.y - p1.y) / 6;

            ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, p2.x, p2.y);
        }
    }

    drawLegend() {
        const ctx = this.ctx;
        const { left, top } = this.options.padding;

        ctx.font = `${ChartConfig.fonts.size}px ${ChartConfig.fonts.family}`;
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';

        let xOffset = left;

        this.datasets.forEach(dataset => {
            // Color indicator
            ctx.beginPath();
            ctx.arc(xOffset + 6, top - 8, 4, 0, Math.PI * 2);
            ctx.fillStyle = dataset.color || ChartConfig.colors.primary;
            ctx.fill();

            // Label
            ctx.fillStyle = ChartConfig.colors.text;
            ctx.fillText(dataset.label, xOffset + 16, top - 8);

            xOffset += ctx.measureText(dataset.label).width + 40;
        });
    }

    drawEmptyState() {
        const ctx = this.ctx;
        ctx.font = `${ChartConfig.fonts.sizeLarge}px ${ChartConfig.fonts.family}`;
        ctx.fillStyle = ChartConfig.colors.textDark;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('No data available', this.width / 2, this.height / 2);
    }
}

// ============================================
// BAR CHART
// ============================================

class BarChart extends BaseChart {
    constructor(canvasId, options = {}) {
        super(canvasId, {
            barWidth: 0.7,
            ...options
        });
    }

    setData(data) {
        // data: [{ label, value, color }]
        this.data = data;

        this.maxY = Math.max(...data.map(d => d.value)) * 1.2;

        this.animate((progress) => this.render(progress));
    }

    render(progress = 1) {
        this.clear();

        if (this.data.length === 0) {
            this.drawEmptyState();
            return;
        }

        const ctx = this.ctx;
        const { left, top } = this.options.padding;

        // Draw grid
        const xLabels = this.data.map(d => d.label);
        const yStep = this.maxY <= 50 ? 10 : this.maxY <= 100 ? 20 : 50;
        this.drawGrid(xLabels, this.maxY, yStep);

        // Draw bars
        const barSpacing = this.chartWidth / this.data.length;
        const barWidth = barSpacing * this.options.barWidth;

        this.data.forEach((item, i) => {
            const x = left + barSpacing * i + (barSpacing - barWidth) / 2;
            const barHeight = (item.value * progress / this.maxY) * this.chartHeight;
            const y = top + this.chartHeight - barHeight;

            // Draw bar with gradient
            const gradient = ctx.createLinearGradient(x, y, x, top + this.chartHeight);
            gradient.addColorStop(0, item.color || ChartConfig.colors.primary);
            gradient.addColorStop(1, item.colorEnd || ChartConfig.colors.accent);

            ctx.fillStyle = gradient;
            ctx.beginPath();
            this.roundedRect(x, y, barWidth, barHeight, 6);
            ctx.fill();

            // Draw value on top
            ctx.fillStyle = ChartConfig.colors.text;
            ctx.font = `bold ${ChartConfig.fonts.size}px ${ChartConfig.fonts.family}`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            ctx.fillText(Math.round(item.value * progress).toString(), x + barWidth / 2, y - 5);
        });
    }

    roundedRect(x, y, width, height, radius) {
        const ctx = this.ctx;

        if (height < radius * 2) {
            radius = height / 2;
        }

        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height);
        ctx.lineTo(x, y + height);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
    }

    drawEmptyState() {
        const ctx = this.ctx;
        ctx.font = `${ChartConfig.fonts.sizeLarge}px ${ChartConfig.fonts.family}`;
        ctx.fillStyle = ChartConfig.colors.textDark;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('No data available', this.width / 2, this.height / 2);
    }
}

// ============================================
// HEATMAP
// ============================================

class HeatMap {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Container not found: ${containerId}`);
            return;
        }

        this.options = {
            maxItems: 20,
            ...options
        };
    }

    setData(errorMap) {
        // errorMap: { character: count }
        if (!this.container) return;

        // Sort by count and take top items
        const sorted = Object.entries(errorMap)
            .sort((a, b) => b[1] - a[1])
            .slice(0, this.options.maxItems);

        if (sorted.length === 0) {
            this.container.innerHTML = `
                <div style="text-align: center; color: var(--text-tertiary); padding: 2rem;">
                    <p>No errors recorded! Great job! 🎉</p>
                </div>
            `;
            return;
        }

        const maxCount = sorted[0][1];

        this.container.innerHTML = `
            <div class="heatmap-container">
                ${sorted.map(([char, count]) => {
            const intensity = count / maxCount;
            const isHot = intensity > 0.5;
            const displayChar = char === ' ' ? '␣' : char;

            return `
                        <div class="heatmap-key ${isHot ? 'hot' : ''}" 
                             style="opacity: ${0.4 + intensity * 0.6};"
                             title="${count} errors">
                            ${this.escapeHtml(displayChar)}
                            <span class="count">${count}</span>
                        </div>
                    `;
        }).join('')}
            </div>
        `;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// ============================================
// STATS DISPLAY
// ============================================

class StatsDisplay {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
    }

    render(stats) {
        if (!this.container) return;

        this.container.innerHTML = `
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <div class="stat-value">${stats.totalTests || 0}</div>
                        <div class="stat-label">Total Tests</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚡</div>
                    <div class="stat-info">
                        <div class="stat-value">${stats.bestWpm || 0}</div>
                        <div class="stat-label">Best WPM</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-info">
                        <div class="stat-value">${Math.round(stats.avgWpm) || 0}</div>
                        <div class="stat-label">Average WPM</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-info">
                        <div class="stat-value">${stats.avgAccuracy || 0}%</div>
                        <div class="stat-label">Average Accuracy</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🔥</div>
                    <div class="stat-info">
                        <div class="stat-value">${stats.currentStreak || 0}</div>
                        <div class="stat-label">Current Streak</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-info">
                        <div class="stat-value">${this.formatTime(stats.totalTime)}</div>
                        <div class="stat-label">Time Typing</div>
                    </div>
                </div>
            </div>
        `;
    }

    formatTime(seconds) {
        if (!seconds) return '0m';

        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);

        if (hours > 0) {
            return `${hours}h ${minutes}m`;
        }
        return `${minutes}m`;
    }
}

// ============================================
// RESULTS CHART MANAGER
// ============================================

class ResultsChartManager {
    constructor() {
        this.wpmChart = null;
        this.accuracyChart = null;
        this.errorHeatmap = null;
    }

    init() {
        // Initialize charts when modal is shown
        this.wpmChart = new LineChart('wpmChart', { height: 180 });
        this.accuracyChart = new LineChart('accuracyChart', { height: 180 });
        this.errorHeatmap = new HeatMap('errorHeatmap');
    }

    updateWithResults(results) {
        if (!results.metrics || results.metrics.length === 0) {
            // No second-by-second data, create simple summary
            return;
        }

        // WPM over time
        if (this.wpmChart) {
            this.wpmChart.setData([{
                label: 'WPM',
                data: results.metrics.map(m => ({ x: m.second, y: m.wpm })),
                color: ChartConfig.colors.primary,
                areaColor: ChartConfig.colors.primaryLight
            }]);
        }

        // Accuracy over time
        if (this.accuracyChart) {
            this.accuracyChart.setData([{
                label: 'Accuracy',
                data: results.metrics.map(m => ({ x: m.second, y: m.accuracy })),
                color: ChartConfig.colors.success,
                areaColor: ChartConfig.colors.successLight
            }]);
        }

        // Error heatmap
        if (this.errorHeatmap && results.errorMap) {
            this.errorHeatmap.setData(results.errorMap);
        }
    }
}

// ============================================
// COMPARISON CHART
// ============================================

class ComparisonChart {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.chart = null;
    }

    init() {
        if (!this.container) return;

        // Create canvas
        const canvas = document.createElement('canvas');
        canvas.id = 'comparisonChartCanvas';
        this.container.appendChild(canvas);

        this.chart = new BarChart('comparisonChartCanvas', { height: 200 });
    }

    compare(currentResult, previousResults) {
        if (!this.chart) this.init();

        // Get last 5 results for comparison
        const recent = previousResults.slice(0, 5);

        const data = recent.map((result, i) => ({
            label: i === 0 ? 'Latest' : `-${i}`,
            value: result.wpm,
            color: i === 0 ? ChartConfig.colors.primary : ChartConfig.colors.accent,
            colorEnd: i === 0 ? ChartConfig.colors.accent : ChartConfig.colors.primary
        }));

        this.chart.setData(data);
    }
}

// ============================================
// EXPORT
// ============================================

window.LineChart = LineChart;
window.BarChart = BarChart;
window.HeatMap = HeatMap;
window.StatsDisplay = StatsDisplay;
window.ResultsChartManager = ResultsChartManager;
window.ComparisonChart = ComparisonChart;
