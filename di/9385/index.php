<?php
/**
 * Email Proofer Index - Centered Stacked Layout
 * Groups by Sub-Category (WARMUP/FOLLOWUP) and sorts by Category Order.
 */

/**
 * 1. CONFIGURATION
 */
$projectName = "ALL-9385 ADA Disability Emails";

/**
 * CATEGORY ORDER: 
 * Define the order you want acronyms to appear.
 */
$categoryOrder = ['ADA'];

/**
 * SUB-CATEGORY GROUPS:
 * These will act as the OptGroup headers in the dropdown.
 */
$subCategoryGroups = [
    'ADA'   => ['ada-e1', 'ada-e2', 'ada-e3'],
    // 'HPSO' => ['hpso-e1', 'hpso-e2', 'hpso-e3'],
    // 'ACA' => ['aca-e4', 'aca-e5', 'aca-e6'],
];

/**
 * CUSTOM LABELS CONFIGURATION 
 * Matches the 'key' generated (prefix-version)
 */
$customLabels = [
    'ada-e1' => 'Existing DI Insureds',
    'ada-e2' => 'All Eligible',
    'ada-e3' => 'Address Email',
];

/**
 * METADATA CONFIGURATION
 */
$metaDefaults = 
[
    'ada-e1' => [
        'subject1'   => "Dr. [NAME], your existing ADA disability coverage might not be enough",
        'preheader1' => "TBD",
    ],
    'ada-e2' => [
        'subject1'   => "Dr. [NAME], your workplace disability coverage might not be enough",
        'preheader1' => "Help strengthen your income protection with ADA Disability Insurance.",
    ],
    'ada-e3' => [
        'subject1'   => "[NAME], Important Note About Your ADA Insurance Address",
        'preheader1' => "TBD",
    ],
];

// 2. LOGIC FUNCTIONS
function getFileInfo($filename, $subCategoryGroups = []) {
    if (!$filename) return null;
    $clean = str_replace('.html', '', $filename);
    
    $delimiter = (strpos($clean, '-') !== false) ? '-' : '_';
    $parts = explode($delimiter, $clean);
    
    $prefix  = strtoupper($parts[0]); 
    $version = strtolower(end($parts)); 
    
    preg_match('/\d+/', $version, $matches);
    $vNum = isset($matches[0]) ? (int)$matches[0] : 999;
    
    $key = strtolower($parts[0] . '-' . end($parts));
    
    $groupLabel = 'OTHER';
    foreach ($subCategoryGroups as $label => $keys) {
        if (in_array($key, $keys)) {
            $groupLabel = $label;
            break;
        }
    }
    
    return [
        'prefix'     => $prefix,
        'groupLabel' => $groupLabel,
        'version'    => $version,
        'vNum'       => $vNum,
        'key'        => $key,
        'full'       => "$prefix " . strtoupper($version)
    ];
}

// 3. FILE PROCESSING
$emailFiles = array_filter(scandir(__DIR__), function($f) {
    return pathinfo($f, PATHINFO_EXTENSION) === 'html';
});

usort($emailFiles, function($a, $b) use ($categoryOrder, $subCategoryGroups) {
    $infoA = getFileInfo($a, $subCategoryGroups);
    $infoB = getFileInfo($b, $subCategoryGroups);
    
    $groups = array_keys($subCategoryGroups);
    $gPosA = array_search($infoA['groupLabel'], $groups);
    $gPosB = array_search($infoB['groupLabel'], $groups);
    $gPosA = ($gPosA === false) ? 999 : $gPosA;
    $gPosB = ($gPosB === false) ? 999 : $gPosB;
    
    if ($gPosA !== $gPosB) return $gPosA <=> $gPosB;

    $posA = array_search($infoA['prefix'], $categoryOrder);
    $posB = array_search($infoB['prefix'], $categoryOrder);
    $posA = ($posA === false) ? 999 : $posA;
    $posB = ($posB === false) ? 999 : $posB;
    
    if ($posA !== $posB) return $posA <=> $posB;
    
    return $infoA['vNum'] <=> $infoB['vNum'];
});

$groupedFiles = [];
foreach ($emailFiles as $f) {
    $info = getFileInfo($f, $subCategoryGroups);
    $groupedFiles[$info['groupLabel']][] = ['file' => $f, 'info' => $info];
}

// 4. STATE
$currentFile = $_GET['f'] ?? ($emailFiles[0] ?? '');
$viewMode    = $_GET['m'] ?? 'desktop';
$curInfo     = getFileInfo($currentFile, $subCategoryGroups);

$lookupKey = $curInfo['key'] ?? '';
$meta = $metaDefaults[$lookupKey] ?? [
    'subject1'   => 'Subject 1 TBD', 
    'preheader1' => 'Preheader 1 TBD'
];

$mobileWidth = 393;
$mobileHeight = 852;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $projectName; ?></title>
    <style>
        :root {
            --bg-color: #f0f2f5;
            --card-bg: #ffffff;
            --accent-color: #2563eb;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --content-width: 900px; 
            --border-gray: #e2e8f0;
            --frame-border: #475569;
        }
        body, html {
            margin: 0; padding: 0; min-height: 100%;
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
            background-color: var(--bg-color); color: var(--text-main); line-height: 1.5;
        }
        .wrapper { display: flex; flex-direction: column; align-items: center; padding: 12px; box-sizing: border-box; }
        
        #top-nav {
            width: 100%; max-width: var(--content-width);
            background: var(--card-bg); border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            margin-bottom: 24px; padding: 24px; box-sizing: border-box;
        }
        
        .header-row { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            padding-bottom: 20px; 
            border-bottom: 1px solid var(--border-gray); 
            gap: 16px; 
        }
        
        .project-info h1 { margin: 0; font-size: 1.25rem; font-weight: 800; letter-spacing: -0.025em; }
        
        .view-toggles { 
            display: flex; 
            gap: 8px; 
            align-items: center; 
            flex-wrap: nowrap;
        }
        
        .view-btn { 
            height: 38px; padding: 0 16px; border-radius: 8px; text-decoration: none; 
            font-size: 13px; font-weight: 600; color: var(--text-muted); transition: 0.2s; 
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            background: #ffffff;
            border: 1px solid var(--border-gray);
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            cursor: pointer; 
            white-space: nowrap;
        }
        .view-btn.active { 
            background: var(--accent-color); 
            color: #ffffff; 
            border-color: var(--accent-color);
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); 
        }
        .view-btn:hover:not(.active) { 
            color: var(--text-main); 
            border-color: var(--text-muted);
            background: #f8fafc;
        }

        .divider { display: none; } /* Removed shared divider for distinct button look */
        
        .selector-w { margin-bottom: 20px; }
        
        select { 
            width: 100%; padding: 14px; border-radius: 12px; 
            border: 2px solid var(--border-gray); font-size: 16px; 
            font-weight: 500; background: white; outline: none; 
            appearance: none; color: #000000;
        }

        optgroup { font-style: normal; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; background: #f1f5f9; }
        option { font-weight: 500; padding: 8px; background: #fff; }

        .meta-stack { background: #f8fafc; border: 1px solid var(--border-gray); border-radius: 12px; padding: 16px; font-size: 14px; }
        .meta-line { display: flex; gap: 12px; margin-bottom: 8px; }
        .meta-line:last-child { margin-bottom: 0; }
        .meta-line b { font-weight: 700; color: var(--text-muted); min-width: 95px; }
        
        #preview-container { width: 100%; display: flex; justify-content: center; padding-bottom: 50px; }
        .mode-desktop #preview-frame { width: 100%; max-width: var(--content-width); height: 1000px; border: 1pt solid var(--frame-border); border-radius: 12px; background: white; overflow: hidden; }
        
        .mode-mobile #preview-frame { 
            width: <?php echo $mobileWidth; ?>px; 
            height: <?php echo $mobileHeight; ?>px; 
            border: 12px solid #1e293b; 
            border-radius: 40px; 
            background: white; 
            position: relative; 
            overflow: hidden; 
            transform-origin: top center; 
        }
        
        iframe { width: 100%; height: 100%; border: none; display: block; }
        
        #print-mount { display: none; }

        /* RESPONSIVE OVERRIDES */
        @media (max-width: 768px) {
            .wrapper { padding: 10px; }
            #top-nav { padding: 16px; }
            
            .header-row { 
                flex-direction: column; 
                text-align: center; 
                align-items: center; 
                gap: 16px; 
                padding-bottom: 16px;
            }

            .view-toggles { 
                width: 100%; 
                justify-content: center;
                gap: 6px; 
            }

            .view-btn {
                padding: 0 10px;
                font-size: 12px;
                gap: 4px;
                height: 34px;
                flex: 1; /* Allow them to grow and shrink evenly */
                max-width: 85px; /* Cap size so they stay on line */
            }
            
            .meta-line { flex-direction: column; gap: 4px; margin-bottom: 12px; }
            .meta-line b { min-width: unset; }
            
            .mode-mobile #preview-frame { transform: scale(0.85); margin-bottom: -100px; }
        }

        @media (max-width: 480px) {
            /* iPhone 12 Pro (390px) specific optimizations */
            .view-toggles {
                gap: 4px;
            }
            .view-btn {
                padding: 0 4px;
                font-size: 10.5px;
                height: 32px;
                letter-spacing: -0.01em;
            }
            .view-btn svg {
                width: 11px;
                height: 11px;
            }

            .mode-mobile #preview-frame { transform: scale(0.7); margin-bottom: -240px; }
            .project-info h1 { font-size: 1.1rem; }
        }

        @media print {
            .header-row, .selector-w, #preview-container { display: none !important; }
            #top-nav { box-shadow: none; border: none; padding: 0; margin-bottom: 20px; width: 100%; max-width: 100%; }
            .meta-stack { background: transparent; border: none; border-bottom: 2px solid #000; border-radius: 0; padding: 0 0 15px 0; width: 100%; }
            #print-mount { display: block !important; width: 100%; height: auto; margin-top: 10px; }
            .wrapper { padding: 0; width: 100%; align-items: flex-start; }
            body, html { background: white; }
        }
    </style>
    <script>
        function preparePrint() {
            const frame = document.querySelector('iframe');
            const printMount = document.getElementById('print-mount');
            if (frame && frame.contentWindow) {
                printMount.innerHTML = frame.contentWindow.document.documentElement.innerHTML;
                setTimeout(() => { window.print(); }, 200);
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
                <div class="project-info"><h1><?php echo $projectName; ?></h1></div>
                <div class="view-toggles">
                    <a href="?f=<?php echo $currentFile; ?>&m=desktop" class="view-btn <?php echo $viewMode=='desktop'?'active':''; ?>">Desktop</a>
                    <a href="?f=<?php echo $currentFile; ?>&m=mobile" class="view-btn <?php echo $viewMode=='mobile'?'active':''; ?>">Mobile</a>
                    <a href="<?php echo htmlspecialchars($currentFile); ?>" target="_blank" class="view-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        Unwrap
                    </a>
                    <button onclick="preparePrint()" class="view-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        PDF
                    </button>
                </div>
            </div>

            <div class="selector-w">
                <form id="prooferForm" method="GET">
                    <input type="hidden" name="m" value="<?php echo $viewMode; ?>">
                    <select name="f" onchange="this.form.submit()">
                        <?php foreach ($groupedFiles as $label => $group): ?>
                            <optgroup label="── <?php echo htmlspecialchars($label); ?> ──">
                                <?php foreach ($group as $item): 
                                    $fullKey = $item['info']['key'];
                                    $text = $customLabels[$fullKey] ?? $item['file'];
                                ?>
                                    <option value="<?php echo $item['file']; ?>" <?php echo $currentFile == $item['file'] ? 'selected' : ''; ?>>
                                        (<?php echo strtoupper($item['info']['version']); ?>) <?php echo $text; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="meta-stack">
                <div class="meta-line"><b>Subject 1</b> <span><?php echo htmlspecialchars($meta['subject1']); ?></span></div>
                <div class="meta-line"><b>Preheader 1</b> <span><?php echo htmlspecialchars($meta['preheader1']); ?></span></div>
            </div>
        </nav>

        <main id="preview-container">
            <?php if ($currentFile): ?>
                <div id="preview-frame">
                    <iframe src="<?php echo $currentFile; ?>?t=<?php echo time(); ?>"></iframe>
                </div>
            <?php endif; ?>
        </main>
        
        <div id="print-mount"></div>
    </div>
</body>
</html>