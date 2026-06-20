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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Link Generation</title>
    <style>
        .grid-8-columns {
            display: grid;
            /* Creates 8 equal-width columns */
            grid-template-columns: repeat(8, minmax(0, 1fr));

            /* Adds space between the grid items (adjust as needed) */
            gap: 16px;

            /* Optional: Removes default list bullets and padding */
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        :root {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 100rem;
        }

        body {
            padding: 2rem;
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
                <li><a href=<?= "/linkgen/?resource=" . $resource . "&startTime=" . $startTime . "&validSeconds=" . ($e * 60) ?>><?= (round($e / 60, 1)) . " hour(s)"  ?></a></li>
            <?php endforeach; ?>
        </ul>
        <h1>Set start time:</h1>
        <ul class="grid-8-columns ">
            <?php for ($i = 0; $i < $N_START_TIMES; $i++): ?>
                <li><a href=<?= "/linkgen/?resource=" . $resource . "&startTime=" . $start_date_timestamps[$i] . "&validSeconds=" . ($validSeconds) ?>><?= $start_date_strings[$i]  ?></a></li>
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