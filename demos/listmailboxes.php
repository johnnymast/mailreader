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
     * Note: You can can also use MailReader::getPrettyMailboxes
     *       To get a polished list.
     */
    $mailboxes = $reader->getPrettyMailboxes();

    if (is_array($mailboxes) && count($mailboxes) > 0) {
        foreach($mailboxes as $mailbox) {
            // Assuming CLI here
            print $mailbox."\n";
        }
    }

} catch (Exception $e) {
    print $e->getMessage();
}

