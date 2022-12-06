<?php

declare(strict_types=1);

/**
 * 掲載期限による終了を処理する
 */
class Queue6
{
  static public int $type = 6;

  static public function dispatch(int $id)
  {
    $case_data = RDS::fetch("SELECT `id`, `expires_at` FROM `case` WHERE `id`=? AND `archive`=? AND `status`=? AND `publish`=? AND `expires_at`>? LIMIT 1;", [
      $id,
      0,
      1,
      1,
      0,
    ]);

    if ($case_data) {
      if (!($_SERVER["REQUEST_TIME"] > $case_data["expires_at"])) {
        return Queue::update(self::$type, $id, 3600);
      }

      RDS::execute("UPDATE `case` SET `publish`=?, `ends_at`=?, `updated_at`=? WHERE `id`=? LIMIT 1;", [
        0,
        $_SERVER["REQUEST_TIME"],
        $_SERVER["REQUEST_TIME"],
        $id,
      ]);

      CaseCount::updateAll(true);

      new Discord("case", [
        "content" => ":hourglass: 掲載期限により終了しました。 <https://lostpet.jp/{$id}>",
      ]);
    }

    Queue::delete(self::$type, $id);
  }
}
