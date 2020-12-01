<?php
require __DIR__.'/vendor/autoload.php';

use App\Event;
define('TITLE_LENGTH', 60);
define('DEFAULT_EVENT_COUNT', 10);
define('DEFAULT_DIRECTION', 'horizontal');
define('DEFAULT_BAR_COLOR', 'yellow');
define('DEFAULT_BG_COLOR', 'gray');

enableCORS();
if (! isset($_POST['calendarID'])) {
    return false;
}
$calendarID = $_POST['calendarID'];
$maxEventsCount =  $_POST['eventCount'] ?? DEFAULT_EVENT_COUNT;
$styleOptions = [
    'direction' => $_POST['direction'] ?? DEFAULT_DIRECTION,
    'barColor'  => $_POST['bar'] ?? DEFAULT_BAR_COLOR,
    'background'=> $_POST['background'] ?? DEFAULT_BG_COLOR,
];

$event = new Event($calendarID, $maxEventsCount);
$calendarName = $event->getCalendarName();
echo "<h1>Upcoming events for $calendarName:</h1>";
$events = $event->getEvents();
if (! $events) {
    echo "No events found";
} else {
    renderEvents($events, $styleOptions);
}

function renderEvents($events, $styleOptions)
{
    $barClass = getBarClass($styleOptions['barColor']);
    switch ($styleOptions['direction']) {
        case 'horizontal':
            renderHorizontal($events, $barClass);
            break;

        case 'vertical':
            renderVertical($events, $barClass, $styleOptions['background']);
            break;
    }
}

function renderHorizontal($events, $barClass)
{
?>
    <div class="event-container horizontal">
    <?php
        $columnCount = 1;
        foreach ($events as $event) {
            $eventTitle = $event['summary'];
            if ($columnCount > 3) {
    ?>
                </div>
                <div class="event-container horizontal">
    <?php
                $columnCount = 1;
            }
    ?>
            <div class="event-box gray-bg gray-border">
                <div class="date white-bg <?= $barClass; ?>"><?= $event['eventMonth']; ?> <?= $event['eventDay']; ?></div>
                <div class="event-detail">
                    <div class="event-title">
                        <a href="<?= $event['link']; ?>" title="<?= $eventTitle; ?>" target="_blank" noopener noreferrer><?= substr($eventTitle, 0, TITLE_LENGTH); ?></a>
                    </div>
                    <div><?= $event['eventDate']; ?></div>
                    <div><?= $event['location']; ?></div>
                    <div class="event-description"><?= $event['description']; ?></div>
                </div>
            </div>
    <?php
            $columnCount++;
        }
    ?>
    </div>
<?php
}

function renderVertical($events, $barClass, $boxBackground)
{
    $boxBackgroundClass = getBackgroudClass($boxBackground);
    $dateBackgroundClass = ($boxBackground == 'white') ? 'gray-bg' : 'white-bg';
?>
    <div class="event-container vertical">
    <?php
        foreach ($events as $event) {
    ?>
            <div class="event-box <?= $boxBackgroundClass; ?> gray-border">
                <div class="event-info">
                    <div class="date-box">
                        <div class="date <?= $dateBackgroundClass; ?> <?= $barClass; ?>"><?= $event['eventAbbrMonth'] ;?><br><?= $event['eventDay']; ?></div>
                    </div>
                    <div class="event-detail">
                        <div class="event-title"><a href="<?= $event['link']; ?>" title="Event Link" target="_blank" noopener noreferrer><?= $event['summary']; ?></a></div>
                        <div><?= $event['eventDate']; ?></div>
                        <div><?= $event['location']; ?></div>
                        <div class="event-description"><?= $event['description']; ?></div>
                    </div>
                </div>
            </div>
    <?php
        }
    ?>
    </div>
<?php
}

function getBackgroudClass($background)
{
    switch ($background) {
        case 'white':
            return 'white-bg';

        case 'gray':
            return 'gray-bg';
    }
}

function getBarClass($barColor)
{
    switch ($barColor) {
        case 'yellow':
            return 'yellow-bar';

        case 'green':
            return 'green-bar';

        case 'darkgreen':
            return 'darkgreen-bar';
    }
}

function enableCORS()
{
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 86400');//cache for 1 day
        
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
            // may also be using GET, POST, OPTIONS, PUT, PATCH, HEAD etc
            header('Access-Control-Allow-Methods: POST');
        }
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }

        exit(0);
    }
}
