<?php

declare(strict_types=1);

/**
 * 登録してから10分以上、更新がなければ、Discordに通知する
 */
class Queue4
{
  static private int $type = 4;

  static public function dispatch(int $id)
  {
    $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? LIMIT 1;", [
      $id,
    ]);

    if ($case_data && $case_data["status"] && $case_data["publish"] && !$case_data["archive"]) {
      if ($_SERVER["REQUEST_TIME"] > $case_data["updated_at"] + 600) {
        new Discord("case", [
          "content" => "新規登録がありました。 <https://lostpet.jp/" . $case_data["id"] . ">",
        ]);
      } else {
        return Queue::update(self::$type, $id, 600);
      }
    }

    Queue::delete(self::$type, $id);
  }
}
