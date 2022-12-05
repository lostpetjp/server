<?php

declare(strict_types=1);

class Batch
{
  static public function dispatch()
  {
    $rows = RDS::fetchAll("SELECT * FROM `batch` WHERE `type`=?;", [
      1,
    ]);

    array_multisort(array_column($rows, 'updated_at'), SORT_ASC, $rows);

    foreach ($rows as $row) {
      $id = $row["id"];
      $updated_at = $row['updated_at'];

      require_once __DIR__ . "/entries/{$id}.php";
      $span = "Batch{$id}"::$span;
      $hours = "Batch{$id}"::$hours;

      if (
        0 === $updated_at
        || ($_SERVER['REQUEST_TIME'] > ($updated_at + $span)
          && (!$hours || in_array((int)date("H"), $hours, true))
        )
      ) {
        self::run($id);

        RDS::execute("UPDATE `batch` SET `updated_at`=? WHERE `type`=? AND `id`=? LIMIT 1;", [
          $_SERVER["REQUEST_TIME"],
          1,
          $id,
        ]);
      }
    }
  }

  static public function run(int $id)
  {
    require_once __DIR__ . "/entries/{$id}.php";
    "Batch{$id}"::dispatch();
  }
}
