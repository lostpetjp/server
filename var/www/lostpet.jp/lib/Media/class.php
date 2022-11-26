<?php

declare(strict_types=1);

class Media
{
  static public function createS3Key(int $id, string $name)
  {
    $start1 = floor($id / 100000000) * 100000000;
    $start2 = floor($id / 1000000) * 1000000;
    $start3 = floor($id / 10000) * 10000;
    $start4 = floor($id / 100) * 100;

    return "upload/src/media/" . max(1, ($start1)) . "-" . ($start1 + 100000000 - 1) . "/" . max(1, ($start2)) . "-" . ($start2 + 1000000 - 1) . "/" . max(($start3), 1) . "-" . ($start3 + 10000 - 1) . "/" . max(($start4), 1) . "-" . ($start4 + 100 - 1) . "/" . $name;
  }
}
