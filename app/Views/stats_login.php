<div class="centered-container">
    <div class="centered-logo-wrapper">
        <div class="vercel-logo-large"></div>
    </div>
    
    <div class="centered-title-wrapper">
        <h1>Admin Access</h1>
        <p>Enter password to view telemetry statistics</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="vercel-alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="vercel-card">
        <form action="/login" method="POST" class="vercel-form">
            <div class="vercel-form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="vercel-input" placeholder="••••••••" required autofocus autocomplete="current-password">
            </div>
            <button type="submit" class="vercel-btn mt-4">Login</button>
        </form>
    </div>
</div>
