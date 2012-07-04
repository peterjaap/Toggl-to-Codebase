Toggl-to-Codebase
=================

Automatically synchronize your Toggl entries to time sessions in Codebase

* Author: Peter Jaap Blaakmeer http://blaakmeer.com
* Twitter: @PeterJaap
* License: GPLv2/MIT

## Requirements ##

* PHP 5.0 or higher
* [cURL](http://us.php.net/manual/en/book.curl.php) extension
* [SQLite3](http://php.net/manual/en/book.sqlite3.php) extension
* [davidreid's Toggl PHP SDK](https://github.com/davereid/toggl-php-sdk)
* [peterjaap's Codebase PHP wrapper](https://github.com/peterjaap/Codebase-PHP-wrapper)

## Configuration ##
* Place the script somewhere convenient
* Edit the details at the top of the file ($verbose will output all log calls, $sqlOutput will log the content of the SQLite database, $dryrun is used for testing and $dateFrom is the date from when the time entries will be retrieved).
* Insert your employee's names in the $employees array
* Insert the API credentials for Toggl and Codebase for your employees
* Set up a post commit hook on your repository or set up a cronjob to run the script every X minutes
* Let it rip!

## Changelog ##
* v1.0 - Released first version