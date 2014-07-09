NMEA-PHP
========

NMEA-PHP is a helper library for parsing,converting and validating NMEA messages.

Current Support
---------------

- GPRMC - Recommended minimum specific GPS/Transit data

Testing
-------

NMEA-PHP is built and tested using [PHPUnit](http://phpunit.de).  Once you have this installed and configured on your system you can run all the tests from NMEA-PHP by executing

	phpunit --bootstrap src/autoload.php test/run-tests.php