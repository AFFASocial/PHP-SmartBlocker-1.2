# PHP SmartBlocker 1.2

[![PHP SmartBlocker](https://github.com/AFFASocial/PHP-SmartBlocker-1.0/raw/main/SB.png)](https://github.com/AFFASocial/PHP-SmartBlocker-1.0/blob/main/SB.png)

# PHP SmartBlocker v1.2

A self-hosted, zero-dependency bot protection and CAPTCHA system for any PHP website. No external APIs, no third-party services, no ongoing cost. Runs entirely on your server using two PHP files.

PHP SmartBlocker sits in front of every page on your site and intercepts all incoming traffic before it reaches your application. Legitimate visitors pass through after a one-time puzzle challenge. Everything else is stopped at the door.

All checks run in PHP memory with no database queries or external calls, making the overhead negligible even on shared hosting.

---

## What It Protects Against

**Bot and scraper traffic**
Blocks known commercial crawlers, SEO tools, and content scrapers including DotBot, AhrefsBot, SemrushBot, YandexBot, BaiduSpider, ByteSpider, PetalBot, Sogou, MJ12Bot, and dozens more. These tools harvest your content to build commercial products and databases without your consent.

**AI training crawlers**
Blocks AI training bots including GPTBot (OpenAI), ClaudeBot (Anthropic), Meta-ExternalAgent (Meta), ByteSpider (ByteDance), and PerplexityBot that scrape your content to train or power commercial AI products — without your permission or compensation.

**Content theft and unauthorised harvesting**
Prevents your posts, member profiles, images, and data from being scraped and republished or sold elsewhere. All visitor traffic must clear the CAPTCHA challenge before any page content is served.

**Fake and bot-generated analytics traffic**
Because bots are stopped before they reach your pages, they cannot inflate your Google Analytics visit counts, distort your traffic reports, or skew your engagement metrics. Your analytics reflect real human visitors only.

**Automated spam registration attempts**
Blocks scripted tools and bot farms that attempt to flood your registration forms with fake accounts for spam, SEO link building, or account resale. Bots cannot solve the jigsaw puzzle required to proceed.

**WordPress credential attacks and path probing**
Blocks automated scanners probing for WordPress vulnerabilities — `/wp-login.php`, `/wp-admin`, `/xmlrpc.php`, `/wp-config.php` — even on non-WordPress sites. Returns a 404 to discourage repeat attempts.

**Server credential harvesting**
Blocks automated probes targeting sensitive server files including `.env`, `.aws/credentials`, configuration files, and other paths commonly targeted by reconnaissance scripts.

**SQL injection and exploit attempts**
Catches requests containing malicious URL parameters and exploit strings before they reach your application or database.

**Outdated browser fingerprints used by bot farms**
No real user runs Chrome 51 or Chrome 89 in 2026. Scrapers and bot farms fake old Chrome versions to appear human. SmartBlocker blocks any Chrome version below 110 with a hard 403.

**Headless browser automation tools**
Blocks tools commonly used for automated scraping and form abuse including Selenium, Puppeteer, Playwright, PhantomJS, and HeadlessChrome.

**Volumetric traffic bursts**
The CAPTCHA wall means burst traffic from coordinated IP clusters hits a lightweight puzzle page rather than your application and database, protecting your server resources and uptime.

**Empty and missing user agents**
Any request with no user agent is blocked immediately with a 403. Legitimate browsers always send a user agent string.

**What passes through**
Legitimate search engine crawlers including Googlebot, Bingbot, DuckDuckBot, FacebookExternalHit, Twitterbot, LinkedInBot, and AppleBot are whitelisted and pass through silently without ever seeing the CAPTCHA, so your search rankings are never affected.

Real human visitors solve the jigsaw puzzle once per 24 hours, then browse freely for the rest of the day with no further interruption.

---

## How It Works — Layer by Layer

### Secret Token Bypass ( Only applies if you are using my Sngine Python/Anaconda Autoposter ) otherwise just ignore this as I have not shared the autoposter yet.
Trusted automated tools (such as an autoposter) can bypass all checks by sending a custom HTTP header containing a secret token. The token is loaded from a server environment variable — never hardcoded in source. Any request that presents the correct token is passed through silently without logging.

### Layer 1 — Empty User Agent Block
Requests with a missing or empty User-Agent header receive an immediate `403 Forbidden` response. Bots that send no UA cannot solve a CAPTCHA anyway, so no challenge is served — they are simply denied.

### Layer 2 — Good Bot Whitelist
Legitimate search engine crawlers and trusted performance testing tools are identified by their User-Agent string and passed through silently without logging. Whitelisted bots include Googlebot, Google Inspection Tool, AdSbot, Bingbot, DuckDuckBot, Facebot, FacebookExternalHit, Twitterbot, LinkedInBot, AppleBot, Slurp, and GTmetrix.

### Layer 3 — Bad Bot and Scraper Block
A curated list of known bad User-Agent strings is checked. Any match receives an immediate `403 Forbidden` response and is logged. This covers a wide range of tools including curl, wget, Python, Java, Ruby, Perl, Go HTTP, Scrapy, Selenium, Puppeteer, Playwright, PhantomJS, HeadlessChrome, AhrefsBot, SemrushBot, DotBot, MJ12Bot, YandexBot, BaiduSpider, ByteSpider, PetalBot, Sogou, Masscan, Nikto, SQLMap, and many more.

### Layer 4 — Outdated Chrome Version Block
No real user runs Chrome below version 110 in 2026. Scrapers and bot farms commonly fake outdated Chrome User-Agent strings to disguise themselves as real browsers. Any Chrome version below 110 receives an immediate `403 Forbidden` response and is logged.

### Layer 5 — WordPress Probe Block
Sites that do not run WordPress are probed constantly by automated scanners looking for vulnerable login pages. Requests for `/wp-login.php`, `/wp-admin`, `/xmlrpc.php`, `/wp-config.php`, `/wp-includes`, and `/wp-content` are returned as `404 Not Found` using your custom 404 page. A `404` is used rather than `403` because it tells scanners the path does not exist, which discourages repeat probing.

### Layer 6 — Jigsaw Puzzle CAPTCHA
Any visitor that passes all the above checks but has not yet been verified as human is served the jigsaw CAPTCHA challenge. The challenge consists of a randomly generated scene image split across a 5-column board with 3 pieces removed. The visitor must drag all 3 jigsaw pieces into their correct slots in the correct order to proceed.

Once solved, a 24-hour cookie and session flag mark the visitor as verified. They will not see the CAPTCHA again until the cookie expires.

---

## The Jigsaw CAPTCHA — In Detail

### The Puzzle
- A richly detailed scene image is generated entirely in the browser using HTML5 Canvas — no image files required
- The board has 5 columns. Three are removed as gaps, two remain visible
- Three draggable jigsaw pieces are shown below the board in a randomised order
- Each piece has classic interlocking tabs and blanks on all four sides, giving every piece a unique silhouette that only fits in one specific gap

### The Randomisation
Three independent shuffles are applied on every visit, making each puzzle unique:
- **Gap positions** — which 3 of the 5 slots are missing (60 possible combinations)
- **Tray order** — the left-to-right display order of the 3 draggable pieces
- **Display numbers** — the numbers shown on pieces and gaps (1, 2, 3 assigned randomly)

### The Ordering Requirement
Pieces must be placed in the correct order — piece 1 first, then piece 2, then piece 3. Placing the right piece in the right slot out of order is rejected. This means the visitor must satisfy both spatial placement and sequential ordering simultaneously.

### Visual Themes
Six dark-themed scene styles are chosen randomly on each visit:
- Deep Space City — skyline silhouette, glowing moon, lit windows
- Neon Ocean — water ripples, distant ship, glowing orb
- Volcanic Dusk — volcano cone, lava streams, fire-lit sky
- Aurora Tundra — animated aurora bands, pine tree silhouettes
- Cyberpunk Rain — rain streaks, neon ground grid, city glow
- Desert Twilight — large sun orb, sand dunes, star field

### Security Features
- Correct slot positions and required placement order are stored server-side and never exposed in the HTML source
- CSRF token on the form — validated server-side with `hash_equals()` to prevent cross-site form submissions
- Wrong slot placements flash red without counting as a failed attempt
- After 5 incorrect submissions the puzzle locks out for 60 seconds
- Rate limit counter resets on each new puzzle so a fresh puzzle after lockout starts clean
- Works on both desktop (mouse drag) and mobile (touch drag)

### Cookie and Session
On successful completion two cookies are set:
- `human_ticket` — HttpOnly, used for server-side session matching
- `human_ticket_mobile` — accessible to JavaScript, for mobile compatibility

Both expire after 24 hours. The server session also stores `verified_human = true` and the visitor's real IP (Cloudflare-aware, see below) for cross-reference.

---

## Logging

All blocked and CAPTCHA events are written to `alist.txt` — one line per entry. Allowed traffic and whitelisted bots are never logged.

Each log line contains: timestamp, status (BLOCKED or CAPTCHA), reason code, IP address, HTTP method, full URL, referrer, port, and User-Agent.

Example log entries:
```
[2026-06-18 14:01:19] BLOCKED | BAD_UA:dotbot | IP:216.244.66.236 | GET https://example.com/search/hashtag/test | REF:- | PORT:46448 | UA:Mozilla/5.0 (compatible; DotBot/1.2;...)
[2026-06-18 14:05:37] CAPTCHA | CAPTCHA_ALL | IP:8.216.48.139 | GET https://example.com/posts/40106 | REF:- | PORT:53333 | UA:Mozilla/5.0 (Windows NT 10.0;...)
[2026-06-18 14:01:59] BLOCKED | OLD_CHROME:51 | IP:191.23.56.185 | GET https://example.com/posts/41965 | REF:- | PORT:46090 | UA:...Chrome/51.0...
```

### Log Rotation
The log file is automatically trimmed when it exceeds 1MB. The oldest entries are removed and the most recent 5,000 lines are kept. Rotation only runs when a block or CAPTCHA event fires — not on every request — to minimise filesystem overhead.

---

## Files Required

| File | Location | Purpose |
|------|----------|---------|
| `blocks.php` | Web root | Main blocker — all checks and CAPTCHA trigger |
| `verify_overlay.php` | Web root | Jigsaw CAPTCHA page — served to unverified visitors |
| `alist.txt` | Web root | Log file — created automatically if it does not exist |

Both PHP files must be in the same directory. The log file is created automatically on first use.

---

## Installation

### Step 1 — Upload the Files
Upload `blocks.php` and `verify_overlay.php` to your web root. For cPanel hosting this is typically `/home/yourusername/public_html/`.

### Step 2 — Edit the File Paths
Open `blocks.php` and update the log file path on this line to match your server:
```php
$logFile = '/home/yourusername/public_html/alist.txt';
```

Open `blocks.php` and update the WordPress probe 404 path if needed:
```php
$custom404 = '/home/yourusername/public_html/404.shtml';
```
If you do not have a custom 404 page, the fallback plain HTML 404 will be used automatically.

### Step 3 — Edit Your .htaccess File
Add these two lines to the top of your `.htaccess` file in your web root:
```apache
SetEnv AUTOPOSTER_TOKEN your_secret_token_here
php_value auto_prepend_file /home/yourusername/public_html/blocks.php
```

Replace `your_secret_token_here` with a long random string. This is the token your trusted automated tools will send in the `X-Autoposter-Token` header to bypass all checks. Replace `yourusername` with your actual server username.

The `SetEnv` line must come **before** the `php_value` line so the token is available when `blocks.php` loads.

### Step 4 — Set File Permissions
```
blocks.php          644
verify_overlay.php  644
alist.txt           644  (or 666 if auto-creation fails)
```

### Step 5 — Test the Installation
Open your site in a browser you have not visited before (or clear cookies). You should see the jigsaw CAPTCHA puzzle. Solve it and confirm you are redirected to your original destination and can browse freely.

To test bot blocking, run:
```bash
curl -A "curl/7.88" https://yoursite.com/
```
You should receive a `403 Forbidden` response.

Check `alist.txt` to confirm the block was logged.

### Step 6 — AJAX Endpoints (Optional)
If your site makes background AJAX requests that fire before the visitor has solved the CAPTCHA, add those paths to the skip list in `blocks.php`:
```php
$skipCaptcha = ['/includes/ajax/data/live.php', '/includes/ajax/chat/live.php'];
```
Replace or extend with your own AJAX endpoint paths.

---

## Configuring the Autoposter Token Bypass

If you have an automated tool (autoposter, RSS importer, deployment script etc.) that needs to access your site without hitting the CAPTCHA or bot checks, configure it to send the secret token header:

```
X-Autoposter-Token: your_secret_token_here
```

The token is loaded on the server from the environment variable set in `.htaccess`:
```apache
SetEnv AUTOPOSTER_TOKEN your_secret_token_here
```

The token is never stored in the PHP source code. If your `.htaccess` file is ever exposed, the token is not visible in the PHP files.

---

## Customisation

### Adding Blocked User Agents
Add strings to the `$blockedAgents` array in `blocks.php`. Any User-Agent containing the string (case-insensitive) will be hard-blocked with a 403:
```php
$blockedAgents = [
    'curl', 'wget', 'python',
    'my-custom-bad-bot', // add your own here
];
```

### Adding Whitelisted Good Bots
Add strings to the `$allowedBots` array in `blocks.php`. Any User-Agent containing the string will be passed through silently:
```php
$allowedBots = [
    'googlebot', 'bingbot',
    'my-trusted-bot', // add your own here
];
```

### Adding AJAX Skip Paths
Add paths to the `$skipCaptcha` array in `blocks.php`:
```php
$skipCaptcha = [
    '/includes/ajax/data/live.php',
    '/includes/ajax/chat/live.php',
    '/api/my-endpoint', // add your own here
];
```

### Adding WordPress Probe Paths
Add paths to the `$wpProbePaths` array in `blocks.php`:
```php
$wpProbePaths = [
    '/wp-login.php', '/wp-admin', '/xmlrpc.php',
    '/phpmyadmin', // add other probe paths here
];
```

### Updating the Minimum Chrome Version
The minimum allowed Chrome version is currently set to 110. Update this in `blocks.php` as older versions become increasingly rare:
```php
if ($chromeMajor < 110) { // increase this over time
```

### Changing the Cookie Duration
The CAPTCHA verification cookie expires after 24 hours. To change this, find both `setcookie` calls in `verify_overlay.php` and update the expiry:
```php
'expires' => time() + 86400, // 86400 = 24 hours, 604800 = 7 days
```

---

## Compatibility

- Any PHP 7.4+ website
- Works on shared hosting, VPS, and dedicated servers
- No PHP extensions required beyond standard defaults
- No Composer, no npm, no build step
- Compatible with any PHP framework or CMS (WordPress, Laravel, Sngine, custom PHP etc.)
- Mobile browsers fully supported via touch drag
- Works automatically whether or not the site is behind Cloudflare — no configuration needed

---

## Security Notes

- The correct puzzle slot positions and placement order are stored in `$_SESSION` server-side and are never sent to the browser
- The CAPTCHA form includes a CSRF token validated with `hash_equals()` on every POST
- The autoposter secret token is loaded from a server environment variable — never hardcoded
- IP resolution is Cloudflare-aware: `getRealIp()` only trusts the `CF-Connecting-IP` header when the direct connection is verified against Cloudflare's published IP ranges, otherwise it uses `REMOTE_ADDR` directly. This means the header can't be spoofed by a visitor who isn't actually coming through Cloudflare, and no configuration is needed whether or not you use Cloudflare.
- The log file path should ideally be outside the web root to prevent direct browser access. If you must keep it in the web root, add this to `.htaccess` to block direct access:

```apache
<Files "alist.txt">
    Order allow,deny
    Deny from all
</Files>
```

---

## What Gets Blocked vs What Gets Challenged

| Visitor Type | Result |
|-------------|--------|
| Empty User-Agent | 403 Blocked |
| Known good bot (Googlebot etc.) | Passed silently |
| Known bad bot / scraper UA | 403 Blocked |
| Old Chrome version (below 110) | 403 Blocked |
| WordPress probe path | 404 Not Found |
| Unverified human visitor | Jigsaw CAPTCHA served |
| Verified human (cookie present) | Passed silently |
| Trusted autoposter (token header) | Passed silently |

---

## Frequently Asked Questions

**Will this affect my Google Search ranking?**
No. Googlebot and all major legitimate search crawlers are whitelisted and pass through silently without ever seeing the CAPTCHA.

**Will real users be annoyed?**
No. The CAPTCHA appears once per 24 hours. After solving it, the visitor browses freely for the rest of the day with no interruptions.

**What if a user fails the puzzle?**
Wrong placements flash red but do not count as a failed attempt. Only incorrect form submissions count. After 5 incorrect submissions a 60-second lockout applies, then the puzzle resets with a fresh random configuration.

**Can bots solve the puzzle?**
The puzzle requires matching jigsaw piece shapes spatially across a randomised scene image, placing them in the correct order, with server-side validation of both slot and sequence. The correct answers are never in the HTML source. This is significantly harder to automate than a checkbox CAPTCHA or simple image match.

**Does it work without JavaScript?**
No — the puzzle is rendered with HTML5 Canvas and requires JavaScript for drag-and-drop. Visitors with JavaScript disabled will see a non-functional puzzle. This is by design — the vast majority of bots do not execute JavaScript at all.

**Can I use this behind Cloudflare?**
Yes, and no configuration is required either way. `blocks.php` resolves the visitor's real IP itself via `getRealIp()`: if the direct connection to your server is verified as coming from a genuine Cloudflare IP range, it trusts the `CF-Connecting-IP` header for the true visitor address; otherwise it uses `REMOTE_ADDR` exactly as before. `verify_overlay.php` reuses the same function, so the CAPTCHA session/cookie IP always matches what `blocks.php` checks on the next request — with or without Cloudflare in front of your site.

---

## Changelog

### v1.2
- Added Cloudflare-aware IP resolution (`getRealIp()` in `blocks.php`): trusts `CF-Connecting-IP` only when the direct connection is verified against Cloudflare's published IP ranges, otherwise falls back to `REMOTE_ADDR` unchanged — no configuration required either way
- `verify_overlay.php`'s `overlay_get_ip()` now calls the same `getRealIp()` from `blocks.php` instead of reading `REMOTE_ADDR` on its own, keeping the CAPTCHA session IP consistent with what `blocks.php` checks — fixes a mismatch that would otherwise cause the puzzle to reappear on every page load for sites behind Cloudflare
- Supersedes the v1.1 "IP source unified" change below, which made IP resolution `REMOTE_ADDR`-only everywhere; v1.2 keeps both files in sync while adding Cloudflare support

### v1.1
- Secret token moved from hardcoded source to server environment variable (`AUTOPOSTER_TOKEN`)
- IP source unified to `REMOTE_ADDR` across both files — eliminates session mismatch from spoofable proxy headers *(superseded in v1.2, see above)*
- `rotate_log()` moved to fire only on block/CAPTCHA events, not on every request
- CSRF token added to CAPTCHA form, validated server-side with `hash_equals()`
- Wrong-answer rate limiting — 5 failures triggers 60-second lockout
- WordPress probe path blocking added (Layer 5)
- Jigsaw puzzle upgraded from 3-column single-piece to 5-column 3-piece ordered placement
- Classic jigsaw tab/blank shapes on all 4 sides of each piece
- 6 rich dark visual themes with detailed scene objects
- Triple randomisation — gap positions, tray order, and display numbers shuffled independently
- Placement order requirement — pieces must be placed in sequence 1 → 2 → 3

### v1.0
- Initial release
- 5-layer bot blocking (empty UA, good bot whitelist, bad bot list, old Chrome, CAPTCHA)
- Single drag-and-drop puzzle with 3 columns and 1 missing piece
- 5 visual themes
- 24-hour verification cookie
- Log rotation at 1MB / 5000 lines

---

## Licence

MIT — free to use, modify, and distribute. Attribution appreciated but not required.
