<?php

declare(strict_types=1);

class Batch4
{
  static public function dispatch(): void
  {
    RDS::execute("DELETE FROM `media-relation` WHERE `status`=?;", [
      0,
    ]);

    RDS::execute("OPTIMIZE TABLE `media-relation`;");
  }
}
