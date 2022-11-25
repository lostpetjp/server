<?php

declare(strict_types=1);


class Batch2
{
  static public function dispatch(): void
  {
    require _DIR_ . "/lib/migrate/index.php";

    MigrateMedia::batch();
  }
}
