<?php

declare(strict_types=1);

class Batch
{
  static public function run()
  {
    $rows = RDS::fetchAll("SELECT * FROM `batch`;");

    array_multisort(array_column($rows, 'updated_at'), SORT_ASC, $rows);

    foreach ($rows as $row) {
      $id = $row['id'];
      $updated_at = $row['updated_at'];
      $span = $row['span'];
      $hours = $row['hour'];
      if ($hours) $hours = json_decode($hours, true);

      if (
        $_SERVER['REQUEST_TIME'] > ($updated_at + $span)
        && (!$hours || in_array((int)date("H"), $hours, true))
      ) {
        require __DIR__ . "/entries/{$id}.php";
      }
    }
  }

  static public function update(int $id)
  {
    RDS::execute("UPDATE `batch` SET `updated_at`=? WHERE `id`=? LIMIT 1;", [
      $_SERVER['REQUEST_TIME'],
      $id,
    ]);
  }
}
