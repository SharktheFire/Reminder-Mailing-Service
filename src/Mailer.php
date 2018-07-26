<?php

namespace ReminderMailing;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mailer;
    private $sender;
    private $recipient;
    private $subject;
    private $message;
    private $userPassword;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    public function sendMail(array $arguments)
    {
        try {
            $this->userPassword = $this->askPromptForPassword();
            $this->sortArguments($arguments);
            $this->setServerSettings();
            $this->addRecipients();
            $this->addContent();

            $this->mailer->send();
            echo "Message has been sent." . PHP_EOL;
        } catch (ArgumentNotFoundException $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: " . $this->mailer->ErrorInfo . PHP_EOL;
        }
    }

    private function setServerSettings()
    {
         $this->mailer->SMTPDebug = 1;                                 // Enable verbose debug output
        $this->mailer->isSMTP();                                      // Set mailer to use SMTP
        $this->mailer->Host = 'smtp.live.com;smtp.live.com';  // Specify main and backup SMTP servers
        $this->mailer->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mailer->Username = $this->sender;                 // SMTP username
        $this->mailer->Password = $this->userPassword;                           // SMTP password
        $this->mailer->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->mailer->Port = 25;                                    // TCP port to connect to
    }

    private function addContent()
    {
        $this->mailer->isHTML(true);                                  // Set email format to HTML
        $this->mailer->Subject = $this->subject;

        // 'This is the HTML message body <b>in bold!</b>'
        $this->mailer->Body = $this->message;

        // 'This is the body in plain text for non-HTML mail clients'
        $this->mailer->AltBody = $this->message;
    }

    private function addRecipients()
    {
        //Recipients
        $this->mailer->setFrom($this->sender, 'Sender');
        $this->mailer->addAddress($this->recipient, 'Empfänger');     // Add a recipient
        // $this->mailer->addAddress('ellen@example.com');               // Name is optional
        // $this->mailer->addReplyTo('info@example.com', 'Information');
        // $this->mailer->addCC('cc@example.com');
        // $this->mailer->addBCC('bcc@example.com');
    }

    private function sortArguments(array $arguments)
    {
        foreach ($arguments as $argument) {
            if (strpos($argument, 'sender=') !== false) {
                $this->sender = str_replace('sender=', '', $argument);
            }
            if (strpos($argument, 'recipient=') !== false) {
                $this->recipient = str_replace('recipient=', '', $argument);
            }
            if (strpos($argument, 'subject=') !== false) {
                $this->subject = str_replace('subject=', '', $argument);
            }
            if (strpos($argument, 'message=') !== false) {
                $this->message = str_replace('message=', '', $argument);
            }
        }

        if (!$this->sender || !$this->recipient || !$this->subject || !$this->message) {
            throw new ArgumentNotFoundException(REQUIREMENTS);
        }
    }

    private function askPromptForPassword()
    {
            echo 'Enter Password: ';
            echo "\033[30;40m";
            $input = rtrim(fgets(STDIN), "\n");
            echo "\033[0m";
            if ($input !== "") {
                return $input;
            }
            throw new ArgumentNotFoundException("Please set your password!" . PHP_EOL);
    }

    // //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
}
