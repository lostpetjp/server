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


      if ($_SERVER["REQUEST_TIME"] > $comment_data["updated_at"] + 300) {
        $case_id = $comment_data["case"];

        $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? AND `status`=? AND `publish`=? AND `archive`=? LIMIT 1;", [
          $case_id,
          1,
          1,
          0,
        ]);

        if ($case_data) {
          $case_body = json_decode($case_data["body"], true);

          // Discordへの通知
          new Discord("comment", [
            "content" => ":speech_balloon: コメントが投稿されました。 <https://lostpet.jp/" . $comment_data["case"] . ">"
              . "\n"
              . (!$comment_data["parent"] ? "\n> **" . $comment_data["head"]["title"] . "**" : "")
              . "\n> " . implode("\n> ", explode("\n", mb_substr($comment_data["body"]["description"], 0, 1500)))
              . "\n　​　",
          ]);

          // コミュニティに新着通知
          new Discord("notify-comment", [
            "content" => ":speech_balloon: コメントが投稿されました。 <https://lostpet.jp/" . $comment_data["case"] . ">"
              . (!$comment_data["private"] ? "\n"
                . (!$comment_data["parent"] ? "\n> **" . $comment_data["head"]["title"] . "**" : "")
                . "\n> " . implode("\n> ", explode("\n", mb_substr($comment_data["body"]["description"], 0, 1500)))
                . "\n　​　" : ""),
          ]);

          new Slack("notify-comment", [
            "text" => ":speech_balloon: コメントが投稿されました。 <https://lostpet.jp/" . $comment_data["case"] . ">"
              . (!$comment_data["private"] ? "\n"
                . (!$comment_data["parent"] ? "\n> **" . $comment_data["head"]["title"] . "**" : "")
                . "\n> " . implode("\n> ", explode("\n", mb_substr($comment_data["body"]["description"], 0, 1500)))
                . "\n　​　" : ""),
          ]);

          // メール通知
          // 1. 案件のオーナーへ
          // 2. 
          $email_encode = $case_data["email"];

          if ($email_encode) {
            $email_decode = Encode::decode("/email/salt.txt", $email_encode);

            // 管理に新着通知
            new Discord("queue", [
              "content" => "[シミュレート] 新着コメントをオーナーにメールアドレスで通知しました。 <https://lostpet.jp/{$case_id}>",
            ]);

            $comment_title = $comment_data["head"]["title"] ?? null;

            $index = 0;

            $title = "[迷子ペットのデータベース] 新しいコメントがありました";
            $body = "新着のコメントがありました。"
              . "\n全部は表示されてないので、ページでコメント全文を確認して下さい。"
              . "\n"
              . "\n--------------------------------------------------"
              . ($comment_title ? "\n[{$comment_title}]" : null)
              . "\n" . trim(mb_substr($comment_data["body"]["description"], 0, 1000)) . "..."
              . "\n--------------------------------------------------"
              . "\n"
              . "\nhttps://lostpet.jp/{$id}";

            // SES::send(to: $email_decode, title: $title, body: $body);  // TODO
            SES::send(to: "info@lostpet.jp", title: $title, body: $body);
          }
        }
      } else {
        return Queue::update(self::$type, $id, 60);
      }
    }

    Queue::delete(self::$type, $id);
  }
}
