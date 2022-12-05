<?php

declare(strict_types=1);


class Batch2
{
  static public int $span = 30;
  static public array $hours = [];

  static public function dispatch(): void
  {
    require_once _DIR_ . "/lib/migrate/index.php";

    MigrateMedia::batch();

    new Discord("batch", [
      "content" => "`batch2`を実行しました。",
    ]);
  }
}
