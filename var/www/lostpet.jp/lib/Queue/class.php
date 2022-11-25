<?php

declare(strict_types=1);

class Queue
{
  static public function batch(): void
  {
    $rows = RDS::fetchAll("SELECT * FROM `queue` WHERE `status`=? AND ? > `starts_at`;", [
      1,
      $_SERVER["REQUEST_TIME"],
    ]);

    $values = [];

    foreach ($rows as $row) {
      $type = $row["type"];
      $id = $row["id"];

      require_once __DIR__ . "/entries/{$type}.php";

      "Queue{$type}"::dispatch($id);

      $values = [...$values, $type, $id,];
    }

    RDS::execute("UPDATE `queue` SET `status`=? WHERE " . implode(" OR ", array_fill(0, count($values), "(`type`=? AND `id`=?)")) . ";", [
      0,
      ...$values,
    ]);
  }

  static public function create(int $type, int $id, int $after_sec): void
  {
    RDS::execute("INSERT INTO `queue` (`type`, `id`, `starts_at`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `starts_at`=VALUES(`starts_at`);", [
      $type,
      $id,
      $_SERVER["REQUEST_TIME"] + $after_sec,
    ]);
  }

  static public function delete(int $type, int $id): void
  {
    RDS::execute("UPDATE `queue` SET `status`=? WHERE `type`=? AND `id`=? LIMIT 1;", [
      0,
      $type,
      $id,
    ]);
  }
}
