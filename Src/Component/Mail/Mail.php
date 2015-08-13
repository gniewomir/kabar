<?php
/**
 * Simple wrapper/abstarction layer for wp_mail function
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Component
 */
namespace kabar\Component\Mail;

/**
 * Mailer class
 */
class Mail extends \kabar\Module\Module\Module
{

    const HEADER_REPLY_TO = 'Reply-To:';

    /**
     * Recipents of this email
     * @var array
     */
    public $recipents = array();

    /**
     * Subject of this email
     * @var string
     */
    public $subject = '';

    /**
     * Message
     * @var string
     */
    public $message = '';

    /**
     * Headers
     * @var array
     */
    public $headers = array();

    /**
     * Reply to recipents
     * @var array
     */
    public $replyTo = array();

    /**
     * Set message subject
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $subject = esc_html($subject);
        if (empty($subject)) {
            return false;
        }

        $this->subject = $subject;
        return true;
    }

    /**
     * Set message content
     * @param string $message
     */
    public function setMessage($message)
    {
        $message = esc_html($message);
        if (empty($message)) {
            return false;
        }

        $this->message = $message;
        return true;
    }

    /**
     * Add email recipents
     * @param string $email
     */
    public function addRecipient($email)
    {
        $this->filterEmail($email);
        $this->recipents[] = $email;
        return true;
    }

    /**
     * Add Reply-To header
     * @param string $email
     */
    public function addReplyTo($email, $name = '')
    {
        $this->headers[self::HEADER_REPLY_TO] = $this->filterName($name).' <'.$this->filterEmail($email).'>';
    }

    /**
     * Send email
     * @return boolean
     */
    public function send()
    {
        if (empty($this->recipents) && empty($this->subject) && empty($this->message)) {
            trigger_error('Recipents, subject or message not provided. Sending email aborted.', E_USER_WARNING);
            return false;
        }

        if (!empty($this->headers)) {
            foreach ($this->headers as $header => $value) {
                $this->headers[$header] = $this->filterOther($value);
            }
        }

        if (!wp_mail($this->recipents, $this->subject, $this->message, $this->headers)) {
            trigger_error('Ecountered error. Email not sent.', E_USER_WARNING);
            return false;
        }

        return true;
    }

    /**
     * Sanitize other
     * @param string $email
     * @return string
     */
    public function filterEmail($email)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
                      '"'  => '',
                      ','  => '',
                      '<'  => '',
                      '>'  => '',
        );

        return strtr($email, $rule);
    }

    /**
     * Sanitize other
     * @param string $name
     * @return string
     */
    public function filterName($name)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
                      '"'  => "'",
                      '<'  => '[',
                      '>'  => ']',
        );

        return trim(strtr($name, $rule));
    }

    /**
     * Sanitize other data
     * @param  string $data
     * @return string
     */
    public function filterOther($data)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
        );

        return strtr($data, $rule);
    }
}
