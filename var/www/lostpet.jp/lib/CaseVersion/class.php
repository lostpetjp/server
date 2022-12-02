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
  static public function update(int|array $matter_ids, int|array $animal_ids, int|array $prefecture_ids, int|array $sort_ids): int
  {
    $values = [];

    if (!is_array($matter_ids)) $matter_ids = [$matter_ids];
    if (!is_array($animal_ids)) $animal_ids = [$animal_ids];
    if (!is_array($prefecture_ids)) $prefecture_ids = [$prefecture_ids];
    if (!is_array($sort_ids)) $sort_ids = [$sort_ids];

    foreach ([0, ...$matter_ids] as $m) {
      foreach ([0, ...$animal_ids] as $a) {
        foreach ([0, ...$prefecture_ids] as $p) {
          foreach ($sort_ids as $s) {
            $values = [...$values, $m, $a, $p, $s, $_SERVER["REQUEST_TIME"],];
          }
        }
      }
    }

    RDS::execute("INSERT INTO `case-version` (`matter`, `animal`, `prefecture`, `sort`, `version`) VALUES " . implode(",", array_fill(0, count($values) / 5, "(?, ?, ?, ?, ?)")) . " ON DUPLICATE KEY UPDATE `version`=VALUES(`version`);", [
      ...$values,
    ]);

    return $_SERVER["REQUEST_TIME"];
  }
}
