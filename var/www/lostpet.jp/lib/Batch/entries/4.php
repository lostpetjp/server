<?php

declare(strict_types=1);

class Batch4
{
  static public int $span = 86400;
  static public array $hours = [2, 3, 4, 5,];

  static public function dispatch(): void
  {
    RDS::execute("DELETE FROM `media-relation` WHERE `status`=?;", [
      0,
    ]);

    RDS::execute("OPTIMIZE TABLE `media-relation`;");

    new Discord("batch", [
      "content" => "`batch4`を実行しました。",
    ]);
  }
}
