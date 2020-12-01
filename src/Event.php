<?php
namespace App;

use App\GoogleAPI;
use Google_Service_Calendar;

class Event
{
    private $calendarId;
    private $optParams = [];

    public function __construct($calendarId, $maxEventsCount)
    {
        $this->calendarId = $calendarId;
        $this->optParams = [
            'maxResults'    => ($maxEventsCount < 1) ? 1 : $maxEventsCount,
            'orderBy'       => 'startTime',
            'singleEvents'  => true,
            'timeMin'       => date('c'),
        ];
    }

    public function getEvents()
    {
        $calendar = $this->getCalendarServiceClient();
        $results = $calendar->events->listEvents($this->calendarId, $this->optParams);

        if ($events = $results->getItems()) {
            return $this->formatEvents($events);
        }

        return false;
    }

    public function getCalendarName()
    {
        $calendar = $this->getCalendarServiceClient();
        $calendarInfo = $calendar->calendars->get($this->calendarId);

        return $calendarInfo->getSummary();
    }

    private function getCalendarServiceClient()
    {
        $googleAPI = new GoogleAPI;
        $client = $googleAPI->getClient();

        return new Google_Service_Calendar($client);
    }

    private function formatEvents($events)
    {
        return array_map(function($event) {
            $startDateTime = $this->assignDefaultIfEmpty($event->start->dateTime,  $event->start->date);
            $endDateTime = $this->assignDefaultIfEmpty($event->end->dateTime,  $event->end->date);
            $startDate = new \DateTime($startDateTime);
            $formattedEvent = [
                'startDateTime' => $startDateTime,
                'endDateTime'   => $endDateTime,
                'eventDate'     => $this->renderEventDate($startDateTime, $endDateTime),
                'eventDay'      => $startDate->format('j'),
                'eventMonth'    => $startDate->format('F'),
                'eventAbbrMonth'=> $startDate->format('M'),
                'link'          => $event->htmlLink,
                'summary'       => $event->getSummary(),
                'location'      => $this->assignDefaultIfEmpty($event->getLocation()),
                'description'   => $this->assignDefaultIfEmpty($event->getDescription()),
            ];

            return $formattedEvent;
        }, $events);
    }

    private function assignDefaultIfEmpty($value, $default = '')
    {
        return (empty($value)) ? $default : $value;
    }

    private function renderEventDate($startDateTime, $endDateTime)
    {
        $startDate = new \DateTime($startDateTime);
        $endDate = new \DateTime($endDateTime);
        $interval = $startDate->diff($endDate);
        $diffDay = $interval->d;
        $diffHour = $interval->h;
        if ($this->isOneDayEvent($diffDay, $diffHour)) {
            return $startDate->format('F j');
        }
        if ($this->isEndTimeAvailable($diffDay, $diffHour)) {
            return $startDate->format('F j \a\t g:i A') . " - " . $endDate->format('g:i A');
        }
        if ($this->isMultipleDayEvent($diffDay)) {
            if ($this->doesEventStartAndEndAtMidNight($startDate, $endDate)) {
                return $startDate->format('F j') . " - " . $endDate->format('F j');
            }
            return $startDate->format('F j \a\t g:i A') . " - " . $endDate->format('F j g:i A');
        }
    }

    private function isOneDayEvent($day, $hour)
    {
        return ($day === 1 && $hour === 0);
    }

    private function isEndTimeAvailable($day, $hour)
    {
        return ($day === 0 && $hour > 0);
    }

    private function isMultipleDayEvent($day)
    {
        return ($day > 1);
    }

    private function doesEventStartAndEndAtMidNight($startDate, $endDate)
    {
        return ($startDate->format('g:i A') === '12:00 AM' && $endDate->format('g:i A') === '12:00 AM');
    }

    // private function getEventDay($startDateTime)
    // {
    //     $startDate = new \DateTime($startDateTime);
    //     return $startDate->format('j');
    // }

    // private function getMonthDay($startDateTime)
    // {
    //     $startDate = new \DateTime($startDateTime);
    //     return $startDate->format('F j');
    // }

    // private function get3LetterMonthDay($startDateTime)
    // {
    //     $startDate = new \DateTime($startDateTime);
    //     return $startDate->format('M j');
    // }
}
