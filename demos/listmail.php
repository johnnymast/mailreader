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