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
     * Return all mailboxes (folders) on the server. This function removes the
     * mailbox prefixes like {server}INBOX.<name here>
     *
     * @return array
     */
    public function getPrettyMailboxes(): array
    {
        $mailboxes = $this->getMailboxes();
        $result = [];

        if (is_array($mailboxes) && count($mailboxes) > 0) {
            $prefixedMailbox = "INBOX.";
            foreach ($mailboxes as $mailbox) {
                // Remove the server prefix
                $mailbox = str_replace('{'.$this->credentials->getHost().'}', '', $mailbox);

                // Remove the INBOX. prefix (if present)
                $mailbox = str_replace($prefixedMailbox, '', $mailbox);

                $result[] = $mailbox;
            }
        }

        return $result;
    }

    /**
     * Check if a mailbox exists.
     *
     * @param string $name
     *
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
     * Subscribe to a mailbox.
     *
     * @param string $name
     *
     * @return bool
     */
    public function subscribeToMailbox($name = ''): bool
    {
        if (empty($name)) {
            return false;
        }

        $name = imap_utf7_encode($name);

        return imap_subscribe($this->conn, "{".$this->credentials->getHost()."}INBOX.".$name);
    }

    /**
     * Create a mailbox (folder)
     *
     * @param string $name
     *
     * @return bool
     */
    public function createMailbox($name = ''): bool
    {
        if (empty($name)) {
            return false;
        }

        $name = imap_utf7_encode($name);

        return imap_createmailbox($this->conn, "{".$this->credentials->getHost()."}INBOX.".$name);
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

    /**
     * Rename a mailbox (folder)
     *
     * @param string $from
     *
     * @param string $to
     *
     * @return bool
     */
    public function renameMailbox($from = '', $to = ''): bool
    {
        if (empty($from) || empty($to)) {
            return false;
        }

        $from = imap_utf7_encode($from);
        $to = imap_utf7_encode($to);

        return imap_renamemailbox($this->conn, "{".$this->credentials->getHost()."}INBOX.".$from, "{".$this->credentials->getHost()."}INBOX.".$to);
    }

    /**
     * Delete a mailbox (folder)
     *
     * @param string $name
     *
     * @return bool
     */
    public function deleteMailbox($name = ''): bool
    {
        if (empty($name)) {
            return false;
        }

        $name = imap_utf7_encode($name);

        return imap_deletemailbox($this->conn, "{".$this->credentials->getHost()."}INBOX.".$name);
    }

    /**
     * @param string $mailbox
     *
     * @return array
     */
    public function getMessages($mailbox = ''): array
    {
        if (! empty($mailbox)) {
            $this->setMailbox($mailbox);
        }

        $result = [];

        $info = imap_check($this->conn);

        $items = imap_fetch_overview($this->conn, "1:{$info->Nmsgs}", 0);
        foreach ($items as $message) {
            $result[] = new Email($message);
        }

        return $result;
    }

    /**
     * @param string $uid
     *
     * @param string $mailbox
     *
     * @return mixed
     */
    public function getMessage($uid = '', $mailbox = '')
    {
        if (empty($mailbox)) {
            $mailbox = $this->getMailbox();
        }
        
        $emails = $this->getMessages($mailbox);
        $mail = null;

        foreach ($emails as $email) {
            if ($email->uid == $uid) {
                $mail = $email;
                break;
            }
        }

        if (is_object($mail) === true) {
            $mail->body = quoted_printable_decode(imap_fetchbody($this->conn, $uid, FT_UID));
        }

        return $mail;
    }

    /**
     * Filter message sent to $to
     *
     * @param string $to
     *
     * @param string $mailbox
     *
     * @return array
     */
    public function filterTo($to = '', $mailbox = ''): array
    {
        if (! empty($mailbox)) {
            $this->setMailbox($mailbox);
        }

        $messages = imap_search($this->conn, 'TO "'.$to.'"', SE_UID);
        $filtered = [];

        if (is_array($messages) && count($messages) > 0) {
            foreach ($messages as $uid) {
                $mail = $this->getMessage($uid);
                if (is_object($mail)) {
                    $filtered[] = $mail;
                }
            }
        }

        return $filtered;
    }

    /**
     * Filter unread message sent to $to
     *
     * @param string $to
     *
     * @return array
     */
    public function filterUnReadMessagesTo($to = '', $mailbox = '')
    {
        $messages = $this->filterTo($to, $mailbox);
        $filtered = [];

        if (is_array($messages) && count($messages) > 0) {
            foreach ($messages as $email) {
                if ($email->seen == false) {
                    $filtered[] = $email;
                }
            }
        }

        return $filtered;
    }

    /**
     * @param string $uid
     *
     * @param string $mailbox
     *
     * @return bool
     */
    public function markMessageAsRead($uid = '', $mailbox = ''): bool
    {
        if (! empty($mailbox)) {
            $this->setMailbox($mailbox);
        }

        return imap_setflag_full($this->conn, $uid, "\\Seen", ST_UID);
    }

    /**
     * @param string $uid
     *
     * @param string $mailbox
     *
     * @return bool
     */
    public function markMessageAsUnRead($uid = '', $mailbox = ''): bool
    {
        if (! empty($mailbox)) {
            $this->setMailbox($mailbox);
        }

        return imap_clearflag_full($this->conn, $uid, "\\Seen", ST_UID);
    }

    /**
     * Move a given message at $index to a given mailbox.
     *
     * @param int $uid
     *
     * @param string $mailbox
     *
     * @return bool
     */
    public function moveMessageFromCurrentMailBox($uid = -1, $to = ''): bool
    {
        if (empty($to)) {
            return false;
        }

        $emails = $this->getMessages($this->getMailbox());
        $mail = null;

        foreach ($emails as $email) {
            if ($email->uid == $uid) {
                $mail = $email;
                break;
            }
        }

        if ($mail) {
            $to = imap_utf7_encode($to);
            imap_mail_move($this->conn, $uid, "INBOX.".$to, CP_UID);

            return imap_expunge($this->conn);
        }

        return false;
    }

    /**
     * Delete a given email message. The param $uid
     * is bases of the uid of the email.
     *
     * @param int $uid
     *
     * @param string $mailbox
     *
     * @return bool
     */
    public function deleteMessage($uid = -1, $mailbox = ''): bool
    {
        if ($uid == -1) {
            return false;
        }

        if (! empty($mailbox)) {
            $this->setMailbox($mailbox);
        }

        $result = imap_delete($this->conn, $uid, FT_UID);
        if ($result) {
            // Real delete emails marked as deleted
            return imap_expunge($this->conn);
        }

        return $result;
    }
}