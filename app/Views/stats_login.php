<style>
    .login-card {
        width: 100%;
        max-width: 400px;
        margin: 2rem auto;
        text-align: center;
    }

    .login-header h1 {
        font-size: 1.8rem;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .login-header p {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        font-family: inherit;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent-secondary);
    }

    .btn-submit {
        width: 100%;
        padding: 0.75rem;
        background: var(--gradient);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: opacity 0.3s;
        margin-top: 1rem;
    }

    .btn-submit:hover {
        opacity: 0.9;
    }

    .error-msg {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #ef4444;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
</style>

<div class="glass-card login-card">
    <div class="login-header">
        <h1>Admin Access</h1>
        <p>Enter password to view telemetry statistics</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autofocus>
        </div>
        <button type="submit" class="btn-submit">Login</button>
    </form>
</div>
