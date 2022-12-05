<?php

declare(strict_types=1);

/**
 * 
 */
class Queue3
{
  static private int $id = 3;

  static public function dispatch(int $id): void
  {

    Queue::delete(self::$id, $id);
  }
}
