<style>
    .error-card {
        width: 100%;
        max-width: 500px;
        margin: 4rem auto;
        text-align: center;
    }

    .error-icon {
        font-size: 3rem;
        color: #ef4444;
        margin-bottom: 1.5rem;
    }

    .error-card h1 {
        font-size: 1.8rem;
        color: var(--text-primary);
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .error-card p {
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .btn-back {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 600;
        transition: background 0.3s;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.08);
    }
</style>

<div class="glass-card error-card">
    <div class="error-icon">⚠️</div>
    <h1>Access Restricted</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="/" class="btn-back">Go to Homepage</a>
</div>
