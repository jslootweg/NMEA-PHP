<?php

class NMEAParser
{
    public static function Parse($raw_message)
    {
        $message = new RMCNMEAMessage($raw_message);
        
        return $message;
    }
    
    public static function dmsTodecimal($dms, $EastingAndNorthing)
    {
        $split = preg_split("/\./", $dms);
        $strLength = strlen($split[0]);
        if($strLength == 4)
        {
            // lat
            $d = substr($dms, 0, 2);
            $m = substr($dms, 2, 2);
            $s = "0." . $split[1];
        }
        else if($strLength == 5)
        {
            // long
            $d = substr($dms, 0, 3);
            $m = substr($dms, 3, 2);
            $s = "0." . $split[1];
        }
        else
        {
            throw new Exception("Invalid DMS format for '$dms'");    
        }
        
        $dec = $d + ($m/60) + (($s*60)/3600);
        
        if((strncmp($EastingAndNorthing, "S", 1) == 0) || (strncmp($EastingAndNorthing, "E", 1) == 0))
        {
            $dec = -1 * $dec;
        }
        
        return $dec;
    }
    
    public static function knotsToMph($knots)
    {
        // 1 Nautical mile is 1.150779 mile
        return $knots * 1.150779;
    }
    
    public static function knotsToKph($knots)
    {
        // 1 Nautical mile is exactly 1.852 km
        return $knots * 1.852;
    }
    
    public static function knotsToMs($knots)
    {
        // 1 Knot is 0.514444 m/s
        return $knots * 0.514444;
    }
}

class NMEAMessage
{
	protected $raw;
	protected $raw_split;
    
    private function NMEADateAndTimeToTimestamp($utcTime, $date)
    {
        // Get day components
        $day = substr($date,0,2);
        $month = substr($date,2,2);
        $year = substr($date,4,2);
        
        // Get time components
        $hour = substr($utcTime,0,2);
        $minute = substr($utcTime,2,2);
        $seconds = substr($utcTime,4,2);
        
        // create time stamp
        return mktime($hour, $minute, $seconds, $month, $day, $year);
    }
	
	public function getUTCTimestamp()
	{
	    return $this->NMEADateAndTimeToTimestamp($this->raw_split[1], $this->raw_split[9]);
    }
	
	public function __construct($raw_message)
	{
		$this->raw = $raw_message;
		$this->raw_split = preg_split("/,/", $raw_message);
	}
	
	public function IsCheckSumValid()
	{
		$charPos = strpos($this->raw, "*");
		
		// Get the string between the $ and the *
        $sub = substr($this->raw, 1, $charPos-1);
        
        $res = 0;        
        for($i=0;$i<strlen($sub);$i++)
        {
        	// XOR each byte
        	$res ^= ord($sub[$i]);
        }
        
        // Convert to hex
        $calculatedChecksum = dechex($res);
        
        return $calculatedChecksum === substr($this->raw, $charPos+1);
	}
}

class RMCNMEAMessage extends NMEAMessage
{
	public $MessageID = "GPRMC";
    
    public function getStatus()
    {
        return $this->raw_split[2];
    }
	
	public function __construct($raw_message)
	{
		parent::__construct($raw_message);
	}
	
	public function getLat()
	{
		return NMEAParser::dmsTodecimal($this->raw_split[3], $this->raw_split[4]);
	}
	
	public function getLong()
	{
		return NMEAParser::dmsTodecimal($this->raw_split[5], $this->raw_split[6]);
	}
	
	public function getSpeedKnots()
	{
		return $this->raw_split[7];
	}
	
	public function getSpeedMph()
	{
		return NMEAParser::knotsToMph($this->raw_split[7]);
	}
	
	public function getSpeedKph()
	{
		return NMEAParser::knotsToKph($this->raw_split[7]);
	}
	
	public function getSpeedMs()
	{
		return NMEAParser::knotsToMs($this->raw_split[7]);
	}
	
	public function getTrueCourse()
	{
		return $this->raw_split[8];
	}
}
