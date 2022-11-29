<?php

declare(strict_types=1);

/**
 * 削除、または終了した案件が1ヶ月間、何も操作がなければ凍結を実行する。
 */
class Queue1
{
  static private int $id = 1;

  static public function dispatch(int $id): void
  {
    $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? LIMIT 1;", [
      $id,
    ]);

    if ($case_data) {
      $is_archived = $case_data["archive"];

      // 既に凍結されている場合、処理は必要ない
      if (!$is_archived) {
        $is_deleted = !$case_data["status"];
        $is_ended = !$case_data["publish"];

        // 削除されている場合
        // または、掲載終了している場合
        if ($is_deleted || $is_ended) {
          // 1ヶ月以上操作がなければ、凍結処理をする
          if ($_SERVER["REQUEST_TIME"] > ($case_data["updated_at"] + (30 * 86400))) {
            // 凍結処理
            Cases::archive($id);

            new Discord("queue", [
              "content" => "凍結しました。https://" . _DOMAIN_ . "/{$id}",
            ]);

            // まだ経過していなければ、処理を1日間、引き延ばす (queueを削除しない)
          } else {
            Queue::create(self::$id, $id, 86400);

            return;
          }
        }
      }
    }

    Queue::delete(self::$id, $id);
  }
}
