<?php

declare(strict_types=1);

/**
 * Slackの各チャンネルにwebhookでメッセージを送信する。
 */
class Slack
{
  public function __construct(
    public string $channel,
    public array $message
  ) {
  }

  public function __destruct()
  {
    $this->send();
  }

  private function send(): void
  {
    $webhook_url = Secret::get('/slack/webhook.json')[$this->channel] ?? null;

    if ($webhook_url) {
      $ch = curl_init($webhook_url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->message));
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      curl_exec($ch);
      curl_close($ch);
    }
  }
}
