<?php

declare(strict_types=1);

class Batch
{
  static public array $entries = [
    1 => [  // 案件のクローン
      "span" => 300,
      "hours" => [],
    ],
    2 => [  // メディアファイルのクローン
      "span" => 0,
      "hours" => [],
    ],
    3 => [  // コメントのクローン
      "span" => 0,
      "hours" => [],
    ],
    4 => [  // `media-relation`のclean up
      "span" => 43200,
      "hours" => [4, 5,],
    ],
  ];

  static public function dispatch()
  {
    $rows = RDS::fetchAll("SELECT * FROM `batch` WHERE `type`=?;", [
      1,
    ]);

    array_multisort(array_column($rows, 'updated_at'), SORT_ASC, $rows);

    foreach (self::$entries as $id => $entry) {
      foreach ($rows as $row) {
        if ($row["id"] === $id) {
          $updated_at = $row['updated_at'];
          $span = $entry["span"];
          $hours = $entry["hours"];

          if (
            $_SERVER['REQUEST_TIME'] > ($updated_at + $span)
            && (!$hours || in_array((int)date("H"), $hours, true))
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
    }
  }

  static public function run(int $id)
  {
    require_once __DIR__ . "/entries/{$id}.php";
    "Batch{$id}"::dispatch();
  }
}
