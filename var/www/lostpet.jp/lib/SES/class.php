<?php

declare(strict_types=1);

class SES
{
  static public function send(?string $from = "",  string $to = "",  string $title = "",  string $body = "")
  {
    $body .= "\n\n【心当たりがない方へ】"
      . "\nこのメールが届いた心当たりがない場合、申し訳ありませんが破棄して下さい。"
      . "\nリンクをクリックしない限り、操作者は何もできません。"

      . "\n\n【操作者】"
      . "\nIPアドレス: " . _IP_

      . "\n\n【送信元】"
      . "\nhttps://" . $_SERVER["SERVER_NAME"] . "/"

      . "\n\n※このメールに返信しても届きません。";

    $try_count = 0;

    while (2 > ++$try_count) {
      try {
        $response = self::client()->sendEmail([
          'Destination' => [
            'ToAddresses' => [
              $to,
            ],
          ],
          'Message' => [
            'Body' => [
              'Text' => [
                'Charset' => 'UTF-8',
                'Data' => $body,
              ],
            ],
            'Subject' => [
              'Charset' => 'UTF-8',
              'Data' => $title,
            ],
          ],
          'Source' => $from ? $from : "no-reply@" . _DOMAIN_,
        ]);


        return isset($response["@metadata"]["statusCode"]) && $response["@metadata"]["statusCode"] === 200 ? true : Document::error(500);
      } catch (Aws\Exception\AwsException $e) {
        new Discord("error", [
          "content" => implode("\n", [
            "```\n" . json_encode([
              $from,
              $to,
              $title,
              $body,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```",
            $e->getAwsRequestId(),
            $e->getAwsErrorType(),
            $e->getAwsErrorCode(),
          ]) . "\n" . __CLASS__ . "::" . __FUNCTION__ . "()",
        ]);

        switch ($e->getAwsErrorCode()) {
          case "AccessDeniedException":
          case "IncompleteSignature":
          case "InvalidAction":
          case "InvalidClientTokenId":
          case "InvalidParameterCombination":
          case "InvalidParameterValue":
          case "InvalidQueryParameter":
          case "MalformedQueryString":
          case "MissingAction":
          case "MissingAuthenticationToken":
          case "MissingParameter":
          case "OptInRequired":
          case "RequestExpired":
          case "ThrottlingException":
          case "ValidationError":
            Document::error(500);
        }

        sleep(1);
      } catch (Exception $e) {
        new Discord("error", [
          "content" => implode("\n", [
            "```\n" . json_encode([
              $from,
              $to,
              $title,
              $body,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n```",
            $e->getMessage(),
            __CLASS__ . "::" . __FUNCTION__,
          ]),
        ]);

        Document::error(500);
      }
    }
  }

  static private function client(): Aws\Ses\SesClient
  {
    static $client = null;

    if ($client === null) {
      if (!class_exists("Aws\Sdk")) {
        require _DIR_ . "/lib/aws/vendor/autoload.php";
      }

      $client = new Aws\Ses\SesClient([
        "version" => "latest",
        "region" => "us-east-1",
      ]);
    }

    return $client;
  }
}
