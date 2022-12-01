<?php

declare(strict_types=1);


class Batch1
{
  static public int $span = 30;
  static public array $hours = [];

  static public function dispatch(): void
  {

    require_once _DIR_ . "/lib/migrate/index.php";

    MigrateCase::batch();

    new Discord("batch", [
      "content" => "`batch1`を実行しました。",
    ]);
  }
}
