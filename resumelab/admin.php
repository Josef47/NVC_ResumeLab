<?php
/**
 * admin.php
 * 
 * Admin dashboard to view analytics statistics
 * Requires URL parameter: ?key=SECRET123
 */

// Admin authentication key
define('ADMIN_KEY', 'SECRET123');

// Check if key is provided and correct
$providedKey = isset($_GET['key']) ? $_GET['key'] : '';

if ($providedKey !== ADMIN_KEY) {
    http_response_code(401);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Unauthorized - ResumeLab Admin</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                background: #f5f5f5;
            }
            .error-box {
                background: white;
                padding: 2rem;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                text-align: center;
            }
            h1 {
                color: #d32f2f;
                margin: 0 0 1rem 0;
            }
            p {
                color: #666;
                margin: 0;
            }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>Unauthorized</h1>
            <p>You do not have permission to access this page.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Fetch stats from stats.php
$statsUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/stats.php';
$statsJson = @file_get_contents($statsUrl);
$stats = json_decode($statsJson, true);

if ($stats === null) {
    $stats = [
        'summary' => [
            'total_events' => 0,
            'page_views' => 0,
            'cta_resume_clicks' => 0,
            'cta_waitlist_clicks' => 0,
            'pricing_clicks' => 0,
            'scroll_25' => 0,
            'scroll_50' => 0,
            'scroll_75' => 0,
            'scroll_100' => 0,
            'exit_no_interaction' => 0
        ]
    ];
}

$summary = isset($stats['summary']) ? $stats['summary'] : [];
$pageViews = isset($summary['page_views']) ? $summary['page_views'] : 0;
$ctaResumeClicks = isset($summary['cta_resume_clicks']) ? $summary['cta_resume_clicks'] : 0;
$ctaWaitlistClicks = isset($summary['cta_waitlist_clicks']) ? $summary['cta_waitlist_clicks'] : 0;
$pricingClicks = isset($summary['pricing_clicks']) ? $summary['pricing_clicks'] : 0;

// Calculate CTR percentages
$ctaResumeCTR = $pageViews > 0 ? round(($ctaResumeClicks / $pageViews) * 100, 2) : 0;
$ctaWaitlistCTR = $pageViews > 0 ? round(($ctaWaitlistClicks / $pageViews) * 100, 2) : 0;
$pricingCTR = $pageViews > 0 ? round(($pricingClicks / $pageViews) * 100, 2) : 0;

// Calculate bounce rate (users who exited without interaction)
$exitNoInteraction = isset($summary['exit_no_interaction']) ? $summary['exit_no_interaction'] : 0;
$bounceRate = $pageViews > 0 ? round(($exitNoInteraction / $pageViews) * 100, 2) : 0;

// Scroll depth statistics
$scroll25 = isset($summary['scroll_25']) ? $summary['scroll_25'] : 0;
$scroll50 = isset($summary['scroll_50']) ? $summary['scroll_50'] : 0;
$scroll75 = isset($summary['scroll_75']) ? $summary['scroll_75'] : 0;
$scroll100 = isset($summary['scroll_100']) ? $summary['scroll_100'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - ResumeLab Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            padding: 2rem;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        header {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .last-updated {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 600;
        }
        .stat-percentage {
            color: #27ae60;
            font-size: 1.2rem;
            font-weight: 600;
        }
        .section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .section h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        .ctr-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .ctr-item:last-child {
            border-bottom: none;
        }
        .ctr-label {
            color: #34495e;
        }
        .scroll-bar {
            background: #ecf0f1;
            height: 30px;
            border-radius: 15px;
            margin: 0.5rem 0;
            position: relative;
            overflow: hidden;
        }
        .scroll-fill {
            background: linear-gradient(90deg, #3498db, #2980b9);
            height: 100%;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            transition: width 0.3s ease;
        }
        .scroll-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }
        .scroll-label span:first-child {
            color: #34495e;
        }
        .scroll-label span:last-child {
            color: #7f8c8d;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 ResumeLab Analytics Dashboard</h1>
            <div class="last-updated">
                Last updated: <?php echo isset($stats['metadata']['last_updated']) ? $stats['metadata']['last_updated'] : 'Never'; ?>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Page Views</div>
                <div class="stat-value"><?php echo number_format($pageViews); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Resume CTA Clicks</div>
                <div class="stat-value"><?php echo number_format($ctaResumeClicks); ?></div>
                <div class="stat-percentage"><?php echo $ctaResumeCTR; ?>% CTR</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Waitlist CTA Clicks</div>
                <div class="stat-value"><?php echo number_format($ctaWaitlistClicks); ?></div>
                <div class="stat-percentage"><?php echo $ctaWaitlistCTR; ?>% CTR</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Bounce Rate</div>
                <div class="stat-value"><?php echo $bounceRate; ?>%</div>
            </div>
        </div>

        <div class="section">
            <h2>Click-Through Rates (CTR)</h2>
            <div class="ctr-item">
                <span class="ctr-label">Resume Review CTA</span>
                <span class="stat-percentage"><?php echo $ctaResumeCTR; ?>%</span>
            </div>
            <div class="ctr-item">
                <span class="ctr-label">Waitlist CTA</span>
                <span class="stat-percentage"><?php echo $ctaWaitlistCTR; ?>%</span>
            </div>
            <div class="ctr-item">
                <span class="ctr-label">Pricing Clicks</span>
                <span class="stat-percentage"><?php echo $pricingCTR; ?>%</span>
            </div>
        </div>

        <div class="section">
            <h2>Scroll Depth</h2>
            <div style="margin-bottom: 1rem;">
                <div class="scroll-label">
                    <span>25% Scroll</span>
                    <span><?php echo number_format($scroll25); ?> users</span>
                </div>
                <div class="scroll-bar">
                    <div class="scroll-fill" style="width: <?php echo $pageViews > 0 ? ($scroll25 / $pageViews * 100) : 0; ?>%;">
                        <?php echo $pageViews > 0 ? round($scroll25 / $pageViews * 100, 1) : 0; ?>%
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div class="scroll-label">
                    <span>50% Scroll</span>
                    <span><?php echo number_format($scroll50); ?> users</span>
                </div>
                <div class="scroll-bar">
                    <div class="scroll-fill" style="width: <?php echo $pageViews > 0 ? ($scroll50 / $pageViews * 100) : 0; ?>%;">
                        <?php echo $pageViews > 0 ? round($scroll50 / $pageViews * 100, 1) : 0; ?>%
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div class="scroll-label">
                    <span>75% Scroll</span>
                    <span><?php echo number_format($scroll75); ?> users</span>
                </div>
                <div class="scroll-bar">
                    <div class="scroll-fill" style="width: <?php echo $pageViews > 0 ? ($scroll75 / $pageViews * 100) : 0; ?>%;">
                        <?php echo $pageViews > 0 ? round($scroll75 / $pageViews * 100, 1) : 0; ?>%
                    </div>
                </div>
            </div>
            <div>
                <div class="scroll-label">
                    <span>100% Scroll</span>
                    <span><?php echo number_format($scroll100); ?> users</span>
                </div>
                <div class="scroll-bar">
                    <div class="scroll-fill" style="width: <?php echo $pageViews > 0 ? ($scroll100 / $pageViews * 100) : 0; ?>%;">
                        <?php echo $pageViews > 0 ? round($scroll100 / $pageViews * 100, 1) : 0; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

