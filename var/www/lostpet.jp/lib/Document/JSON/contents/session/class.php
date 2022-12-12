<?php

declare(strict_types=1);

class JSONDocumentSession
{
  static public int $cache_time = 0;

  static public function create()
  {
    $rows = [];

    if (is_string($_COOKIE["PHPSESSID"] ?? null)) {
      Session::load(true);

      $rows = RDS::fetchAll("SELECT `type`, `content`, `updated_at` FROM `session-relation` WHERE `session`=? AND (`type`=? OR `type`=?) AND `status`=?;", [
        Me::$session,
        1,
        2,
        1,
      ]);
    }

    return [
      "id" => Me::$session,
      "relation" => $rows,
    ];
  }
}
