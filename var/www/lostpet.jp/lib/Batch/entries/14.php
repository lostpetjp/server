<?php

declare(strict_types=1);

class Batch14
{
  static public int $span = 30;
  static public array $hours = [23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9,];

  static public function dispatch(): void
  {
    $max_id1 = RDS::fetchColumn("SELECT `id` FROM `comment` ORDER BY `id` DESC LIMIT 1;");

    $max_id2 = RDS::fetchColumn("SELECT `id` FROM `batch` WHERE `type`=? ORDER BY `id` DESC LIMIT 1;", [
      5,
    ]);
    if (!$max_id2) $max_id2 = 0;

    if ($max_id1 > $max_id2) {
      $values = [];

      for ($i = 1; 10000 > $i; $i++) {
        $id = $max_id2 + $i;

        if (RDS::fetch("SELECT `id` FROM `comment` WHERE `id`=? LIMIT 1;", [
          $id,
        ])) {
          $values = [...$values, 5, $id,];
        }

        if ($id >= $max_id1 || count($values) >= 1000) break;
      }

      if ($values) {
        RDS::execute("INSERT INTO `batch` (`type`, `id`) VALUES " . implode(",", array_fill(0, count($values) / 2, "(?, ?)")) . ";", [
          ...$values,
        ]);
      }
    }

    $entries = RDS::fetchAll("SELECT `id` FROM `batch` WHERE `type`=? AND ? > `updated_at` ORDER BY `updated_at` ASC LIMIT 50;", [
      5,
      $_SERVER["REQUEST_TIME"] - (7 * 86400),
    ]);

    $comment_data_set = $entries ? RDS::fetchAll("SELECT `id`, `archive`, `status`, `case`, `parent`, `updated_at` FROM `comment` WHERE `id` IN (" . implode(",", array_fill(0, count($entries), "?")) . ");", [
      ...array_column($entries, "id"),
    ]) : [];

    $update_comment_ids = [];
    $delete_comment_ids = [];
    $archive_comment_ids = [];

    $exists_case_ids = [];
    $exists_comment_ids = [];

    $delete_comment_ids = [...$delete_comment_ids, ...array_diff(array_column($entries, "id"), array_column($comment_data_set, "id")),];

    foreach ($comment_data_set as $comment_data) {
      $comment_id = $comment_data["id"];

      // 30日以上経過している行がチェックの対象
      if (($_SERVER["REQUEST_TIME"] - (30 * 86400)) > $comment_data["updated_at"]) {
        if (0 === $comment_data["archive"]) {
          $can_archive = false;

          // 削除されている場合 => ファイルを削除
          if (!$comment_data["status"]) {
            $can_archive = true;

            // 削除されていない場合 => 関連が存在しなければ => ファイルを削除
          } else {
            // 所属する案件がarchiveなら、コメントもarchive
            if (!$can_archive) {
              $case_id = $comment_data["case"];

              $exists = $exists_case_ids[$case_id] ?? null;

              if (null === $exists) {
                $exists_case_ids[$case_id] = $exists = self::existsCase($case_id);
              }

              if (!$exists) $can_archive = true;
            }

            // 所属するスレッドがarchiveなら、コメントもarchive
            if (!$can_archive) {
              $parent_id = $comment_data["parent"];

              if ($parent_id) {
                $exists = $exists_comment_ids[$parent_id] ?? null;

                if (null === $exists) {
                  $exists_comment_ids[$parent_id] = $exists = self::existsComment($parent_id);
                }

                if (!$exists) $can_archive = true;
              }
            }
          }

          // 削除フラグが立っていたら、凍結する
          if ($can_archive) {
            new Discord("queue", [
              "content" => "[シミュレート] `comment={$comment_id}`を凍結しました。",
            ]);

            // TODO
            // $archive_comment_ids[] = $comment_id;
          }

          $update_comment_ids[] = $comment_id;
        } else {
          $delete_comment_ids[] = $comment_id;
        }
      } else {
        $update_comment_ids[] = $comment_id;
      }
    }

    if ($update_comment_ids) {
      RDS::execute("UPDATE `batch` SET `updated_at`=? WHERE `type`=? AND `id` IN (" . implode(",", array_fill(0, count($update_comment_ids), "?")) . ");", [
        $_SERVER["REQUEST_TIME"],
        5,
        ...$update_comment_ids,
      ]);
    }

    if ($archive_comment_ids) {
      RDS::execute("UPDATE `batch` SET `archive`=?, `password`=?, `updated_at`=? WHERE `type`=? AND `id` IN (" . implode(",", array_fill(0, count($archive_comment_ids), "?")) . ");", [
        1,
        null,
        $_SERVER["REQUEST_TIME"],
        5,
        ...$archive_comment_ids,
      ]);
    }

    if ($delete_comment_ids) {
      RDS::execute("DELETE FROM `batch` WHERE `type`=? AND `id` IN (" . implode(",", array_fill(0, count($delete_comment_ids), "?")) . ");", [
        5,
        ...$delete_comment_ids,
      ]);

      RDS::execute("OPTIMIZE TABLE `batch`;");
    }

    new Discord("batch", [
      "content" => "`batch14`を実行しました。",
    ]);
  }

  static private function existsCase(int $case_id): bool
  {
    $case_data = RDS::fetch("SELECT `id`, `archive` FROM `case` WHERE `id`=?;", [
      $case_id,
    ]);

    return !!($case_data && !$case_data["archive"]);
  }

  static private function existsComment(int $comment_id): bool
  {
    $comment_data = RDS::fetch("SELECT `id`, `archive`, `parent` FROM `comment` WHERE `id`=?;", [
      $comment_id,
    ]);

    return !!($comment_data && !$comment_data["archive"]);
  }
}
