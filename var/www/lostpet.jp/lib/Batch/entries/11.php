<?php

declare(strict_types=1);

class Batch11
{
  static public int $span = 86400;
  static public array $hours = [6, 7,];

  static public function dispatch(): void
  {
    RDS::execute("DELETE FROM `queue` WHERE `status`=?;", [
      0,
    ]);

    RDS::execute("OPTIMIZE TABLE `queue`;");

    new Discord("batch", [
      "content" => "`batch11`を実行しました。",
    ]);
  }
}
