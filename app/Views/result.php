<div class="page-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Speedtest Result</h1>
    <p class="font-mono text-secondary">ID: #<?php echo htmlspecialchars($result['id']); ?> &bull; <?php echo htmlspecialchars($result['timestamp']); ?></p>
</div>

<div class="stats-summary-grid">
    <div class="summary-card">
        <span class="summary-label">Download</span>
        <span class="summary-value" style="color: #00ff00;"><?php echo htmlspecialchars($result['dl']); ?> <span>Mbps</span></span>
    </div>
    <div class="summary-card">
        <span class="summary-label">Upload</span>
        <span class="summary-value" style="color: #0070f3;"><?php echo htmlspecialchars($result['ul']); ?> <span>Mbps</span></span>
    </div>
    <div class="summary-card">
        <span class="summary-label">Ping</span>
        <span class="summary-value"><?php echo htmlspecialchars($result['ping']); ?> <span>ms</span></span>
    </div>
    <div class="summary-card">
        <span class="summary-label">Jitter</span>
        <span class="summary-value"><?php echo htmlspecialchars($result['jitter']); ?> <span>ms</span></span>
    </div>
</div>

<div class="vercel-card" style="padding: 0; margin-bottom: 2.5rem;">
    <div class="metric-panel-row" style="border-bottom: 1px solid var(--border-admin);">
        <span class="text-secondary">Client IP Address</span>
        <strong class="font-mono"><?php echo htmlspecialchars($result['ip']); ?></strong>
    </div>
    <div class="metric-panel-row" style="border-bottom: 1px solid var(--border-admin);">
        <span class="text-secondary">ISP Info</span>
        <strong><?php echo htmlspecialchars($result['ispinfo'] ?: 'Unknown'); ?></strong>
    </div>
    <div class="metric-panel-row" style="border-bottom: 1px solid var(--border-admin);">
        <span class="text-secondary">Extra Data</span>
        <strong class="font-mono"><?php echo htmlspecialchars($result['extra'] ?: 'None'); ?></strong>
    </div>
    <div class="metric-panel-row" style="flex-direction: column; align-items: flex-start; gap: 0.5rem; padding: 1.25rem 1rem;">
        <span class="text-secondary">User Agent / Client Identifier</span>
        <div class="code-block" style="width: 100%; box-sizing: border-box;"><?php echo htmlspecialchars($result['ua']); ?></div>
    </div>
</div>

<div class="text-center" style="margin-bottom: 3rem;">
    <a href="/stats" class="vercel-btn-secondary" style="margin-right: 0.5rem;">Back to Dashboard</a>
    <a href="/" class="vercel-btn">Run New Test</a>
</div>
