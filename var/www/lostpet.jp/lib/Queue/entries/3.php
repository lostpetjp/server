<?php

declare(strict_types=1);

/**
 * 削除、または終了した案件が1ヶ月間、何も操作がなければ凍結を実行する。
 */
class Queue3
{
  static private int $id = 3;

  static public function dispatch(int $id): void
  {

    Queue::delete(self::$id, $id);
  }
}
