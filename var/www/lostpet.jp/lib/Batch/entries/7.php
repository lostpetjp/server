<?php

declare(strict_types=1);

class Batch7
{
  static public int $span = 86400;
  static public array $hours = [2, 3, 4,];

  static public function dispatch(): void
  {
    RDS::execute("DELETE FROM `case-version` WHERE ?>`version`;", [
      strtotime("-1 month"),
    ]);

    RDS::execute("OPTIMIZE TABLE `case-version`;");

    new Discord("batch", [
      "content" => "`batch7`を実行しました。",
    ]);
  }
}
