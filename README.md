# Google Calendar Events

Display public calendar events from Google Calendar on a webpage.

## Steps to enable Google Calendar API

1. You need to enable Google Calendar API by going to the URL below:
   https://developers.google.com/calendar/quickstart/php

2. Download the credentials.json file and save it to the location where the PHP script will reside.

    If you rename the file '**credentials.json**' to something else, then update that new name on the file **src/GoogleAPI.php**:

    `$client->setAuthConfig('CREDENTIAL-FILE-NAME.json');`

3. Run `composer install`, if not ran already.

4. Run the command `php initial_setup.php` to create an access token for the Google Calendar API using the credential file downloaded in step 2.
