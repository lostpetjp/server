<?php

declare(strict_types=1);

class CaseVersion
{
  static public function get(int $matter_id, int $animal_id, int $prefecture_id, int $sort_id): int
  {
    $values = [];
    $wheres = [];

    // for matter
    if (2 === $matter_id) {
      $wheres[] = "(`matter`=? OR `matter`=? OR `matter`=?)";
      $values = [...$values, 2, 3, 4,];
    } else {
      $wheres[] = "`matter`=?";
      $values[] = $matter_id;
    }

    // for animal
    if (99 === $animal_id) {
      $wheres[] = "(`animal`=? OR `animal`=? OR `animal`=? OR `animal`=? OR `animal`=?)";
      $values = [...$values, 5, 6, 7, 8, 99,];
    } else {
      $wheres[] = "`animal`=?";
      $values[] = $animal_id;
    }

    // for prefecture
    $wheres[] = "`prefecture`=?";
    $values[] = $prefecture_id;

    // for sort
    $wheres[] = "`sort`=?";
    $values[] = $sort_id;

    $version = RDS::fetchColumn("SELECT MAX(`version`) FROM `case-version` WHERE " . implode(" AND ", $wheres) . " LIMIT 1;", [
      ...$values,
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
