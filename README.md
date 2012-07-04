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
* Place the script somewhere convenient (make sure the directory & tracktime.log are writable by the webserver)
* Edit the details at the top of the file ($verbose will output all log calls, $sqlOutput will log the content of the SQLite database, $dryrun is used for testing and $dateFrom is the date from when the time entries will be retrieved).
* Insert your employee's names in the $employees array
* Insert the API credentials for Toggl and Codebase for your employees
* Set up a post commit hook on your repository or set up a cronjob to run the script every X minutes
* Let it rip!

## Ticket syntax ##
* Match the project name in Toggl with the project name in Codebase (case insensitive)
* If it can't match the project name, it will try the first word of your description to find a match
* If it still can't match the project name, the time entry won't be added in Codebase
* Add, for example, '#12' to the ticket description to add the time entry to ticket #12 within the found project

## Changelog ##
* v1.0 - Released first version