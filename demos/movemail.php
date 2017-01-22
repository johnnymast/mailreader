<?php
require 'autoload.php';

use JM\MailReader\MailReader;

try {
    $reader = new MailReader();

    /**
     * Note: port is an additional option to set.
     *       This option is not required.
     */
    $reader->connect([
        'server'   => 'foobar.com',
        'username' => 'foo@bar.com',
        'password' => 'bar'
    ]);

    $mailbox = 'Never seen mailbox';
    $targetEmailAddress = 'foo@bar.com';

    // Create the mailbox (folder) if it does not already exist.
    if ($reader->mailboxExists($mailbox) === false) {
        $reader->createMailbox($mailbox);
    }

    $messages = $reader->filterUnReadMessagesTo($targetEmailAddress);

    if (is_array($messages) && count($messages) > 0) {
        foreach ($messages as $message) {
            $reader->moveMessage($message['index'], $mailbox);
        }
    }

    // Switch to mailbox (folder) $mailbox
    $reader->setMailbox($mailbox);

    // Retrieve the messages in mailbox (folder) $mailbox
    $reader->setMailbox($mailbox);
    $messages = $reader->readMailbox();

    // List emails in mailbox (folder) $mailbox
    if (is_array($messages) && count($messages) > 0) {
        foreach ($messages as $email) {
            $header = $email['header'];

            // Assuming CLI here
            print $header->Subject."\n";
        }
    }

} catch (\Exception $e) {

}
