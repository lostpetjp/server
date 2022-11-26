<?php

declare(strict_types=1);


class Batch3
{
  static public function dispatch(): void
  {
    require_once _DIR_ . "/lib/migrate/index.php";

    MigrateComment::batch();
  }
}
