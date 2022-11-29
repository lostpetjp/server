<?php

declare(strict_types=1);

class Batch6
{
  static public int $span = 3600;
  static public array $hours = [7, 9, 11, 13, 15, 17, 19, 21, 23, 3,];

  static public function dispatch(): void
  {
    CaseCount::updateAll();

    new Discord("batch", [
      "content" => "`batch6`を実行しました。",
    ]);
  }
}
