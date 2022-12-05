<?php

declare(strict_types=1);

/**
 * 掲載期限がある案件を探してキューに登録する
 */
class Batch9
{
  static public int $span = 43200;
  static public array $hours = [4,];

  static public function dispatch(): void
  {
    $rows = RDS::fetchAll("SELECT `id`, `expires_at` FROM `case` WHERE `expires_at`>? AND `status`=? AND `publish`=? AND `archive`=?;", [
      0,
      1,
      1,
      0,
    ]);

    $map = array_combine(array_column($rows, "id"), array_column($rows, "expires_at"));

    $values = [];

    foreach ($map as $case_id => $expires_at) {
      $values = [...$values, 6, $case_id, $expires_at, 1,];
    }

    RDS::execute("INSERT INTO `queue` (`type`, `id`, `starts_at`, `status`) VALUES " . implode(",", array_fill(0, count($values) / 4, "(?, ?, ?, ?)")) . " ON DUPLICATE KEY UPDATE `starts_at`=VALUES(`starts_at`), `status`=VALUES(`status`);", [
      ...$values,
    ]);

    new Discord("batch", [
      "content" => "`batch9`を実行しました。",
    ]);
  }
}
