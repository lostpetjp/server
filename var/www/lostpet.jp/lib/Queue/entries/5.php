<?php

declare(strict_types=1);

/**
 * 掲載終了してから10分以上、更新がなければ、Discordに通知する
 */
class Queue5
{
  static public int $type = 5;

  static public function dispatch(int $id)
  {
    $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? LIMIT 1;", [
      $id,
    ]);

    if ($case_data && !$case_data["archive"]) {
      if (!($_SERVER["REQUEST_TIME"] > $case_data["updated_at"] + 600)) {
        return Queue::update(self::$type, $id, 600);
      }

      $message = null;

      if (!$case_data["status"]) {
        $message = ":x: 削除されました。";
      } elseif (!$case_data["publish"]) {
        $message = ":homes: 掲載終了しました。";
      }

      if ($message) {
        new Discord("case", [
          "content" => "{$message} <https://lostpet.jp/" . $case_data["id"] . ">",
        ]);
      }
    }

    Queue::delete(self::$type, $id);
  }
}
