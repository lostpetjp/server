<?php

declare(strict_types=1);

class Batch6
{
  static public int $span = 7200;
  static public array $hours = [4, 10, 18,];

  static public function dispatch(): void
  {
    CaseCount::updateAll();

    new Discord("batch", [
      "content" => "`batch6`を実行しました。",
    ]);
  }
}
