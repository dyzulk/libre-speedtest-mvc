<div class="page-header">
    <h1>Telemetry Dashboard</h1>
    <p>Real-time network speed telemetry statistics and recent test runs.</p>
</div>

<?php if (isset($summary)): ?>
    <div class="stats-summary-grid">
        <div class="summary-card">
            <span class="summary-label">Total Runs</span>
            <span class="summary-value"><?php echo number_format($summary['total_tests']); ?></span>
        </div>
        <div class="summary-card">
            <span class="summary-label">Avg Download</span>
            <span class="summary-value"><?php echo number_format($summary['avg_dl'], 2); ?> <span>Mbps</span></span>
        </div>
        <div class="summary-card">
            <span class="summary-label">Avg Upload</span>
            <span class="summary-value"><?php echo number_format($summary['avg_ul'], 2); ?> <span>Mbps</span></span>
        </div>
        <div class="summary-card">
            <span class="summary-label">Avg Latency</span>
            <span class="summary-value"><?php echo number_format($summary['avg_ping'], 1); ?> <span>ms</span></span>
        </div>
    </div>
<?php endif; ?>

<div class="vercel-card" style="padding: 0;">
    <div class="table-wrapper" style="border: none;">
        <table class="vercel-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 180px;">Timestamp</th>
                    <th>Client IP</th>
                    <th style="text-align: right; width: 120px;">Download</th>
                    <th style="text-align: right; width: 120px;">Upload</th>
                    <th style="text-align: right; width: 100px;">Ping</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary" style="padding: 3rem 1rem;">
                            No speedtest records found. Run some tests first!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td>
                                <a href="/results/<?php echo htmlspecialchars($row['id']); ?>" class="vercel-badge vercel-badge-id">
                                    #<?php echo htmlspecialchars($row['id']); ?>
                                </a>
                            </td>
                            <td class="font-mono text-secondary" style="white-space: nowrap;">
                                <?php echo htmlspecialchars($row['timestamp']); ?>
                            </td>
                            <td class="font-mono" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($row['ip']); ?>
                            </td>
                            <td class="font-mono" style="text-align: right; font-weight: 600;">
                                <?php echo htmlspecialchars($row['dl']); ?> <span style="font-weight: 400; color: var(--text-muted-admin); font-size: 0.8rem;">Mbps</span>
                            </td>
                            <td class="font-mono" style="text-align: right; font-weight: 600;">
                                <?php echo htmlspecialchars($row['ul']); ?> <span style="font-weight: 400; color: var(--text-muted-admin); font-size: 0.8rem;">Mbps</span>
                            </td>
                            <td class="font-mono" style="text-align: right; font-weight: 600;">
                                <?php echo htmlspecialchars($row['ping']); ?> <span style="font-weight: 400; color: var(--text-muted-admin); font-size: 0.8rem;">ms</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
