<style>
    .result-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
        width: 100%;
        text-align: center;
    }

    .result-header h1 {
        font-size: 2.2rem;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .result-header p {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .result-metrics {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        width: 100%;
        margin: 1.5rem 0;
    }

    .metric-row {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        padding: 1.25rem;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .metric-row-full {
        grid-column: span 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-direction: row;
        text-align: left;
        padding: 1rem 1.25rem;
    }

    .metric-row-full span {
        color: var(--text-secondary);
    }

    .metric-row-full strong {
        color: var(--text-primary);
    }

    .metric-title {
        font-size: 0.8rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .metric-val {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .back-link {
        color: var(--accent-secondary);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
        margin-top: 1rem;
    }

    .back-link:hover {
        color: var(--text-primary);
    }
</style>

<div class="glass-card result-card">
    <div class="result-header">
        <h1>Test Results</h1>
        <p>Record ID: <?php echo htmlspecialchars($result['id']); ?></p>
        <p><?php echo htmlspecialchars($result['timestamp']); ?></p>
    </div>

    <div class="result-metrics">
        <div class="metric-row">
            <div class="metric-title">Download</div>
            <div class="metric-val"><?php echo htmlspecialchars($result['dl']); ?> <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-secondary);">Mbps</span></div>
        </div>
        
        <div class="metric-row">
            <div class="metric-title">Upload</div>
            <div class="metric-val"><?php echo htmlspecialchars($result['ul']); ?> <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-secondary);">Mbps</span></div>
        </div>

        <div class="metric-row">
            <div class="metric-title">Ping</div>
            <div class="metric-val"><?php echo htmlspecialchars($result['ping']); ?> <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-secondary);">ms</span></div>
        </div>

        <div class="metric-row">
            <div class="metric-title">Jitter</div>
            <div class="metric-val"><?php echo htmlspecialchars($result['jitter']); ?> <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-secondary);">ms</span></div>
        </div>

        <div class="metric-row-full">
            <span>IP / ISP info</span>
            <strong><?php echo htmlspecialchars($result['ispinfo']); ?></strong>
        </div>

        <div class="metric-row-full">
            <span>Extra Info</span>
            <strong><?php echo htmlspecialchars($result['extra'] ?: 'None'); ?></strong>
        </div>

        <div class="metric-row-full" style="flex-direction: column; align-items: flex-start; text-align: left;">
            <span style="margin-bottom: 0.5rem;">User Agent</span>
            <strong style="font-size: 0.85rem; font-weight: 400; word-break: break-all; color: var(--text-secondary);"><?php echo htmlspecialchars($result['ua']); ?></strong>
        </div>
    </div>

    <a href="/" class="back-link" id="back-home-btn">Run Another Test</a>
</div>
