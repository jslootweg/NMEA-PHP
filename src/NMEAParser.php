<?php

class NMEAParser
{
    public static function Parse($raw_message)
    {
    	$message = new RMCNMEAMessage($raw_message);
    	
    	return $message;
    }
    
    public function dmsTodecimal($dms, $EastingAndNorthing)
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
        
        return $d + ($m/60) + (($s*60)/3600);
        
        /*$degree=(int)($deg_coord/100); //simple way
        $minutes= $deg_coord-($degree*100);
        $dotdegree=$minutes/60;
        $decimal=$degree+$dotdegree;
        //South latitudes and West longitudes need to return a negative result
        if (($direction=="S") or ($direction=="W"))
        {
            $decimal=$decimal*(-1);
        }
        $decimal=number_format($decimal,$precision,'.',''); //truncate decimal to $precision places
        return $decimal;*/
    }
}

class NMEAMessage
{
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
		$this->raw_split = preg_split("/,/", $raw_message);
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
}
