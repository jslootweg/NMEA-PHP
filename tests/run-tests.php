<?php

class NMEATest extends PHPUnit_Framework_TestCase
{
    public function testDMStoDecimalConversion()
    {
        $result = NMEAParser::dmsTodecimal("3723.2475", "N");
        
        $this->assertEquals(37.3874583, $result, '', 0.0000001);
    }
    
    /**
     * @depends testDMStoDecimalConversion
     */
    public function testCanParseRMC_1()
    {
        $message = NMEAParser::Parse('$GPRMC,161229.487,A,3723.2475,N,12158.3416,W,0.13,309.62,120598, ,*10');

        // Assert
        $this->assertEquals("GPRMC", $message->MessageID);
        //$this->assertEquals("161229.487", $message->UTCTimeRaw);
        
        $expectedTime = mktime(16,12,29,05,12,98);
        $this->assertEquals($expectedTime, $message->getUTCTimestamp());
        
        $this->assertEquals("A", $message->getStatus());
    }
}