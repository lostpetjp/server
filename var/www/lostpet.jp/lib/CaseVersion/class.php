<?php

declare(strict_types=1);

class CaseVersion
{
  static public function get(int $matter_id, int $animal_id, int $prefecture_id, int $sort_id): int
  {
    $version = RDS::fetchColumn("SELECT `version` FROM `case-version` WHERE `matter`=? AND `animal`=? AND `prefecture`=? AND `sort`=? LIMIT 1;", [
      $matter_id,
      $animal_id,
      $prefecture_id,
      $sort_id,
    ]);

    return $version ? $version : 0;
  }

  /**
   * sort
   * 0: 発生順
   * 1: 新着順
   * *: 発生順を更新した場合、必ず新着順も更新される
   */
  static public function update(int $matter_id, int $animal_id, int $prefecture_id, int $sort_id): int
  {
    RDS::execute("INSERT INTO `case-version` (`matter`, `animal`, `prefecture`, `sort`, `version`) VALUES (?, ?, ?, ?, ?)" . (!$sort_id ? ",(?, ?, ?, ?, ?)" : "") . " ON DUPLICATE KEY UPDATE `version`=VALUES(`version`);", [
      $matter_id, $animal_id, $prefecture_id, $sort_id, $_SERVER["REQUEST_TIME"],
      ...(!$sort_id ? [
        $matter_id, $animal_id, $prefecture_id, 1, $_SERVER["REQUEST_TIME"],
      ] : [])
    ]);

    return $_SERVER["REQUEST_TIME"];
  }
}
