<?php

date_default_timezone_set('UTC');

class NMEATest extends PHPUnit_Framework_TestCase
{
    public function testDMStoDecimalConversion()
    {
        $result = NMEAParser::dmsTodecimal("3723.2475", "N");
        
        $this->assertEquals(37.3874583, $result, '', 0.0001);
    }
    
    public function testDMStoDecimalConversion_CanDealWithSouthing()
    {
        $result = NMEAParser::dmsTodecimal("3723.2475", "S");
        
        $this->assertEquals(-37.3874583, $result, '', 0.0001);
    }
    
    public function testDMStoDecimalConversion_Longitude()
    {
        $result = NMEAParser::dmsTodecimal("12158.3416", "W");
        
        $this->assertEquals(121.9724, $result, '', 0.0001);
    }
    
    public function testDMStoDecimalConversion_LongitudeWithEasting()
    {
        $result = NMEAParser::dmsTodecimal("12158.3416", "E");
        
        $this->assertEquals(-121.9724, $result, '', 0.0001);
    }
    
    /**
     * @depends testDMStoDecimalConversion
     * @depends testDMStoDecimalConversion_CanDealWithSouthing
     * @depends testDMStoDecimalConversion_Longitude
     * @depends testDMStoDecimalConversion_LongitudeWithEasting
     */
    public function testCanParseRMC_1()
    {
    	$raw = '$GPRMC,161229.487,A,3723.2475,N,12158.3416,W,0.13,309.62,120598, ,*30';
    
        $message = NMEAParser::Parse($raw);

        // Assert
        $this->assertEquals("GPRMC", $message->MessageID);
        //$this->assertEquals("161229.487", $message->UTCTimeRaw);
        
        $expectedTime = mktime(16,12,29,05,12,98);
        $this->assertEquals($expectedTime, $message->getUTCTimestamp());
        
        $this->assertEquals("A", $message->getStatus());
        
        $this->assertEquals(37.3874583, $message->getLat(), '', 0.0001);
        
        $this->assertEquals(121.9724, $message->getLong(), '', 0.0001);
        
        $this->assertEquals(0.13, $message->getSpeedKnots());
        
        $this->assertEquals(0.1496, $message->getSpeedMph(), '', 0.0001);
        
        $this->assertEquals(0.24076, $message->getSpeedKph(), '', 0.0001);
        
        $this->assertEquals(0.06687, $message->getSpeedMs(), '', 0.0001);
        
        $this->assertEquals(309.62, $message->getTrueCourse());
        
        $this->assertTrue($message->IsCheckSumValid());
    }
}