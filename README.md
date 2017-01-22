
# MailReader

MailReader allows you to perform basic mail tasks like listing/moving and deleting mail on your mailserver.
The following operations are supported out of the box.

 - List/Delete/Move mail
 - List/Create/Delete mailboxes (folders)

For some basic examples checkout the demos folder in this repository.
  

## Requirements

The following versions of PHP are supported by this version.

+ PHP 5.4
+ PHP 7.0
+ PHP 7.1
+ HHVM
+ Php_imap (core php extension) 

However for development and testing you will need php 5.6 as this is the minimal required for PhpUnit.
  

## Simple to use

List email in your inbox.

```php

use JM\MailReader\MailReader;

try {
    $reader = new MailReader();
    $reader->connect([
        'server'   => 'foobar.com',
        'username' => 'foo@bar.com',
        'password' => 'bar'
    ]);

    /**
     * Note: Uncomment the following line if
     *       you wish to read a mailbox (folder)
     */
    //$reader->setMailbox('Sent');

    // messages will now contain the emails you received
    $messages = $reader->readMailbox();

    if (is_array($messages) && count($messages) > 0) {
        foreach($messages as $email) {
            $header = $email['header'];

            // Assuming CLI here
            print $header->Subject."\n";
        }
    }
} catch (\Exception $e) {
    print $e->getMessage();
}

```

Find email sent to "foo@bar.com" and display them (handy if you have a catch all mailbox).

```php

use JM\MailReader\MailReader;

try {
    $reader = new MailReader();
    $reader->connect([
        'server'   => 'foobar.com',
        'username' => 'foo@bar.com',
        'password' => 'bar'
    ]);

    /**
     * Note: Uncomment the following line if
     *       you wish to read a mailbox (folder)
     */
    //$reader->setMailbox('Sent');

    // messages will now contain the emails you received
    $messages = $reader->readMailbox();

    if (is_array($messages) && count($messages) > 0) {
        foreach($messages as $email) {
            $header = $email['header'];

            // Assuming CLI here
            print $header->Subject."\n";
        }
    }
} catch (\Exception $e) {
    print $e->getMessage();
}
```

Filter unread email for foo@bar.com and move it to folder "Never seen mailbox". This mailbox (folder) will be created it it does not exist yet.


```php 

use JM\MailReader\MailReader;

/**
 * Note: port is an additional option to set.
 *       This option is not required.
 */
$reader->connect([
    'server'   => 'foobar.com',
    'username' => 'foo@bar.com',
    'password' => 'bar'
]);

$mailbox = 'Unread';
$targetEmailAddress = 'foo@bar.com';

// Create the mailbox (folder) if it does not already exist.
if ($reader->mailboxExists($mailbox) == false) {
    $reader->createMailbox($mailbox);
}

$messages = $reader->filterUnReadMessagesTo($targetEmailAddress);

if (is_array($messages) && count($messages) > 0) {
    foreach($messages as $message) {
        $reader->moveMessage($message['index'], $mailbox);
    }
}

// Switch to mailbox (folder) $mailbox
$reader->setMailbox('unseen');

// Retrieve the messages in mailbox (folder) $mailbox
$reader->setMailbox($mailbox);
$messages = $reader->readMailbox();

```
 
## Author

This package is created and maintained by [Johnny Mast](https://github.com/johnnymast).

## License

The MIT License (MIT)

Copyright (c) 2017 Johnny Mast

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
