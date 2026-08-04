<style>
    .hero-section {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .hero-section h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-section p {
        color: var(--text-secondary);
        font-size: 1.1rem;
    }

    .speedtest-dashboard {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2rem;
        width: 100%;
    }

    .ip-display {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-color);
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-size: 0.9rem;
        color: var(--text-secondary);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        width: 100%;
    }

    .metric-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.3s, border-color 0.3s;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255, 255, 255, 0.15);
    }

    .metric-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .metric-unit {
        font-size: 0.9rem;
        color: var(--text-secondary);
        font-weight: 400;
        margin-left: 0.25rem;
    }

    .start-btn {
        background: var(--gradient);
        border: none;
        color: white;
        padding: 1rem 3rem;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 50px;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4);
    }

    .start-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 24px rgba(124, 58, 237, 0.6);
    }

    .status-text {
        font-size: 0.95rem;
        color: var(--text-secondary);
        min-height: 24px;
    }
</style>

<div class="glass-card speedtest-dashboard">
    <div class="hero-section">
        <h1>LibreSpeed MVC Engine</h1>
        <p>Speedtest interface powered by PHP MVC Routing</p>
    </div>

    <div class="ip-display" id="ip-display-container">
        Client IP: <span id="client-ip-placeholder">Fetching IP details...</span>
    </div>

    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-label">Download</div>
            <div class="metric-value" id="download-val">0.00<span class="metric-unit">Mbps</span></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Upload</div>
            <div class="metric-value" id="upload-val">0.00<span class="metric-unit">Mbps</span></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Ping</div>
            <div class="metric-value" id="ping-val">0<span class="metric-unit">ms</span></div>
        </div>
    </div>

    <button class="start-btn" id="start-test-btn">Start Test</button>
    
    <div class="status-text" id="status-display">Ready to test MVC routes</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ipPlaceholder = document.getElementById('client-ip-placeholder');
        const startBtn = document.getElementById('start-test-btn');
        const statusDisplay = document.getElementById('status-display');
        const downloadVal = document.getElementById('download-val');
        const uploadVal = document.getElementById('upload-val');
        const pingVal = document.getElementById('ping-val');

        // Fetch client IP on load
        fetch('/getIP?cors=1')
            .then(res => res.json())
            .then(data => {
                ipPlaceholder.textContent = data.processedString;
            })
            .catch(() => {
                ipPlaceholder.textContent = 'Unable to fetch IP';
            });

        startBtn.addEventListener('click', () => {
            if (startBtn.disabled) return;
            startBtn.disabled = true;
            
            // Simulating test process hitting actual controller routes
            statusDisplay.textContent = 'Checking latency (/empty)...';
            const startTime = Date.now();
            
            fetch('/empty?cors=1')
                .then(() => {
                    const latency = Date.now() - startTime;
                    pingVal.innerHTML = `${latency}<span class="metric-unit">ms</span>`;
                    
                    statusDisplay.textContent = 'Simulating download stream (/garbage)...';
                    return fetch('/garbage?ckSize=4&cors=1');
                })
                .then(res => res.blob())
                .then(blob => {
                    const downloadSpeed = ((blob.size * 8) / (1024 * 1024) / 0.5).toFixed(2);
                    downloadVal.innerHTML = `${downloadSpeed}<span class="metric-unit">Mbps</span>`;
                    
                    statusDisplay.textContent = 'Simulating upload stream (/empty)...';
                    return fetch('/empty?cors=1', {
                        method: 'POST',
                        body: new Blob([new Uint8Array(2 * 1024 * 1024)]) // 2MB upload data
                    });
                })
                .then(() => {
                    const uploadSpeed = (16.5 + Math.random() * 5).toFixed(2);
                    uploadVal.innerHTML = `${uploadSpeed}<span class="metric-unit">Mbps</span>`;
                    
                    statusDisplay.textContent = 'Saving telemetry stats (/telemetry)...';
                    
                    const fd = new FormData();
                    fd.append('ispinfo', JSON.stringify({ ip: ipPlaceholder.textContent }));
                    fd.append('extra', 'MVC Test Run');
                    fd.append('dl', parseFloat(downloadVal.textContent).toString());
                    fd.append('ul', parseFloat(uploadVal.textContent).toString());
                    fd.append('ping', parseFloat(pingVal.textContent).toString());
                    fd.append('jitter', '1.2');
                    fd.append('log', 'Simulated MVC transaction log details');
                    
                    return fetch('/telemetry', {
                        method: 'POST',
                        body: fd
                    });
                })
                .then(res => res.text())
                .then(text => {
                    const id = text.replace('id ', '').trim();
                    statusDisplay.innerHTML = `Test complete! Saved ID: <a href="/results/${id}" style="color: var(--accent-secondary);">${id}</a>`;
                    startBtn.disabled = false;
                })
                .catch(err => {
                    statusDisplay.textContent = 'Error executing MVC route test: ' + err.message;
                    startBtn.disabled = false;
                });
        });
    });
</script>
