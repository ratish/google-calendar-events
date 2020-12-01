<?php

use App\GoogleAPI;
use App\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /** @test */
    public function testGetEvents()
    {
        $event = new Event('PUBLIC_CALENDAR_ID@group.calendar.google.com', 10);
        $this->assertIsArray($event->getEvents());
    }

    public function testGetCalendarName()
    {
        $event = new Event('PUBLIC_CALENDAR_ID@group.calendar.google.com', 10);
        $this->assertEquals('CAB Events', $event->getCalendarName());
    }
}
