# PHP SmartBlocker 1.1

[![PHP SmartBlocker](https://github.com/AFFASocial/PHP-SmartBlocker-1.0/raw/main/SB.png)](https://github.com/AFFASocial/PHP-SmartBlocker-1.0/blob/main/SB.png)

"Real users only"

Just download the .zip unzip the files and click on the PHP_SmartBlocker_1.0_Installation_Guide.html and it should open in your browser follow the instructions and DONE. You can copy the code snippets directly from the installation guide during the installation.

Keep in mind I designed this to work with Sngine Social Network. This code will work on any PHP website. Also keep in mind that a real spammer "human" can solve the puzzle register and post spam, So I suggest your website have new user approval, posts approval or an option to make the site by invitation only which Sngine Social Network does all 3. The code was made due to the constant onslot of attacks Sngine Social Network is hit with 24/7. In my case I use this code along with Users Approval System built in to Sngine PHP Social Network now 100% Zero bad activity whatsoever.

In any case on a normal PHP website with registration enabled this will stop every automated attack and registration period. Keep in mind the web is loaded with click farms where a user is paid pennies to create a spam account on your server. BASICALLY you can say 99% is all stopped that 1% is those humans being paid to spam your website. An example you might get hundreds of fake visits per hour none will every get through except that 1% human spammer if registration is wide open. I would recommend every php website use this code period. It just stops everything we do not want all the time 24/7.

A lightweight, zero-dependency PHP bot protection system that stops scrapers, bots, and fake traffic dead in their tracks — all running locally on your server with no external APIs, no paid services, and zero ongoing cost.

## How It Works

PHP SmartBlocker runs as a PHP auto-prepend file, executing before every single page on your site. Every request is filtered through multiple layers in this order:

- Empty or missing user agents are hard blocked with a 403 immediately
- Known legitimate search bots like Googlebot, Bingbot and DuckDuckBot are whitelisted and pass through freely — your SEO is completely unaffected
- Known scraper and bot tools like curl, wget, scrapy, selenium, puppeteer and hundreds more are hard blocked with a 403
- Outdated Chrome versions below 110 used as bot fingerprints are hard blocked
- Every other visitor is presented with a drag-and-drop puzzle CAPTCHA they must solve before accessing any page

The Jigsaw CAPTCHA

A randomly generated scene image is split into 5 columns across a board. Three pieces are removed and shown as draggable jigsaw pieces below, each with classic interlocking tabs and blanks on all four sides. Six visual themes are used — deep space city, neon ocean, volcanic dusk, aurora tundra, cyberpunk rain, and desert twilight — chosen randomly on every visit.

The visitor must drag all 3 pieces into their correct slots in the right order (piece 1 first, then 2, then 3). The correct slot positions and required placement order are stored server-side and never exposed in the HTML source. Wrong slot placements flash red without counting as a failed attempt. After 5 incorrect submissions the puzzle locks out for 60 seconds. Works on both desktop with mouse drag and mobile with touch drag.

ℹ The board gaps, tray pieces, and display numbers are all independently randomised on every visit — making pattern recognition and automated solving extremely difficult.

## After Solving

A 24-hour cookie is set so real visitors only see the puzzle once per day.

## Logging

All blocked requests and CAPTCHA challenges are logged to alist.txt with full details including IP, user agent, URL and timestamp. Allowed traffic is not logged to keep the file small. The log automatically trims itself to 5,000 lines when it reaches 1MB so it never grows out of control.

## Real World Results

- Server CPU usage drops to near idle
- Google Analytics data becomes clean real traffic only
- Bot registrations drop to zero
- Content scraping stops completely
- GA noise from fake traffic eliminated completely
- Proven in production on a live social network

## Requirements

- PHP 7.4 or higher
- Apache with .htaccess support
- Two files uploaded to your web root
- One line added to .htaccess

## Files

- `blocks.php` — the main bot blocker, upload to your web root
- `verify_overlay.php` — the drag-and-drop puzzle CAPTCHA page, upload to your web root

## Installation

1. Upload both files to your website root directory
2. Add this one line to your .htaccess file:

```
php_value auto_prepend_file /home/YOUR_USERNAME/public_html/blocks.php
```

Replace `YOUR_USERNAME` with your own cPanel username. You can find your exact path in cPanel → File Manager → navigate to public_html → the address bar shows your full path.

3. Open your site in an incognito window — the drag-and-drop puzzle should appear immediately

That's it. PHP SmartBlocker is now protecting every page on your site.

## License

Free to use, modify and share.

---

**** Most common solutions and their weaknesses ****

- **Cloudflare** — effective but you're dependent on a third party, costs money at scale, and can have false positives
- **Fail2ban** — server level, requires root access, not available on shared hosting
- **ModSecurity** — powerful but complex to configure, requires server access
- **reCAPTCHA** — depends on Google, privacy concerns, bypassable by humans
- **IP blocklists** — reactive, always behind, VPNs bypass them instantly
- **Country blocking** — crude, catches legitimate users, VPNs bypass it
- **Wordfence/security plugins** — WordPress specific, heavy, database dependent

**What makes PHP SmartBlocker exceptional:**

- **Proactive not reactive** — doesn't try to identify bad traffic, requires proof of humanity
- **Zero dependencies** — nothing to break, nothing to pay for, nothing to maintain
- **Shared hosting friendly** — works where most solutions don't
- **Puzzle CAPTCHA** — drag and drop is genuinely one of the hardest challenges for automated tools
- **Layered defense** — multiple checks in the right order, cheapest first
- **Proven in production** — real results on a live site

The philosophy behind it is what makes it special — instead of building bigger walls against known threats, it simply requires everyone to prove they are human. That approach doesn't need updating as new threats emerge.

AFFA Social https://www.affasocial.com
