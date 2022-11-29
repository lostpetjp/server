<?php

declare(strict_types=1);

class Batch5
{
  static public int $span = 86400;
  static public array $hours = [3, 4, 5, 6, 7,];

  static public function dispatch(): void
  {
    RDS::execute("DELETE FROM `contact` WHERE ?>`created_at`;", [
      strtotime("-1 month"),
    ]);

    RDS::execute("OPTIMIZE TABLE `contact`;");

    new Discord("batch", [
      "content" => "`batch5`を実行しました。",
    ]);
  }
}
