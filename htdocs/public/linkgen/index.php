<?php



// ==========================================
// Usage Example
// ==========================================

// Your encryption key MUST be exactly 32 bytes (256 bits) for AES-256
// Generate a real one using base64_encode(openssl_random_pseudo_bytes(32))
$PATH = __DIR__ . '/../../private';
require_once $PATH  . "/src/links.php";
require_once $PATH  . "/src/config.php";

$resource = NULL;
$links = [];
$resources = [
    // "leadership-case-study-1.html",
    // "leadership-case-study-2.html",
    // "leadership-case-study-3.html",
    // "leadership-case-study-4.html",
    "digitech.php",
];

$validSeconds = Config::$EXPIRE_TIME_SECONDS;
$startTime = time();

if (isset($_GET['validSeconds'])) {
    $validSeconds = $_GET['validSeconds'];
}

if (isset($_GET['startTime'])) {
    $startTime = $_GET['startTime'];
}

$timeZone = new DateTimeZone('Africa/Johannesburg');

$DATE_FORMAT = 'H:i:s (d M Y)';

$tempDate = new DateTime();
$tempDate->setTimezone($timeZone);

$tempDate->setTimestamp($startTime);
$currStartTimeStr = $tempDate->format($DATE_FORMAT );

$tempDate->setTimestamp($startTime + $validSeconds);
$currExpTimeStr = $tempDate->format($DATE_FORMAT );


$N_START_TIMES = 48;

$exp_times_mins = [];
$start_date_strings = [];
$start_date_timestamps = [];
$resource = $resources[0];
$resource_set = isset($_GET['resource']);

if ($resource_set) {
    $resource = $_GET['resource'];
}

for ($i = 0; $i < 50; $i++) {
    $links[$i] = generateLink($resource, $startTime, $validSeconds);
};
for ($i = 0; $i < 48; $i++) {
    $exp_times_mins[$i] = 30 + $i * 30;
}

// 1. Get the current time
$date = new DateTime();
$date->setTimezone($timeZone);

// 2. Get the current minute and second
$minutes = (int)$date->format('i');
$seconds = (int)$date->format('s');

// 3. Calculate total minutes (including fractional minutes from seconds)
$totalMinutes = $minutes + ($seconds / 60);

// 4. Round to the nearest 30 minutes
// (e.g., 14 mins becomes 0, 16 mins becomes 30, 46 mins becomes 60)
$roundedMinutes = floor($totalMinutes / 30) * 30;

// 5. Apply the rounded time to the object
$date->setTime((int)$date->format('H'), 0, 0); // Reset mins/secs to 00:00
$date->modify("+$roundedMinutes minutes");    // Add back the rounded minutes

// 3. Define a 30-minute interval
$interval = new DateInterval('PT30M');

// 4. Loop 48 times (24 hours total)
for ($i = 0; $i < $N_START_TIMES; $i++) {
    $timestamp = $date->getTimestamp();
    $dateString = $date->format($DATE_FORMAT );

    $start_date_timestamps[$i] = $timestamp;
    $start_date_strings[$i] = $dateString;

    // Move the date forward by 30 minutes for the next iteration
    $date->add($interval);
}

function toLink(string $link)
{
    return Config::$URL . '/' . $link;
}

$t = microtime(true);

function getClassExp(int $e) {
    global $validSeconds;
    if($e * 60 === intval($validSeconds)) {
        return "selected";
    } 
    return "";
}

function getClassStart(int $e) {
    global $startTime;
    if($e === intval($startTime)) {
        return "selected";
    } 
    return "";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Link Generation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-card: rgb(9 9 11);
            --text-main: #ffffff;
            --text-muted: #e0e0e0;
            --accent-color: #3bd4d8;
            --border-color: rgba(255, 255, 255, 0.15);
            --ease-smooth: cubic-bezier(0.25, 1, 0.5, 1);
        }

        html {
            scroll-behavior: smooth;
            font-family: 'Montserrat', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-main);
            line-height: 1.8; 
            font-size: 14px; 
            transition: color 0.3s var(--ease-smooth);
            background-color: #000000;
        }

        html::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -10;

            pointer-events: none; 
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 29px, rgba(255, 255, 255, 0.03) 29px, rgba(255, 255, 255, 0.02) 30px),
                repeating-linear-gradient(90deg, transparent, transparent 29px, rgba(255, 255, 255, 0.03) 29px, rgba(255, 255, 255, 0.01) 30px),
                repeating-linear-gradient(45deg, transparent, transparent 41px, rgba(255, 255, 255, 0.02) 41px, rgba(255, 255, 255, 0.01) 42px),
                repeating-linear-gradient(135deg, transparent, transparent 41px, rgba(255, 255, 255, 0.02) 41px, rgba(255, 255, 255, 0.01) 42px),
                linear-gradient(180deg, rgb(24 24 27), rgb(9 9 11) 100%);
            background-size: 300px 300px, 300px 300px, 300px 300px, 300px 300px, 100% 100%;
            animation: gridWave 40s infinite linear;
        }

        body {
            margin: 0 auto;
            padding: 3.5rem 2rem;
            min-height: 100vh;
            background-color: transparent;
            max-width: 100rem;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
        }

        @keyframes gridWave {
            0% { background-position: 0px 0px, 0px 0px, 0px 0px, 0px 0px, 0% 0%; }
            100% { background-position: 300px 600px, -600px 300px, 600px 600px, -300px 300px, 0% 0%; }
        }

        h1 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--accent-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        h1:nth-of-type(4), 
        h1:nth-of-type(5), 
        h1:nth-of-type(6) {
            text-transform: none;
            font-size: 1.25rem;
            color: var(--text-main);
            border-bottom: none;
            margin: 0.7rem 0;
            padding-bottom: 0;
            letter-spacing: normal;
            background: rgba(255, 255, 255, 0.02);
            padding: 0.8rem 1.2rem;
            border-left: 3px solid var(--accent-color);
            border-radius: 0 6px 6px 0;
        }

        h3 {
            margin: 0.5rem 0 1.5rem 0;
        }

        h3 a {
            display: inline-block;
            text-decoration: none;
            color: #000000;
            background-color: var(--accent-color);
            font-weight: 600;
            font-size: 1.05rem;
            padding: 0.6rem 1.5rem;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(59, 212, 216, 0.2);
            transition: transform 0.2s var(--ease-smooth), box-shadow 0.2s var(--ease-smooth);
            position: relative;
            z-index: 5;
        }

        h3 a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 212, 216, 0.4);
        }

        p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 0.8rem;
        }

        .grid-8-columns {
            display: grid;
            grid-template-columns: repeat(8, minmax(0, 1fr));
            gap: 16px;
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
        }

        .grid-8-columns li a {
            display: block;
            text-align: center;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 490;
            padding: 12px 6px;
            color: var(--text-muted);
            background: rgba(0, 0, 0, 0.4);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            transition: all 0.2s var(--ease-smooth);
            word-break: break-word;
            position: relative;
            z-index: 5;
        }

        .grid-8-columns li a:hover {
            color: #000000;
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(59, 212, 216, 0.3);
        }

        @media (max-width: 1200px) {
            .grid-8-columns { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        @media (max-width: 768px) {
            .grid-8-columns { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        input[type="number"] {
            background-color: rgba(0,0,0,0.5) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 6px !important;
            color: var(--text-main) !important;
            outline: none;
            font-family: monospace;
            transition: border-color 0.2s ease;
            position: relative;
            z-index: 5;
        }

        input[type="number"]:focus {
            border-color: var(--accent-color) !important;
        }

        button#copyBtn {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600 !important;
            background-color: transparent !important;
            border: 1px solid var(--accent-color) !important;
            color: var(--accent-color) !important;
            transition: all 0.2s var(--ease-smooth) !important;
            position: relative;
            z-index: 5;
        }

        button#copyBtn:hover {
            background-color: var(--accent-color) !important;
            color: #000000 !important;
            box-shadow: 0 0 15px rgba(59, 212, 216, 0.2);
        }

        #links {
            list-style: none;
            padding: 0;
            margin: 2rem 0 0 0;
            background: rgba(9, 9, 11, 0.7);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            max-height: 450px;
            overflow-y: auto;
            backdrop-filter: blur(5px);
        }

        #links li {
            padding: 12px 18px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 0;
        }

        #links li:last-child {
            border-bottom: none;
        }

        #links li a {
            color: var(--accent-color);
            text-decoration: none;
            font-family: monospace;
            font-size: 1rem;
            word-break: break-all;
            transition: color 0.15s ease;
        }

        #links li a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .selected {
            background-color: var(--accent-color) !important;
            color: #000000 !important;
        }

    </style>
</head>

<body>
    <h1>Available Resources:</h1>
    <?php foreach ($resources as $r): ?>
        <h3><a href=<?= "/linkgen/?resource=" . $r . "&startTime=" . $startTime . "&validSeconds=" . $validSeconds ?>><?= $r  ?></a></h3>
    <?php endforeach; ?>
    <?php if ($resource_set): ?>
        <h1>Set expiration:</h1>
        <ul class="grid-8-columns ">
            <?php foreach ($exp_times_mins as $e): ?>
                <li
                
                ><a            
                    class="<?= getClassExp($e)?>"         
                    href=<?= "/linkgen/?resource=" . $resource . "&startTime=" . $startTime . "&validSeconds=" . ($e * 60) ?>>
                    <?= (round($e / 60, 1)) . " hour(s)"  ?>
                </a></li>
            <?php endforeach; ?>
        </ul>
        <h1>Set start time:</h1>
        <ul class="grid-8-columns ">
            <?php for ($i = 0; $i < $N_START_TIMES; $i++): ?>
                <li><a 
                    class="<?= getClassStart($start_date_timestamps[$i])?>"  
                    href=<?= "/linkgen/?resource=" . $resource . "&startTime=" . $start_date_timestamps[$i] . "&validSeconds=" . ($validSeconds) ?>>
                    <?= $start_date_strings[$i]  ?>
                </a></li>
            <?php endfor; ?>
        </ul>
        <h1>Current Resource: <?= $resource ?></h1>
        <h1>Current Start Time: <?php echo $currStartTimeStr ?></h1>
        <h1>Current Expiry Time: <?php echo $currExpTimeStr ?></h1>
        <p>Enter the number of links to copy:</p>
        <div style="display: flex; gap: 8px; margin-bottom: 16px;">
            <input type="number" id="copyCount" value="10" min="1" max="<?= count($links) ?>" style="padding: 8px; font-size: 16px; width: 120px;">
            <button id="copyBtn" style="padding: 8px 16px; font-size: 16px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 4px;">Copy to Clipboard</button>
        </div>
        <ul id="links">
            <?php foreach ($links as $link): ?>
                <li>
                    <a href=<?= toLink($link) ?>><?= toLink($link) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif ?>
</body>

</html>