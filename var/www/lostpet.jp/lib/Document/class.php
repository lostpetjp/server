<?php

declare(strict_types=1);

class Document
{
  static public null|HTMLDocument|JSONDocument $client = null;

  static public function create(HTMLDocument|JSONDocument $client)
  {
    (self::$client = $client)->create();
  }

  static public function error(int $status): void
  {
    http_response_code($status);

    exit;
  }

  static public function echo(array|string $body): void
  {
    self::$client->{__FUNCTION__}($body);

    exit;
  }
}
