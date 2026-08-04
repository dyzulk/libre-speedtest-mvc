<!-- Classic LibreSpeed speedtest interface, migrated from from/index-classic.html -->
<!-- Override layout styles for classic white-background appearance -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<script type="text/javascript" src="/speedtest.js"></script>

<style>
    /* ------------------------------------------------------------------ */
    /* Layout override: neutralize the modern layout from main.php        */
    /* ------------------------------------------------------------------ */
    :root {
        --bg-color: #ffffff;
        --card-bg: transparent;
        --border-color: transparent;
        --text-primary: #202020;
        --text-secondary: #606060;
        --accent-primary: #6060aa;
        --accent-secondary: #6060ff;
        --gradient: none;
    }

    body {
        font-family: "Roboto", sans-serif;
        background-color: #ffffff;
        background-image: none;
        color: #202020;
        text-align: center;
    }

    header {
        border-bottom: none;
    }

    .logo {
        background: none;
        -webkit-background-clip: unset;
        -webkit-text-fill-color: #404040;
        color: #404040;
        font-family: "Roboto", sans-serif;
        text-transform: none;
        letter-spacing: normal;
    }

    nav a {
        color: #6060aa;
    }

    nav a:hover {
        color: #404040;
    }

    main {
        max-width: 100%;
        padding: 0 1.5rem;
    }

    footer {
        border-top: none;
        color: #808080;
    }

    /* ------------------------------------------------------------------ */
    /* Classic speedtest styles (preserved from original index-classic)    */
    /* ------------------------------------------------------------------ */
    h1 {
        color: #404040;
    }

    #loading {
        background-color: #ffffff;
        color: #404040;
        text-align: center;
    }

    span.load-circle {
        display: inline-block;
        width: 2em;
        height: 2em;
        vertical-align: middle;
        background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAMAAAD04JH5AAAAP1BMVEUAAAB2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZyFzwnAAAAFHRSTlMAEvRFvX406baecwbf0casimhSHyiwmqgAAADpSURBVHja7dbJbQMxAENRahnN5lkc//5rDRAkDeRgHszXgACJoKiIiIiIiIiIiIiIiIiIiIj4HHspsrpAVhdVVguzrA4OWc10WcEqpwKbnBo0OU1Q5NSpsoJFTgOecrrdEag85DRgktNqfoEdTjnd7hrEHMEJvmRUYJbTYk5Agy6nau6Abp5Cm7mDBtRdPi9gyKdU7w4p1fsLvyqs8hl4z9/w3n/Hmr9WoQ65lAU4d7lMYOz//QboRR5jBZibLMZdAR6O/Vfa1PlxNr3XdS3HzK/HVPRu/KnLs8iAOh993VpRRERERMT/fAN60wwWaVyWwAAAAABJRU5ErkJggg==");
        background-size: 2em 2em;
        margin-right: 0.5em;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(359deg);
        }
    }

    #start-stop-btn {
        display: inline-block;
        margin: 0 auto;
        color: #6060aa;
        background-color: rgba(0, 0, 0, 0);
        border: 0.15em solid #6060ff;
        padding: 0;
        font: inherit;
        border-radius: 0.3em;
        transition: all 0.3s;
        box-sizing: border-box;
        width: 8em;
        height: 3em;
        line-height: 2.7em;
        cursor: pointer;
        box-shadow: 0 0 0 rgba(0, 0, 0, 0.1), inset 0 0 0 rgba(0, 0, 0, 0.1);
    }

    #start-stop-btn:hover {
        box-shadow: 0 0 2em rgba(0, 0, 0, 0.1), inset 0 0 1em rgba(0, 0, 0, 0.1);
    }

    #start-stop-btn.running {
        background-color: #ff3030;
        border-color: #ff6060;
        color: #ffffff;
    }

    #start-stop-btn::before {
        content: "Start";
    }

    #start-stop-btn.running::before {
        content: "Abort";
    }

    #server-area {
        margin-top: 1em;
    }

    #server {
        font-size: 1em;
        padding: 0.2em;
    }

    #test {
        margin-top: 2em;
        margin-bottom: 12em;
    }

    div.test-area {
        display: inline-block;
        width: 16em;
        height: 12.5em;
        position: relative;
        box-sizing: border-box;
    }

    div.test-area-2 {
        display: inline-block;
        width: 14em;
        height: 7em;
        position: relative;
        box-sizing: border-box;
        text-align: center;
    }

    div.test-area div.test-name {
        position: absolute;
        top: 0.1em;
        left: 0;
        width: 100%;
        font-size: 1.4em;
        z-index: 9;
    }

    div.test-area-2 div.test-name {
        display: block;
        text-align: center;
        font-size: 1.4em;
    }

    div.test-area div.meter-text {
        position: absolute;
        bottom: 1.55em;
        left: 0;
        width: 100%;
        font-size: 2.5em;
        z-index: 9;
    }

    div.test-area-2 div.meter-text {
        display: inline-block;
        font-size: 2.5em;
    }

    div.meter-text:empty::before {
        content: "0.00";
    }

    div.test-area div.unit {
        position: absolute;
        bottom: 2em;
        left: 0;
        width: 100%;
        z-index: 9;
    }

    div.test-area-2 div.unit {
        display: inline-block;
    }

    div.test-area canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    div.test-group {
        display: block;
        margin: 0 auto;
    }

    #share-area {
        width: 95%;
        max-width: 40em;
        margin: 0 auto;
        margin-top: 2em;
    }

    #share-area > * {
        display: block;
        width: 100%;
        height: auto;
        margin: 0.25em 0;
    }

    #privacy-policy {
        position: fixed;
        top: 2em;
        bottom: 2em;
        left: 2em;
        right: 2em;
        overflow-y: auto;
        width: auto;
        height: auto;
        box-shadow: 0 0 3em 1em #000000;
        z-index: 999999;
        text-align: left;
        background-color: #ffffff;
        padding: 1em;
    }

    a.privacy {
        text-align: center;
        font-size: 0.8em;
        color: #808080;
        padding: 0 3em;
    }

    div.close-privacy-policy {
        width: 100%;
        text-align: center;
    }

    div.close-privacy-policy a.privacy {
        padding: 1em 3em;
    }

    @media all and (max-width: 40em) {
        body {
            font-size: 0.8em;
        }
    }

    div.visible {
        animation: fade-in 0.4s;
        display: block;
    }

    div.hidden {
        animation: fade-out 0.4s;
        display: none;
    }

    @keyframes fade-in {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    @keyframes fade-out {
        0% {
            display: block;
            opacity: 1;
        }
        100% {
            display: block;
            opacity: 0;
        }
    }

    @media all and (prefers-color-scheme: dark) {
        body,
        #loading {
            background: #202020;
            color: #f4f4f4;
            color-scheme: dark;
        }

        header {
            border-bottom-color: transparent;
        }

        .logo {
            -webkit-text-fill-color: #e0e0e0;
            color: #e0e0e0;
        }

        nav a {
            color: #9090ff;
        }

        h1 {
            color: #e0e0e0;
        }

        a {
            color: #9090ff;
        }

        #privacy-policy {
            background: #000000;
        }

        #results-img {
            filter: invert(1);
        }

        footer {
            color: #808080;
        }
    }
</style>

<h1><?php echo htmlspecialchars($title); ?></h1>

<div id="loading" class="visible">
    <p id="message"><span class="load-circle"></span>Selecting a server...</p>
</div>

<div id="test-wrapper" class="hidden">
    <button id="start-stop-btn" onclick="startStop()" aria-label="Start"></button><br>
    <a class="privacy" href="#" onclick="document.getElementById('privacy-policy').style.display=''">Privacy</a>

    <div id="server-area">
        Server: <select id="server" onchange="s.setSelectedServer(SPEEDTEST_SERVERS[this.value])"></select>
    </div>

    <div id="test">
        <div class="test-group">
            <div class="test-area-2">
                <div class="test-name">Ping</div>
                <div id="ping-text" class="meter-text" style="color: #aa6060"></div>
                <div class="unit">ms</div>
            </div>
            <div class="test-area-2">
                <div class="test-name">Jitter</div>
                <div id="jit-text" class="meter-text" style="color: #aa6060"></div>
                <div class="unit">ms</div>
            </div>
        </div>

        <div class="test-group">
            <div class="test-area">
                <div class="test-name">Download</div>
                <canvas id="dl-meter" class="meter"></canvas>
                <div id="dl-text" class="meter-text"></div>
                <div class="unit">Mbit/s</div>
            </div>
            <div class="test-area">
                <div class="test-name">Upload</div>
                <canvas id="ul-meter" class="meter"></canvas>
                <div id="ul-text" class="meter-text"></div>
                <div class="unit">Mbit/s</div>
            </div>
        </div>

        <div id="ip-area">
            <span id="ip"></span>
        </div>

        <div id="share-area" style="display: none">
            <h3>Share results</h3>
            <p>Test ID: <span id="test-id"></span></p>
            <input type="text" value="" id="results-url" readonly
                onclick="this.select();this.focus();this.select();document.execCommand('copy');alert('Link copied')">
            <img src="" id="results-img" alt="Speed test result">
        </div>
    </div>

    <a href="https://github.com/librespeed/speedtest">Source code</a>
</div>

<div id="privacy-policy" style="display: none">
    <h2>Privacy Policy</h2>
    <p>This HTML5 speed test server is configured with telemetry enabled.</p>

    <h4>What data we collect</h4>
    <p>
        At the end of the test, the following data is collected and stored:
    </p>
    <ul>
        <li>Test ID</li>
        <li>Time of testing</li>
        <li>Test results (download and upload speed, ping and jitter)</li>
        <li>IP address</li>
        <li>ISP information</li>
        <li>Approximate location (inferred from IP address, not GPS)</li>
        <li>User agent and browser locale</li>
        <li>Test log (contains no personal information)</li>
    </ul>

    <h4>How we use the data</h4>
    <p>
        Data collected through this service is used to:
    </p>
    <ul>
        <li>Allow sharing of test results (sharable image for forums, etc.)</li>
        <li>To improve the service offered to you (for instance, to detect problems on our side)</li>
    </ul>
    <p>No personal information is disclosed to third parties.</p>

    <h4>Your consent</h4>
    <p>By starting the test, you consent to the terms of this privacy policy.</p>

    <h4>Data removal</h4>
    <p>
        If you want to have your information deleted, you need to provide either the ID of the test or your IP
        address. This is the only way to identify your data, without this information we won't be able to comply
        with your request.<br><br>
        Contact this email address for all deletion requests:
        <?php if (!empty($admin_email)): ?>
            <a href="mailto:<?php echo htmlspecialchars($admin_email); ?>"><?php echo htmlspecialchars($admin_email); ?></a>.
        <?php else: ?>
            <a href="mailto:PUT@YOUR_EMAIL.HERE">TO BE FILLED BY DEVELOPER</a>.
        <?php endif; ?>
    </p>

    <br><br>
    <div class="close-privacy-policy">
        <a class="privacy" href="#" onclick="document.getElementById('privacy-policy').style.display='none'">Close</a>
    </div>
    <br>
</div>

<script>
    /**
     * Classic LibreSpeed speedtest interface logic.
     * Migrated from from/index-classic.html with ES6 modernization.
     */

    const I = (id) => document.getElementById(id);

    // Server list. Leave empty for standalone installation.
    const SPEEDTEST_SERVERS = [];

    // Initialize speedtest
    const s = new Speedtest();
    s.setParameter("telemetry_level", "basic");

    // Server auto-selection
    function initServers() {
        if (SPEEDTEST_SERVERS.length === 0) {
            // Standalone installation
            I("loading").className = "hidden";
            I("server-area").style.display = "none";
            I("test-wrapper").className = "visible";
            initUI();
        } else {
            // Multiple servers
            const noServersAvailable = () => {
                I("message").innerHTML = "No servers available";
            };

            const runServerSelect = () => {
                s.selectServer((server) => {
                    if (server !== null) {
                        I("loading").className = "hidden";

                        // Sort servers by country, then by city
                        const parseServerName = (name) => {
                            const parts = (name || "").split(",").map((p) => p.trim());
                            let country;
                            let city;

                            if (parts.length >= 3) {
                                country = parts[1];
                                city = parts[0];
                            } else if (parts.length === 2) {
                                country = parts[1];
                                city = parts[0];
                            } else {
                                country = parts[0];
                                city = "";
                            }

                            country = country.replace(/\s*\([^)]*\)\s*/g, "").trim();
                            return { country, city };
                        };

                        const indexed = SPEEDTEST_SERVERS.map((srv, idx) => ({ idx, server: srv }));
                        indexed.sort((a, b) => {
                            const pa = parseServerName(a.server.name);
                            const pb = parseServerName(b.server.name);
                            return pa.country.localeCompare(pb.country) || pa.city.localeCompare(pb.city);
                        });

                        // Populate server list for manual selection
                        for (const entry of indexed) {
                            if (entry.server.pingT === -1) continue;
                            const option = document.createElement("option");
                            option.value = entry.idx;
                            option.textContent = entry.server.name;
                            if (entry.server === server) option.selected = true;
                            I("server").appendChild(option);
                        }

                        // Show test UI
                        I("test-wrapper").className = "visible";
                        initUI();
                    } else {
                        noServersAvailable();
                    }
                });
            };

            if (typeof SPEEDTEST_SERVERS === "string") {
                s.loadServerList(SPEEDTEST_SERVERS, (servers) => {
                    if (servers === null) {
                        noServersAvailable();
                    } else {
                        SPEEDTEST_SERVERS = servers;
                        runServerSelect();
                    }
                });
            } else {
                s.addTestPoints(SPEEDTEST_SERVERS);
                runServerSelect();
            }
        }
    }

    const meterBk = /Trident.*rv:(\d+\.\d+)/i.test(navigator.userAgent) ? "#EAEAEA" : "#80808040";
    const dlColor = "#6060AA";
    const ulColor = "#616161";
    const progColor = meterBk;

    // Gauge drawing
    function drawMeter(c, amount, bk, fg, progress, prog) {
        const ctx = c.getContext("2d");
        const dp = window.devicePixelRatio || 1;
        const cw = c.clientWidth * dp;
        const ch = c.clientHeight * dp;
        const sizScale = ch * 0.0055;

        if (c.width === cw && c.height === ch) {
            ctx.clearRect(0, 0, cw, ch);
        } else {
            c.width = cw;
            c.height = ch;
        }

        ctx.beginPath();
        ctx.strokeStyle = bk;
        ctx.lineWidth = 12 * sizScale;
        ctx.arc(c.width / 2, c.height - 58 * sizScale, c.height / 1.8 - ctx.lineWidth, -Math.PI * 1.1, Math.PI * 0.1);
        ctx.stroke();

        ctx.beginPath();
        ctx.strokeStyle = fg;
        ctx.lineWidth = 12 * sizScale;
        ctx.arc(c.width / 2, c.height - 58 * sizScale, c.height / 1.8 - ctx.lineWidth, -Math.PI * 1.1, amount * Math.PI * 1.2 - Math.PI * 1.1);
        ctx.stroke();

        if (typeof progress !== "undefined") {
            ctx.fillStyle = prog;
            ctx.fillRect(c.width * 0.3, c.height - 16 * sizScale, c.width * 0.4 * progress, 4 * sizScale);
        }
    }

    function mbpsToAmount(s) {
        return 1 - (1 / (Math.pow(1.3, Math.sqrt(s))));
    }

    function format(d) {
        d = Number(d);
        if (d < 10) return d.toFixed(2);
        if (d < 100) return d.toFixed(1);
        return d.toFixed(0);
    }

    // UI state
    let uiData = null;

    function startStop() {
        if (s.getState() === 3) {
            // Speed test is running, abort
            s.abort();
            uiData = null;
            I("start-stop-btn").className = "";
            I("start-stop-btn").setAttribute("aria-label", "Start");
            I("server").disabled = false;
            initUI();
        } else {
            // Test is not running, begin
            I("start-stop-btn").className = "running";
            I("start-stop-btn").setAttribute("aria-label", "Abort");
            I("share-area").style.display = "none";
            I("server").disabled = true;

            s.onupdate = (data) => {
                uiData = data;
            };

            s.onend = (aborted) => {
                I("start-stop-btn").className = "";
                I("start-stop-btn").setAttribute("aria-label", "Start");
                I("server").disabled = false;
                updateUI(true);

                if (!aborted) {
                    try {
                        const testId = uiData.testId;
                        if (testId !== null) {
                            const shareURL = window.location.href.substring(0, window.location.href.lastIndexOf("/")) + "/results/?id=" + testId;
                            I("results-img").src = shareURL;
                            I("results-url").value = shareURL;
                            I("test-id").innerHTML = testId;
                            I("share-area").style.display = "";
                        }
                    } catch (e) {
                        // Silently ignore share errors
                    }
                }
            };

            s.start();
        }
    }

    // Read data sent back by the test and update the UI
    function updateUI(forced) {
        if (!forced && s.getState() !== 3) return;
        if (uiData === null) return;

        const status = uiData.testState;
        I("ip").textContent = uiData.clientIp;
        I("dl-text").textContent = (status === 1 && uiData.dlStatus === 0) ? "..." : format(uiData.dlStatus);
        drawMeter(I("dl-meter"), mbpsToAmount(Number(uiData.dlStatus * (status === 1 ? oscillate() : 1))), meterBk, dlColor, Number(uiData.dlProgress), progColor);
        I("ul-text").textContent = (status === 3 && uiData.ulStatus === 0) ? "..." : format(uiData.ulStatus);
        drawMeter(I("ul-meter"), mbpsToAmount(Number(uiData.ulStatus * (status === 3 ? oscillate() : 1))), meterBk, ulColor, Number(uiData.ulProgress), progColor);
        I("ping-text").textContent = format(uiData.pingStatus);
        I("jit-text").textContent = format(uiData.jitterStatus);
    }

    function oscillate() {
        return 1 + 0.02 * Math.sin(Date.now() / 100);
    }

    // Update the UI every frame
    function frame() {
        requestAnimationFrame(frame);
        updateUI();
    }
    frame();

    // Initialize UI
    function initUI() {
        drawMeter(I("dl-meter"), 0, meterBk, dlColor, 0);
        drawMeter(I("ul-meter"), 0, meterBk, ulColor, 0);
        I("dl-text").textContent = "";
        I("ul-text").textContent = "";
        I("ping-text").textContent = "";
        I("jit-text").textContent = "";
        I("ip").textContent = "";
    }

    // Auto-initialize on DOM ready
    document.addEventListener("DOMContentLoaded", () => {
        initServers();
    });
</script>