<?php

declare(strict_types=1);

/**
 * メディアが投稿されたら、即Discordに通知する
 */
class Queue9
{
  static private int $type = 9;

  static public function dispatch(int $id)
  {
    $media_data = RDS::fetch("SELECT `name` FROM `media` WHERE `id`=? AND `status`=? LIMIT 1;", [
      $id,
      1,
    ]);

    if ($media_data) {
      new Discord("media", [
        "content" => "メディアが投稿されました。 <https://lostpet.jp/media/" . $media_data["name"] . ">",
      ]);
    }

    Queue::delete(self::$type, $id);
  }
}
