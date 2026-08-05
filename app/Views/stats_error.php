<div class="centered-container" style="max-width: 500px;">
    <div class="centered-logo-wrapper">
        <div class="vercel-logo-large" style="border-bottom-color: var(--error-admin);"></div>
    </div>
    
    <div class="centered-title-wrapper">
        <h1 style="color: var(--error-admin);">Configuration Required</h1>
        <p style="margin-top: 0.5rem; line-height: 1.5; font-size: 0.95rem;">
            <?php echo htmlspecialchars($message); ?>
        </p>
    </div>

    <div class="vercel-card text-center">
        <p class="text-secondary" style="font-size: 0.85rem; margin-bottom: 1.5rem; line-height: 1.5;">
            To enable access to the admin statistics page, update the following variable in your workspace <code class="font-mono" style="background: rgba(255,255,255,0.08); padding: 0.1rem 0.3rem; border-radius: 3px;">.env</code> file:
        </p>
        <div class="code-block" style="margin-bottom: 1.5rem;">SPEEDTEST_PASSWORD=your_secure_password</div>
        
        <a href="/" class="vercel-btn">Go to Homepage</a>
    </div>
</div>
