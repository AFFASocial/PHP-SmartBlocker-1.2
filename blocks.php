<?php
/**
 * Bot/Scraper Block - blocks.php
 * No external API calls — all checks run locally with zero overhead.
 *
 * S. Secret token bypass for trusted tools (autoposter etc.)
 * 1. Blocks empty / missing user agents
 * 2. Whitelists legitimate search engine bots (Googlebot, DuckDuckBot etc.)
 * 3. Blocks known bad bots and scrapers by user agent string
 * 4. Blocks outdated Chrome versions used as bot fingerprints
 * 5. Blocks WordPress probe paths (wp-login.php, wp-admin etc.) — site is not WordPress
 * 6. Drag-and-drop puzzle CAPTCHA via verify_overlay.php for all human visitors
 *
 * Logs BLOCKED and CAPTCHA events to alist.txt — one line per entry.
 * Add to .htaccess: php_value auto_prepend_file /home/yourusername/public_html/blocks.php
 */

// ---------------------------------------------------------------
// SESSION — must be started before any CAPTCHA checks
// ---------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------
// LOGGING — helpers defined early, rotate_log called AFTER early returns
// ---------------------------------------------------------------
$logFile = '/home/yourusername/public_html/alist.txt';
if (!is_writable(dirname($logFile)) && !@touch($logFile)) {
    $logFile = sys_get_temp_dir() . '/alist.txt';
}

function rotate_log(string $logFile): void {
    if (!file_exists($logFile) || filesize($logFile) < 1048576) {
        return; // Under 1MB — nothing to do
    }
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (count($lines) > 5000) {
        $trimmed = array_slice($lines, -5000); // Keep last 5000 lines
        file_put_contents($logFile, implode(PHP_EOL, $trimmed) . PHP_EOL, LOCK_EX);
    }
}

function writelog(string $logFile, string $status, string $reason, string $ip): void {
    $date    = date('Y-m-d H:i:s');
    $method  = $_SERVER['REQUEST_METHOD']  ?? '-';
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST']       ?? '-';
    $uri     = $_SERVER['REQUEST_URI']     ?? '-';
    $fullUrl = $scheme . '://' . $host . $uri;
    $ua      = $_SERVER['HTTP_USER_AGENT'] ?? 'NONE';
    $referer = $_SERVER['HTTP_REFERER']    ?? '-';
    $port    = $_SERVER['REMOTE_PORT']     ?? '-';

    $line = "[{$date}] {$status} | {$reason} | IP:{$ip} | {$method} {$fullUrl} | REF:{$referer} | PORT:{$port} | UA:{$ua}" . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

// ---------------------------------------------------------------
// HELPER — check if visitor already passed CAPTCHA
// Uses REMOTE_ADDR consistently — no proxy headers (not behind Cloudflare)
// ---------------------------------------------------------------
function already_verified(string $ip): bool {
    if (!empty($_SESSION['verified_human']) && ($_SESSION['verified_ip'] ?? '') === $ip) {
        return true;
    }
    if (!empty($_COOKIE['human_ticket']) && $_COOKIE['human_ticket'] === 'verified') {
        return true;
    }
    if (!empty($_COOKIE['human_ticket_mobile']) && $_COOKIE['human_ticket_mobile'] === 'verified') {
        return true;
    }
    return false;
}

// ---------------------------------------------------------------
// HELPER — serve the CAPTCHA challenge and stop execution
// ---------------------------------------------------------------
function serve_captcha(string $logFile, string $reason, string $ip): void {
    writelog($logFile, 'CAPTCHA', $reason, $ip);
    require __DIR__ . '/verify_overlay.php';
    exit;
}

// ---------------------------------------------------------------
// SECRET TOKEN BYPASS — for trusted automated tools e.g. autoposter
// Load from environment variable to avoid hardcoding secrets in source.
// Set on your server: SetEnv AUTOPOSTER_TOKEN your_secret_here  (in .htaccess or httpd.conf)
// Autoposter sends header: X-Autoposter-Token: <token> to bypass all checks silently
// ---------------------------------------------------------------
$secretToken = getenv('AUTOPOSTER_TOKEN') ?: '';
if ($secretToken !== '' && ($_SERVER['HTTP_X_AUTOPOSTER_TOKEN'] ?? '') === $secretToken) {
    return; // Trusted request — skip everything silently
}

// ---------------------------------------------------------------
// GET VISITOR IP — no Cloudflare, use REMOTE_ADDR directly
// ---------------------------------------------------------------
$userAgent   = $_SERVER['HTTP_USER_AGENT'] ?? '';
$visitorIp   = $_SERVER['REMOTE_ADDR']     ?? '0.0.0.0';
$requestPath = strtok($_SERVER['REQUEST_URI'] ?? '', '?');

// ---------------------------------------------------------------
// 1. BLOCK MISSING / EMPTY USER-AGENTS
//    Hard 403 — bots with no UA can't solve a CAPTCHA anyway
// ---------------------------------------------------------------
if (trim($userAgent) === '') {
    writelog($logFile, 'BLOCKED', 'EMPTY_UA', $visitorIp);
    http_response_code(403);
    echo '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1><p>Access Denied.</p></body></html>';
    exit;
}

// ---------------------------------------------------------------
// 2. WHITELIST LEGITIMATE SEARCH ENGINE BOTS
//    Let through silently — no logging needed for good bots
// ---------------------------------------------------------------
$allowedBots = [
    'googlebot', 'google-inspectiontool', 'adsbot-google', 'mediapartners-google',
    'bingbot', 'slurp', 'duckduckbot', 'facebot', 'facebookexternalhit',
    'twitterbot', 'linkedinbot', 'applebot',
];

$uaLower = strtolower($userAgent);

foreach ($allowedBots as $good) {
    if (strpos($uaLower, $good) !== false) {
        return; // Good bot — exit early, no log rotation needed
    }
}

// ---------------------------------------------------------------
// 3. BLOCK KNOWN SCRAPERS / BOTS BY USER-AGENT
//    Hard 403 — these are definitively non-human tools
// ---------------------------------------------------------------
$blockedAgents = [
    'curl', 'wget', 'libwww', 'python', 'java/', 'ruby/', 'perl/',
    'go-http', 'httpclient', 'okhttp', 'axios', 'node-fetch',
    'scrapy', 'mechanize', 'phantomjs', 'headlesschrome', 'selenium',
    'puppeteer', 'playwright', 'htmlunit', 'nutch',
    'ahrefsbot', 'semrushbot', 'dotbot', 'mj12bot', 'blexbot',
    'yandexbot', 'baiduspider', 'sogou', 'exabot', 'sistrix',
    'rogerbot', 'bytespider', 'petalbot', 'zgrab', 'masscan',
    'nmap', 'nikto', 'sqlmap', 'dirbuster', 'nuclei',
    'bot', 'crawler', 'spider', 'scraper', 'fetcher', 'harvest',
    'scan', 'exploit', 'inject', 'attack',
    // Passkey probing and authentication reconnaissance
    'passkey-domain-check',
];

foreach ($blockedAgents as $agent) {
    if (strpos($uaLower, $agent) !== false) {
        rotate_log($logFile);
        writelog($logFile, 'BLOCKED', 'BAD_UA:' . $agent, $visitorIp);
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1><p>Access Denied.</p></body></html>';
        exit;
    }
}

// ---------------------------------------------------------------
// 4. BLOCK OUTDATED CHROME VERSIONS — bot fingerprint
//    No real user runs Chrome below 110 in 2026.
//    Scrapers fake old Chrome UAs to avoid detection.
// ---------------------------------------------------------------
if (preg_match('/Chrome\/(\d+)\./', $userAgent, $matches)) {
    $chromeMajor = (int) $matches[1];
    if ($chromeMajor < 110) {
        rotate_log($logFile);
        writelog($logFile, 'BLOCKED', 'OLD_CHROME:' . $chromeMajor, $visitorIp);
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1><p>Access Denied.</p></body></html>';
        exit;
    }
}

// ---------------------------------------------------------------
// 5. BLOCK WORDPRESS PROBE PATHS — site is not WordPress
//    Returns your custom 404 page. Using 404 (not 403) tells scanners
//    the path simply doesn't exist, which discourages repeat probing.
// ---------------------------------------------------------------
$wpProbePaths = [
    '/wp-login.php', '/wp-admin', '/wp-admin/', '/xmlrpc.php',
    '/wp-config.php', '/wp-includes', '/wp-content',
    '/.well-known/passkey-endpoints', '/.well-known/passkey-domain-check',
];

foreach ($wpProbePaths as $probe) {
    if ($requestPath === $probe || strpos($requestPath, $probe . '/') === 0) {
        rotate_log($logFile);
        writelog($logFile, 'BLOCKED', 'WP_PROBE:' . $requestPath, $visitorIp);
        http_response_code(404);
        $custom404 = '/home/yourusername/public_html/404.shtml';
        if (file_exists($custom404)) {
            readfile($custom404);
        } else {
            echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>';
        }
        exit;
    }
}

// ---------------------------------------------------------------
// 6. DRAG-AND-DROP PUZZLE CAPTCHA — challenge ALL visitors on first visit
//    Once solved, the session + cookie lets them through freely.
//    Skip AJAX endpoints so background requests don't get interrupted.
// ---------------------------------------------------------------
$skipCaptcha = ['/includes/ajax/data/live.php', '/includes/ajax/chat/live.php'];

if (!already_verified($visitorIp) && !in_array($requestPath, $skipCaptcha, true)) {
    rotate_log($logFile);
    serve_captcha($logFile, 'CAPTCHA_ALL', $visitorIp);
}
