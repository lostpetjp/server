<?php

declare(strict_types=1);

/**
 * 案件数の一斉更新
 */
class Queue7
{
  static public int $type = 7;

  static public function dispatch(int $id)
  {
    CaseCount::updateAll();

    Queue::delete(self::$type, $id);
  }
}
