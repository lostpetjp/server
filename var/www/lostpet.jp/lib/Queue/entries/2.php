<?php

/**
 * 削除、または終了した案件が1ヶ月間、何も操作がなければ凍結を実行する。
 */
class Queue2
{
  static private int $id = 2;

  static public function dispatch(int $id): void
  {
    $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? LIMIT 1;", [
      $id,
    ]);

    // 関連のログを全て削除する
    // 削除し切れない場合は、次回に回す

    // Queue::delete(self::$id, $id);
  }
}
