<?php

namespace JM\MailReader\Adapters\Core;

use JM\MailReader\Adapters\AdapterAbstract;
use JM\MailReader\Adapters\AdapterInterface;
use JM\MailReader\Credentials;
use JM\MailReader\Email;

class Core extends AdapterAbstract implements AdapterInterface
{
    /**
     * @var resource
     */
    private $conn;

    /**
     * @param \JM\MailReader\Credentials $credentials
     * @return bool
     */
    public function connect(Credentials $credentials): bool
    {
        $this->credentials = $credentials;

        return $this->authenticate();
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        if ($this->conn) {
            return imap_close($this->conn);
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function authenticate(): bool
    {
        $this->conn = @imap_open('{'.$this->credentials->getHost().':'.$this->credentials->getPort().'/notls}', $this->credentials->getUsername(), $this->credentials->getPassword());

        return ($this->conn !== false);
    }

    /**
     * @param string $mailbox
     * @return bool
     */
    public function setMailbox($mailbox = ''): bool
    {
        if ($mailbox == 'INBOX') {
            $mailbox = "{".$this->credentials->getHost()."}INBOX";
        } else {
            $mailbox = "{".$this->credentials->getHost()."}INBOX.".imap_utf7_encode($mailbox);
        }

        $result = false;

        if ($this->conn) {
            $result = imap_reopen($this->conn, $mailbox);
            $this->mailbox = $mailbox;
        }

        return ($result !== false);
    }

    /**
     * Return the name of the current mailbox.
     *
     * @return string
     */
    public function getMailbox(): string
    {
        $mailbox = str_replace("{".$this->credentials->getHost()."}", '', $this->mailbox);
        $mailbox = (substr($mailbox, 0, 6) == 'INBOX.') ? substr($mailbox, -6) : $mailbox;

        return $mailbox;
    }

    /**
     * Return a list of available mailboxes.
     *
     * @return array
     */
    public function getMailboxes(): array
    {
        return imap_list($this->conn, "{".$this->credentials->getHost()."}", "*");
    }

    /**
     * Check if a mailbox exists.
     *
     * @param string $name
     * @return bool
     */
    public function mailboxExists($name = ''): bool
    {
        if (empty($name)) {
            return false;
        }

        $name = imap_utf7_encode($name);
        $prefixedName = "{".$this->credentials->getHost()."}INBOX.".$name;
        $mailboxes = imap_list($this->conn, "{".$this->credentials->getHost()."}", "*");

        return in_array($prefixedName, $mailboxes);
    }

    /**
     * Read the current mailbox
     *
     * @return array
     */
    public function readMailbox(): array
    {
        $msg_cnt = @imap_num_msg($this->conn);

        $emails = [];
        for ($i = 1; $i <= $msg_cnt; $i++) {
            $header = imap_headerinfo($this->conn, $i);
            $emails[] = new Email(trim($header->Msgno), $header, imap_fetchstructure($this->conn, $i));
        }
        $this->messages = $emails;

        return $this->messages;
    }
}