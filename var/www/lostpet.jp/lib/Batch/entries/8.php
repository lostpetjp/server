<?php

declare(strict_types=1);

class Batch8
{
  static public int $span = 60;
  static public array $hours = [];

  static public function dispatch(): void
  {
    Queue::batch();

    new Discord("batch", [
      "content" => "`batch8`を実行しました。",
    ]);
  }
}
