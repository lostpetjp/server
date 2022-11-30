<?php

declare(strict_types=1);

class CaseCount
{
  static public function get(int $matter_id, int $animal_id, int $prefecture_id): int
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

    $count = RDS::fetchColumn("SELECT SUM(`count`) FROM `case-count` WHERE " . implode(" AND ", $wheres) . " LIMIT 1;", [
      ...$values,
    ]);

    return $count ? (int)$count : 0;
  }

  static public function getAll()
  {
  }

  /**
   * 案件数の正確な同期
   * 負荷がかかる処理なので時間を置くこと
   */
  static public function updateAll(): void
  {
    $matter_ids = array_column(Matter::$data, "id");
    $matter_ids = [0, ...$matter_ids,];

    $animal_ids = array_column(Animal::$data, "id");
    $animal_ids = [0, ...$animal_ids,];

    $prefecture_ids = array_column(Prefecture::$data, "id");
    $prefecture_ids = [0, ...$prefecture_ids,];

    $exists_keys = [];
    $update_data_set = [];
    $request_data_set = [];

    foreach ($matter_ids as $matter_id) {
      foreach ($animal_ids as $animal_id) {
        foreach ($prefecture_ids as $prefecture_id) {
          $request_data_set[] = [$matter_id, $animal_id, $prefecture_id,];
        }
      }
    }

    foreach ($request_data_set as $request_data) {
      [$matter_id, $animal_id, $prefecture_id,] = $request_data;
      $key = implode(":", $request_data);

      if (!in_array($key, $exists_keys, true)) {
        $exists_keys[] = $key;

        $values = [];
        $wheres = [];

        foreach (["matter", "animal", "prefecture",] as $name) {
          $id = ${"{$name}_id"};

          if ($id) {
            $values[] = $id;
            $wheres[] = "`{$name}`=?";
          }
        }

        $wheres = [...$wheres, "`publish`=1", "`status`=1",];

        $count = RDS::fetchColumn("SELECT COUNT(*) FROM `case` WHERE " . implode(" AND ", $wheres) . ";", [
          ...$values,
        ]);

        $update_data_set = [...$update_data_set, $matter_id, $animal_id, $prefecture_id, $count,];
      }
    }

    RDS::execute("INSERT INTO `case-count` (`matter`, `animal`, `prefecture`, `count`) VALUES " . implode(",", array_fill(0, (count($update_data_set) / 4), "(?, ?, ?, ?)")) . " ON DUPLICATE KEY UPDATE `count`=VALUES(`count`);", [
      ...$update_data_set,
    ]);
  }

  /**
   * 案件数の即時更新
   * 負荷がかからないが、状況によってズレが生じる危険あり
   */
  static public function add(int $matter_id, int $animal_id, int $prefecture_id): void
  {
    self::update($matter_id,  $animal_id,  $prefecture_id, 1);
  }

  static public function remove(int $matter_id, int $animal_id, int $prefecture_id): void
  {
    self::update($matter_id,  $animal_id,  $prefecture_id, 2);
  }

  static private function update(int $matter_id, int $animal_id, int $prefecture_id, int $type): void
  {
    RDS::execute("UPDATE `case-count` SET `count`=(`count` " . (2 === $type ? "-" : "+") . " 1) WHERE `matter`=? OR `matter`=? OR `animal`=? OR `animal`=? OR `prefecture`=? OR `prefecture`=?;", [
      $matter_id, 0, $animal_id, 0, $prefecture_id, 0,
    ]);
  }
}
