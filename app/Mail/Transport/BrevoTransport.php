<?php

namespace App\Mail\Transport;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailSender;
use Brevo\Client\Model\SendSmtpEmailTo;
use Brevo\Client\Model\SendSmtpEmailCc;
use Brevo\Client\Model\SendSmtpEmailBcc;
use Brevo\Client\Model\SendSmtpEmailReplyTo;
use Brevo\Client\Model\SendSmtpEmailAttachment;
use GuzzleHttp\Client;
use Illuminate\Mail\SentMessage;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Email;
use Illuminate\Support\Facades\Log;

class BrevoTransport implements TransportInterface
{
  /**
   * The Brevo API client instance.
   */
  protected TransactionalEmailsApi $api;

  /**
   * Create a new Brevo transport instance.
   *
   * @param  string  $apiKey
   * @return void
   */
  public function __construct(protected string $apiKey)
  {
    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->apiKey);
    $this->api = new TransactionalEmailsApi(new Client(), $config);
  }

  /**
   * {@inheritdoc}
   */
  public function send(\Symfony\Component\Mime\RawMessage $message, Envelope $envelope = null): ?SymfonySentMessage
  {
    $email = MessageConverter::toEmail($message);

    $sendSmtpEmail = new SendSmtpEmail();

    // 1. Set Sender (From)
    $from = $email->getFrom()[0] ?? null;
    if ($from) {
      $sender = new SendSmtpEmailSender([
        'name' => $from->getName() ?: $from->getAddress(), // Fix: Fallback ke email jika nama kosong
        'email' => $from->getAddress()
      ]);
      $sendSmtpEmail->setSender($sender);
    }

    // 2. Set Recipients (To) - INI PERBAIKAN UTAMA
    $toAddresses = [];
    foreach ($email->getTo() as $address) {
      $toAddresses[] = new SendSmtpEmailTo([
        'email' => $address->getAddress(),
        'name' => $address->getName() ?: $address->getAddress() // Fix: Fallback ke email
      ]);
    }
    $sendSmtpEmail->setTo($toAddresses);

    // 3. Set CC
    if ($email->getCc()) {
      $ccAddresses = [];
      foreach ($email->getCc() as $address) {
        $ccAddresses[] = new SendSmtpEmailCc([
          'email' => $address->getAddress(),
          'name' => $address->getName() ?: $address->getAddress() // Fix: Fallback
        ]);
      }
      $sendSmtpEmail->setCc($ccAddresses);
    }

    // 4. Set BCC
    if ($email->getBcc()) {
      $bccAddresses = [];
      foreach ($email->getBcc() as $address) {
        $bccAddresses[] = new SendSmtpEmailBcc([
          'email' => $address->getAddress(),
          'name' => $address->getName() ?: $address->getAddress() // Fix: Fallback
        ]);
      }
      $sendSmtpEmail->setBcc($bccAddresses);
    }

    // 5. Set Reply-To
    if ($email->getReplyTo()) {
      $replyTo = $email->getReplyTo()[0];
      $sendSmtpEmail->setReplyTo(new SendSmtpEmailReplyTo([
        'email' => $replyTo->getAddress(),
        'name' => $replyTo->getName() ?: $replyTo->getAddress() // Fix: Fallback
      ]));
    }

    // Set subject
    $sendSmtpEmail->setSubject($email->getSubject());

    // Set HTML and text content
    if ($email->getHtmlBody()) {
      $sendSmtpEmail->setHtmlContent($email->getHtmlBody());
    }

    if ($email->getTextBody()) {
      $sendSmtpEmail->setTextContent($email->getTextBody());
    }

    // Set attachments
    if ($email->getAttachments()) {
      $attachments = [];
      foreach ($email->getAttachments() as $attachment) {
        $attachments[] = new SendSmtpEmailAttachment([
          'content' => base64_encode($attachment->getBody()),
          'name' => $attachment->getFilename() ?? 'attachment'
        ]);
      }
      $sendSmtpEmail->setAttachment($attachments);
    }

    // Send email via Brevo API
    try {
      $this->api->sendTransacEmail($sendSmtpEmail);

      // Create a sent message envelope
      if (!$envelope) {
        $envelope = new Envelope(
          $email->getFrom()[0] ?? throw new \Exception('No sender address found'),
          array_merge($email->getTo(), $email->getCc(), $email->getBcc())
        );
      }

      return new SymfonySentMessage($message, $envelope);
    } catch (\Exception $e) {
      Log::error('Brevo email send error: ' . $e->getMessage());
      // Penting: Throw ulang errornya biar masuk ke failed_jobs Laravel
      throw $e;
    }
  }

  /**
   * Get the string representation of the transport.
   *
   * @return string
   */
  public function __toString(): string
  {
    return 'brevo';
  }
}
