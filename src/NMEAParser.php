<?php

class NMEAParser
{
    public static function Parse($raw_message)
    {
    	$message = new RMCNMEAMessage($raw_message);
    	
    	return $message;
    }
}

class NMEAMessage
{
	protected $raw_split;
	
	public function __get($UTCTimeRaw)
	{
	    return $this->raw_split[1];
    }
	
	public function __construct($raw_message)
	{
		$this->raw_split = preg_split("/,/", $raw_message);
	}
}

class RMCNMEAMessage extends NMEAMessage
{
	public $MessageID = "GPRMC";
	
	public function __construct($raw_message)
	{
		parent::__construct($raw_message);
	}
}
