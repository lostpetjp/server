<?php

declare(strict_types=1);

class JSONDocumentSession
{
  static public int $cache_time = 0;

  static public function create()
  {
    Session::load(true);

    return [
      "id" => Me::$session,
    ];
  }
}
