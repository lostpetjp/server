<?php

declare(strict_types=1);

class CaseIndex
{
  static public function get(int $matter_id, int $animal_id, int $prefecture_id, int $sort_id, int $page_id, int $version, ?int $count = null)
  {
    $index = RDS::fetchColumn("SELECT `index` FROM `case-index` WHERE `matter`=? AND `animal`=? AND `prefecture`=? AND `sort`=? AND `page`=? AND `version`=? LIMIT 1;", [
      $matter_id,
      $animal_id,
      $prefecture_id,
      $sort_id,
      $page_id,
      $version,
    ]);

    $case_ids = $index ? json_decode($index, true) : [];

    if (!$case_ids) {
      if (null === $count) {
        $count = CaseCount::get($matter_id, $animal_id, $prefecture_id);
      }

      if ($count) {
        $order = 1 === $sort_id ? "modified_at" : "starts_at";

        $wheres = array_filter([
          $matter_id ? (2 === $matter_id ? "(`matter`=? OR `matter`=? OR `matter`=?)" : "`matter`=?") : null,
          $animal_id ? (99 === $animal_id ? "(`animal`=? OR `animal`=? OR `animal`=? OR `animal`=? OR `animal`=?)" : "`animal`=?") : null,
          $prefecture_id ? "`prefecture`=?" : null,
        ], fn (?string $sql) => $sql);

        $values = array_filter([
          ...($matter_id ? (2 === $matter_id ? [2, 3, 4] : [$matter_id,]) : []),
          ...($animal_id ? (99 === $animal_id ? [5, 6, 7, 8, 99,] : [$animal_id]) : []),
          $prefecture_id ? $prefecture_id : null,
        ], fn (?string $sql) => $sql);

        $rows = RDS::fetchAll("SELECT `id`, `{$order}` FROM `case` WHERE " . implode(" AND ", $wheres) . ($wheres ? " AND" : "") . " `status`=1 AND `publish`=1;", [
          ...$values,
        ]);

        array_multisort(array_column($rows, $order), SORT_DESC, array_column($rows, "id"), SORT_DESC, $rows);

        $values = [];
        $page = 0;

        while (true) {
          $index = array_column(array_slice($rows, ((++$page - 1) * 60), 60), "id");
          if ($page === $page_id) $case_ids = $index;
          if (!$index) break;

          $values = [
            ...$values,
            $matter_id,
            $animal_id,
            $prefecture_id,
            $sort_id,
            $page,
            $version,
            json_encode($index),
          ];
        }

        if ($values) {
          RDS::execute("INSERT INTO `case-index` (`matter`, `animal`, `prefecture`, `sort`, `page`, `version`, `index`) VALUES " . implode(",", array_fill(0, count($values) / 7, "(?, ?, ?, ?, ?, ?, ?)")) . " ON DUPLICATE KEY UPDATE `version`=VALUES(`version`), `index`=VALUES(`index`);", [
            ...$values,
          ]);
        }
      }
    }

    return $case_ids;
  }
}
