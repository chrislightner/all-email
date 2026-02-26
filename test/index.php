<?php
/**
 * Modern Email Proofer Index - Centered Stacked Layout
 * Automatically detects emails in the same folder and provides Desktop/Mobile toggles.
 */

// 1. CUSTOM LABELS CONFIGURATION (RESTORED)
// Edit these to match your specific project needs.
$customLabels = [
    'e1' => 'First email baseline',
    'e2' => 'Second email Crossell',
    'e3' => 'Third email Catch-all',
];

// 2. DYNAMIC VERSION DETECTION
function getVersionLabel($filename) {
    $parts = explode('_', str_replace('.html', '', $filename));
    return strtoupper(end($parts)); // Returns E1, E2, etc.
}

// 3. CONFIGURATION & FILE SCANNING
$dir = __DIR__;
$files = scandir($dir);
$emailFiles = [];

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
        $emailFiles[] = $file;
    }
}

// 4. GET CURRENT STATE
$currentFile = isset($_GET['f']) ? $_GET['f'] : (isset($emailFiles[0]) ? $emailFiles[0] : '');
$viewMode = isset($_GET['m']) ? $_GET['m'] : 'desktop'; 

// Modern Device Dimensions
$mobileWidth = 393;
$mobileHeight = 852;

// 5. DEFINE METADATA (Subject/Preheader)
$tagRaw = getVersionLabel($currentFile);
$vTag = $currentFile ? "(" . $tagRaw . ") " : "";
$subject = "Subject TBD";
$subject2 = ""; 
$preheader = "Preheader TBD";

// Logic based on the detected version tag
$tagLower = strtolower($tagRaw);

if ($tagLower === 'e1') {
    $subject = $vTag . "[NAME], another benefit of your ADA Student Membership";
    $subject2 = $vTag . "Alternative Subject: Secure your future today, [NAME]";
    $preheader = "Life Insurance and Disability Insurance activated for you";
} elseif ($tagLower === 'e2') {
    $subject = $vTag . "[NAME], another benefit of your ADA Student Membership";
    $preheader = "Activate your Life Insurance and Disability Insurance with guaranteed acceptance";
} elseif ($tagLower === 'e3') {
    $subject = $vTag . "[NAME], another benefit of your ADA Student Membership";
    $preheader = "Activate your Life Insurance and Disability Insurance with guaranteed approval";
} else {
    $subject = $vTag . $subject;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Proof - <?php echo htmlspecialchars($currentFile); ?></title>
    <style>
        :root {
            --bg-color: #f0f2f5;
            --card-bg: #ffffff;
            --accent-color: #007bff;
            --text-main: #1a1a1a;
            --text-muted: #65676b;
            --content-width: 800px; 
            --border-gray: #666666;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        #top-nav {
            width: 100%;
            max-width: var(--content-width);
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            padding: 20px;
            box-sizing: border-box;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .project-info h1 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--text-main);
            font-weight: 700;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .version-select {
            flex-grow: 1;
            min-width: 200px;
            position: relative;
        }

        /* STYLED DROPDOWN */
        select {
            width: 100%;
            padding: 12px 40px 12px 16px;
            border-radius: 8px;
            border: 2px solid var(--accent-color);
            background: #fff;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,123,255,0.1);
        }

        select:hover {
            border-color: #0056b3;
            background-color: #f8fbff;
        }

        .version-select::after {
            content: "";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 0; 
            height: 0; 
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid var(--accent-color);
            pointer-events: none;
        }

        .view-toggles {
            display: flex;
            background: #eee;
            padding: 4px;
            border-radius: 10px;
            gap: 2px;
        }

        .view-btn, .print-btn {
            padding: 8px 16px;
            border-radius: 7px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .view-btn.active {
            background: #fff;
            color: var(--accent-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .print-btn:hover {
            background: #e0e0e0;
            color: #333;
        }

        .meta-stack {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 13px;
            line-height: 1.5;
            border: 1px solid #edf0f2;
        }

        .meta-line {
            margin-bottom: 4px;
            display: flex;
        }
        .meta-line:last-child { margin-bottom: 0; }
        .meta-line b { 
            min-width: 90px; 
            color: var(--text-main);
        }
        .meta-line span { color: var(--text-muted); }

        #preview-container {
            width: 100%;
            display: flex;
            justify-content: center;
            padding-bottom: 50px;
        }

        #print-mount {
            display: none;
        }

        .mode-desktop #preview-frame {
            width: 100%;
            max-width: var(--content-width);
            height: 1200px; 
            background: #fff;
            border: 1px solid var(--border-gray);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 4px;
        }

        .mode-mobile #preview-frame {
            width: <?php echo $mobileWidth; ?>px;
            height: <?php echo $mobileHeight; ?>px;
            border: 14px solid #1a1a1a;
            border-radius: 54px; 
            background: #fff;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden; 
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        @media print {
            @page {
                size: portrait;
                margin: 0.5in;
            }
            body, html, .wrapper {
                background: #fff !important;
                display: block !important;
                height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #top-nav {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                margin: 0 0 10px 0 !important;
                padding: 0 !important;
            }
            .header-row, .controls {
                display: none !important; 
            }
            .meta-stack {
                background: #fff !important;
                border: none !important;
                padding: 0 0 10px 0 !important;
                font-size: 11pt !important;
                border-bottom: 1px solid #333 !important;
                border-radius: 0 !important;
                margin-bottom: 15px !important;
                break-after: avoid !important;
            }
            #preview-container {
                display: none !important; 
            }
            #print-mount {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
    <script>
        function preparePrint() {
            const frame = document.querySelector('iframe');
            const printMount = document.getElementById('print-mount');
            
            if (frame && frame.contentWindow) {
                const emailContent = frame.contentWindow.document.documentElement.innerHTML;
                printMount.innerHTML = emailContent;
                
                setTimeout(() => {
                    window.print();
                }, 200);
            } else {
                window.print();
            }
        }
    </script>
</head>
<body class="mode-<?php echo $viewMode; ?>">

    <div class="wrapper">
        <nav id="top-nav">
            <div class="header-row">
                <div class="project-info">
                    <h1>ALL-9362 ASDA Emails</h1>
                </div>
                <div class="view-toggles">
                    <button onclick="preparePrint()" class="print-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Print PDF
                    </button>
                    <div style="width: 1px; background: #ddd; margin: 0 5px;"></div>
                    <a href="index.php?f=<?php echo $currentFile; ?>&m=desktop" class="view-btn <?php echo ($viewMode == 'desktop') ? 'active' : ''; ?>">Desktop</a>
                    <a href="index.php?f=<?php echo $currentFile; ?>&m=mobile" class="view-btn <?php echo ($viewMode == 'mobile') ? 'active' : ''; ?>">Mobile</a>
                </div>
            </div>
            
            <div class="controls">
                <div class="version-select">
                    <form id="fileForm" method="GET" action="index.php">
                        <input type="hidden" name="m" value="<?php echo $viewMode; ?>">
                        <select name="f" onchange="this.form.submit()">
                            <?php if (empty($emailFiles)): ?>
                                <option>No HTML files found</option>
                            <?php else: ?>
                                <?php foreach ($emailFiles as $file): ?>
                                    <?php 
                                        $vKey = strtolower(getVersionLabel($file));
                                        $labelText = isset($customLabels[$vKey]) ? $customLabels[$vKey] : htmlspecialchars($file);
                                    ?>
                                    <option value="<?php echo $file; ?>" <?php echo ($currentFile == $file) ? 'selected' : ''; ?>>
                                        (<?php echo strtoupper($vKey); ?>) <?php echo $labelText; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </form>
                </div>
            </div>

            <div class="meta-stack" style="margin-top: 15px;">
                <div class="meta-line"><b>Subject:</b> <span><?php echo htmlspecialchars($subject); ?></span></div>
                <?php if (!empty($subject2)): ?>
                    <div class="meta-line"><b>Subject 2:</b> <span><?php echo htmlspecialchars($subject2); ?></span></div>
                <?php endif; ?>
                <div class="meta-line"><b>Preheader:</b> <span><?php echo htmlspecialchars($preheader); ?></span></div>
            </div>
        </nav>

        <main id="preview-container">
            <?php if ($currentFile): ?>
                <div id="preview-frame">
                    <iframe src="<?php echo $currentFile; ?>?t=<?php echo time(); ?>"></iframe>
                </div>
            <?php else: ?>
                <div style="text-align:center; color:#999; padding-top:100px;">
                    <h2>No emails found.</h2>
                </div>
            <?php endif; ?>
        </main>

        <div id="print-mount"></div>
    </div>

</body>
</html>