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
        // Discordに通知
        new Discord("case", [
          "content" => ":rocket: 新規登録がありました。 <https://lostpet.jp/" . $case_data["id"] . ">",
        ]);

        // コミュニティに新着通知
        new Discord("notify-case", [
          "content" => ":blue_circle: 新規登録がありました。 <https://lostpet.jp/" . $case_data["id"] . "> (" . Matter::$data[$case_data["matter"]]["title"] . " / " . Animal::$data[$case_data["animal"]]["title"] . " / " . Prefecture::$data[$case_data["prefecture"]]["title"] . " / " . date("Y-m-d", $case_data["starts_at"]) . ")",
        ]);

        new Slack("notify-case", [
          "text" => ":blue_circle: 新規登録がありました。 <https://lostpet.jp/" . $case_data["id"] . "> (" . Matter::$data[$case_data["matter"]]["title"] . " / " . Animal::$data[$case_data["animal"]]["title"] . " / " . Prefecture::$data[$case_data["prefecture"]]["title"] . " / " . date("Y-m-d", $case_data["starts_at"]) . ")",
        ]);

        // メール通知 (予定)

      } else {
        return Queue::update(self::$type, $id, 600);
      }
    }

    Queue::delete(self::$type, $id);
  }
}
