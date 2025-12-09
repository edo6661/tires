<?php

namespace App\Mail\Transport;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailSender;
use Brevo\Client\Model\SendSmtpEmailTo;
use Brevo\Client\Model\SendSmtpEmailAttachment;
use GuzzleHttp\Client;
use Illuminate\Mail\SentMessage;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Email;

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

    // Set sender
    $from = $email->getFrom()[0] ?? null;
    if ($from) {
      $sender = new SendSmtpEmailSender([
        'name' => $from->getName(),
        'email' => $from->getAddress()
      ]);
      $sendSmtpEmail->setSender($sender);
    }

    // Set recipients
    $toAddresses = [];
    foreach ($email->getTo() as $address) {
      $toAddresses[] = new SendSmtpEmailTo([
        'email' => $address->getAddress(),
        'name' => $address->getName()
      ]);
    }
    $sendSmtpEmail->setTo($toAddresses);

    // Set CC
    if ($email->getCc()) {
      $ccAddresses = [];
      foreach ($email->getCc() as $address) {
        $ccAddresses[] = [
          'email' => $address->getAddress(),
          'name' => $address->getName()
        ];
      }
      $sendSmtpEmail->setCc($ccAddresses);
    }

    // Set BCC
    if ($email->getBcc()) {
      $bccAddresses = [];
      foreach ($email->getBcc() as $address) {
        $bccAddresses[] = [
          'email' => $address->getAddress(),
          'name' => $address->getName()
        ];
      }
      $sendSmtpEmail->setBcc($bccAddresses);
    }

    // Set Reply-To
    if ($email->getReplyTo()) {
      $replyTo = $email->getReplyTo()[0];
      $sendSmtpEmail->setReplyTo([
        'email' => $replyTo->getAddress(),
        'name' => $replyTo->getName()
      ]);
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
          'name' => $attachment->getFilename() ?? $attachment->getName()
        ]);
      }
      $sendSmtpEmail->setAttachment($attachments);
    }

    // Send email via Brevo API
    try {
      $result = $this->api->sendTransacEmail($sendSmtpEmail);

      // Create a sent message with the message ID from Brevo
      // If no envelope provided, create one from the email addresses
      if (!$envelope) {
        $envelope = new Envelope(
          $email->getFrom()[0] ?? throw new \Exception('No sender address found'),
          $email->getTo()
        );
      }

      return new SymfonySentMessage($message, $envelope);
    } catch (\Exception $e) {
      \Illuminate\Support\Facades\Log::error('Brevo email send error: ' . $e->getMessage());
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
