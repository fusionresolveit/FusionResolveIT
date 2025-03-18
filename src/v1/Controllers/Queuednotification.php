<?php

declare(strict_types=1);

namespace App\v1\Controllers;

use App\Traits\ShowAll;
use App\Traits\ShowItem;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

final class Queuednotification extends Common
{
  // Display
  use ShowItem;
  use ShowAll;

  protected $model = \App\Models\Queuednotification::class;

  protected function instanciateModel(): \App\Models\Queuednotification
  {
    return new \App\Models\Queuednotification();
  }

  /**
   * Run the scheduled to send mails
   */
  public static function scheduleSendmails(): bool
  {
    $crontask = \App\Models\Crontask::where('name', 'queuednotification')->first();
    if (is_null($crontask))
    {
      return false;
    }

    $crontaskexecution = new \App\v1\Controllers\Crontaskexecution();
    $executionId = $crontaskexecution->createExecution($crontask);

    $queuenotification = new self();
    $nbMails = $queuenotification->sendMails();

    $executionlog = new \App\Models\Crontaskexecutionlog();
    $executionlog->crontaskexecution_id = $executionId;
    $executionlog->volume = $nbMails;
    $executionlog->content = $nbMails . ' mail(s) sent';
    $executionlog->save();

    $crontaskexecution->endExecution($executionId);

    return true;
  }

  private function sendMails(): int
  {
    $mailer = new PHPMailer(true);

    $nbMailsSend = 0;
    $mails = \App\Models\Queuednotification::get();
    foreach ($mails as $mail)
    {
      // TODO send mail
      if (
          is_null($mail->sender) ||
          is_null($mail->sendername) ||
          is_null($mail->recipient) ||
          is_null($mail->recipientname) ||
          is_null($mail->name) ||
          is_null($mail->body_html)
      )
      {
        continue;
      }

      try
      {
        //Server settings
        // $mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mailer->isSMTP();                                            //Send using SMTP
        $mailer->Host       = '127.0.0.1';                     //Set the SMTP server to send through
        // $mailer->SMTPAuth   = true;                                   //Enable SMTP authentication
        // $mailer->Username   = 'user@example.com';                     //SMTP username
        // $mailer->Password   = 'secret';                               //SMTP password
        // $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mailer->Port = 2525;

        //Recipients
        $mailer->setFrom($mail->sender, $mail->sendername);
        $mailer->addAddress($mail->recipient, $mail->recipientname);
        if (!is_null($mail->replyto) && !is_null($mail->replytoname))
        {
          $mailer->addReplyTo($mail->replyto, $mail->replytoname);
        }

        //Attachments
        // $mailer->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mailer->isHTML(true);                                  //Set email format to HTML
        $mailer->Subject = $mail->name;
        $mailer->Body    = \App\v1\Controllers\Toolbox::convertHtmlToMarkdown($mail->body_html);
        $mailer->AltBody = $mail->body_html;

        // $mail->addCustomHeader('X-FRIT-instance', 'custom-value');
        // $mail->addCustomHeader('X-FRIT-type', 'App\\Models\\Ticket');
        // $mail->addCustomHeader('X-FRIT-id', '546');
        // "Auto-Submitted":"auto-generated","X-Auto-Response-Suppress":"OOF, DR, NDR, RN, NRN" => $mail->headers

        if (empty($mailer->Body))
        {
          // $mail->delete();
          // continue;
        }

        $mailer->send();

        $mail->messageid = $mailer->getLastMessageID();
        $mail->save();

        $mail->delete();

        $nbMailsSend++;
      }
      catch (Exception $e)
      {
        echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
      }
    }
    return $nbMailsSend;
  }
}
