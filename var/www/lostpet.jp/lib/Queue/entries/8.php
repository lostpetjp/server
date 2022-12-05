<?php

declare(strict_types=1);

/**
 * コメントが投稿されてから10分以上、更新がなければ、Discordに通知する
 */
class Queue8
{
  static private int $type = 8;

  static public function dispatch(int $id)
  {
    $comment_data = RDS::fetch("SELECT * FROM `comment` WHERE `id`=? LIMIT 1;", [
      $id,
    ]);

    if ($comment_data && $comment_data["status"]) {
      $comment_data["head"] = json_decode($comment_data["head"], true);
      $comment_data["body"] = json_decode($comment_data["body"], true);

      if ($_SERVER["REQUEST_TIME"] > $comment_data["updated_at"] + 600) {
        new Discord("comment", [
          "content" => "コメントが投稿されました。 <https://lostpet.jp/" . $comment_data["case"] . ">"
            . "\n"
            . (!$comment_data["parent"] ? "\n> **" . $comment_data["head"]["title"] . "**" : "")
            . "\n> " . implode("\n> ", explode("\n", mb_substr($comment_data["body"]["description"], 0, 1500)))
            . "\n",
        ]);
      } else {
        return Queue::update(self::$type, $id, 600);
      }
    }

    Queue::delete(self::$type, $id);
  }
}
