<?php

class NMEATest extends PHPUnit_Framework_TestCase
{
    public function testCanBeNegated()
    {
        $message = NMEAParser::Parse('$GPRMC,161229.487,A,3723.2475,N,12158.3416,W,0.13,309.62,120598, ,*10');

        // Assert
        $this->assertEquals("GPRMC", $message->MessageID);
        $this->assertEquals("161229.487", $message->UTCTimeRaw);
        
        $date = new DateTime();
        $date->setTime(16, 12, 29);
        $this->assertEquals("161229.487", $message->UTCTime);
    }
}