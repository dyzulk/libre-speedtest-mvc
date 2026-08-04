<style>
    .stats-card {
        width: 100%;
        max-width: 900px;
    }

    .stats-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stats-header h1 {
        font-size: 2rem;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    .stats-header p {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.9rem;
    }

    th {
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    td {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        color: var(--text-primary);
    }

    tr:hover td {
        background: rgba(255, 255, 255, 0.01);
    }

    .badge {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        color: var(--accent-secondary);
        text-decoration: none;
    }

    .badge:hover {
        background: var(--gradient);
        color: white;
        border-color: transparent;
    }
</style>

<div class="glass-card stats-card">
    <div class="stats-header">
        <div>
            <h1>Telemetry Logs</h1>
            <p>List of recent speedtest executions</p>
        </div>
        <div>
            <a href="/logout" class="badge" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #ef4444;">
                Logout
            </a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Timestamp</th>
                    <th>Client IP</th>
                    <th>Download</th>
                    <th>Upload</th>
                    <th>Ping</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No speedtest records found. Run some tests first!</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td>
                                <a href="/results/<?php echo htmlspecialchars($row['id']); ?>" class="badge">
                                    #<?php echo htmlspecialchars($row['id']); ?>
                                </a>
                            </td>
                            <td style="white-space: nowrap; color: var(--text-secondary);"><?php echo htmlspecialchars($row['timestamp']); ?></td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($row['ip']); ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($row['dl']); ?></strong> Mbps</td>
                            <td><strong><?php echo htmlspecialchars($row['ul']); ?></strong> Mbps</td>
                            <td><strong><?php echo htmlspecialchars($row['ping']); ?></strong> ms</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
