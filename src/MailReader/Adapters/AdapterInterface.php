<?php

namespace JM\MailReader\Adapters;

interface AdapterInterface
{
    /**
     * Set the current mailbox
     *
     * @param string $mailbox
     * @return bool
     */
    public function setMailbox($mailbox = ''): bool;

    /**
     * Return the name of the current mailbox.
     *
     * @return string
     */
    public function getMailbox(): string;

    /**
     * Return a list of available mailboxes.
     *
     * @return array
     */
    public function getMailboxes() : array;

    /**
     * Check if a mailbox exists.
     *
     * @param string $name
     * @return bool
     */
    public function mailboxExists($name = '') : bool;

    /**
     * Read the current mailbox
     *
     * @return array
     */
    public function readMailbox() : array;
}