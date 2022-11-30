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

  static public function redirect(string $pathname, int $cache_time = 600): void
  {
    if (1 === _REQUEST_) {
      if (_PATH_ !== $pathname) {
        http_response_code(301);
        header("cache-control: max-age={$cache_time},public,immutable");
        header("location: https://" . _SERVER_ . $pathname);
        exit;
      }
    }
  }
}
