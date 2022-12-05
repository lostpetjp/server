<?php

declare(strict_types=1);


class Batch3
{
  static public int $span = 30;
  static public array $hours = [];

  static public function dispatch(): void
  {
    require_once _DIR_ . "/lib/migrate/index.php";

    MigrateComment::batch();

    new Discord("batch", [
      "content" => "`batch3`を実行しました。",
    ]);
  }
}
