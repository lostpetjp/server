<?php

declare(strict_types=1);

class Batch15
{
  static public int $span = 43200;
  static public array $hours = [3, 4, 5, 15, 16, 17,];

  static public function dispatch(): void
  {
    // 有効期限切れ
    RDS::execute("UPDATE `session` SET `status`=?, `updated_at`=? WHERE ?>`expires_at`;", [
      0,
      $_SERVER["REQUEST_TIME"],
      $_SERVER["REQUEST_TIME"],
    ]);

    // 関係を削除
    $session_ids = array_column(RDS::fetchAll("SELECT `id` FROM `session` WHERE `status`=? AND ?>`updated_at`;", [
      0,
      $_SERVER["REQUEST_TIME"] - (86400 * 1),
    ]), "id");

    if ($session_ids) {
      RDS::execute("DELETE FROM `session-relation` WHERE `session` IN (" . implode(",", array_fill(0, count($session_ids), "?")) . ");", [
        ...$session_ids,
      ]);

      RDS::execute("OPTIMIZE TABLE `session-relation`;");

      RDS::execute("DELETE FROM `session` WHERE `id` IN (" . implode(",", array_fill(0, count($session_ids), "?")) . ");", [
        ...$session_ids,
      ]);

      RDS::execute("OPTIMIZE TABLE `session`;");
    }
  }
}
