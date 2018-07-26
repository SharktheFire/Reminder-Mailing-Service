<?php

require_once __DIR__ . '/vendor/autoload.php';

use ReminderMailing\Mailer;

const REQUIREMENTS = <<<TXT
    Bitte gebe folgende Daten ein:
    --------------------------------
    Absender -> sender=<Absender>
    Empfänger -> recipient=<Empfänger>
    Betreff -> subject=<Betreff>
    Nachricht -> message=<Nachricht> \n
TXT;

if (count($argv) < 1) {
    echo REQUIREMENTS;
}

$mailer = new Mailer();
$mailer->sendMail($argv);


