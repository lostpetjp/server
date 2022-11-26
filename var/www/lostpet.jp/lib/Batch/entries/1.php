<?php

declare(strict_types=1);


class Batch1
{
  static public function dispatch(): void
  {
    require_once _DIR_ . "/lib/migrate/index.php";

    MigrateCase::batch();
  }
}
