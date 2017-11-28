<?php
require 'autoload.php';

use JM\MailReader\MailReader;

try {
    $reader = new MailReader();
    $reader->connect([
        'server'   => 'foobar.com',
        'username' => 'foo@bar.com',
        'password' => 'bar'
    ]);

    // messages will now contain the emails you received
    $messages = $reader->filterTo('foo@bar.com');

    if (is_array($messages) && count($messages) > 0) {
        foreach ($messages as $email) {
            $header = $email['header'];

            // Assuming CLI here
            print $header->Subject."\n";
        }
    }
} catch (\Exception $e) {
    print $e->getMessage();
}