<?php

declare(strict_types=1);

class Cases
{
  // 案件を終了する
  static public function end(int $id)
  {
    RDS::execute("UPDATE `case` SET `publish`=?, `ends_at`=? WHERE `id`=? LIMIT 1;", [
      0,
      $_SERVER["REQUEST_TIME"],
      $id,
    ]);

    Queue::create(1, $id, (30 * 86400));
  }

  // 案件を削除
  static public function delete(int $id)
  {
    RDS::execute("UPDATE `case` SET `status`=? WHERE `id`=? LIMIT 1;", [
      0,
      $id,
    ]);

    Queue::create(1, $id, (30 * 86400));
  }

  // 案件を凍結する
  static public function archive(int $id)
  {
    RDS::execute("UPDATE `case` SET `archive`=?, `email`=?, `password`=? WHERE `id`=? LIMIT 1;", [
      1,
      null,
      null,
      $id,
    ]);

    Queue::create(2, $id, (1 * 86400));
  }
}
