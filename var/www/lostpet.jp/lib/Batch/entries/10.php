<?php

declare(strict_types=1);

/**
 * まだ凍結されていない、削除された案件を`archive=1`に変更する
 */
class Batch10
{
  static public int $span = 43200;
  static public array $hours = [3,];

  static public function dispatch(): void
  {
    RDS::execute("UPDATE `case` SET `archive`=?, `email`=?, `password`=?, `updated_at`=? WHERE (`status`=? OR `publish`=?) AND `archive`=? AND ?>`updated_at`;", [
      1,
      null,
      null,
      $_SERVER["REQUEST_TIME"],
      0,
      0,
      0,
      $_SERVER["REQUEST_TIME"] - (30 * 86400),
    ]);

    new Discord("batch", [
      "content" => "`batch10`を実行しました。",
    ]);
  }
}
