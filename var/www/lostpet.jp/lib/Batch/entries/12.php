<?php

declare(strict_types=1);

class Batch12
{
  static public int $span = 30;
  static public array $hours = [23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,];

  static public function dispatch(): void
  {
    $max_id1 = RDS::fetchColumn("SELECT `id` FROM `media` ORDER BY `id` DESC LIMIT 1;");

    $max_id2 = RDS::fetchColumn("SELECT `id` FROM `batch` WHERE `type`=? ORDER BY `id` DESC LIMIT 1;", [
      4,
    ]);
    if (!$max_id2) $max_id2 = 0;

    if ($max_id1 > $max_id2) {
      $values = [];

      for ($i = 1; 10000 > $i; $i++) {
        $id = $max_id2 + $i;

        if (RDS::fetch("SELECT `id` FROM `media` WHERE `id`=? LIMIT 1;", [
          $id,
        ])) {
          $values = [...$values, 4, $id,];
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
      4,
      $_SERVER["REQUEST_TIME"] - (7 * 86400),
    ]);

    $media_data_set = $entries ? RDS::fetchAll("SELECT `id`, `archive`, `status`, `updated_at` FROM `media` WHERE `id` IN (" . implode(",", array_fill(0, count($entries), "?")) . ");", [
      ...array_column($entries, "id"),
    ]) : [];

    $update_media_ids = [];
    $delete_media_ids = [];
    $archive_media_ids = [];

    $exists_case_ids = [];
    $exists_comment_ids = [];
    $live_case_ids = [];

    $delete_media_ids = [...$delete_media_ids, ...array_diff(array_column($entries, "id"), array_column($media_data_set, "id")),];

    foreach ($media_data_set as $media_data) {
      $media_id = $media_data["id"];

      // 30日以上経過している行がチェックの対象
      if (($_SERVER["REQUEST_TIME"] - (30 * 86400)) > $media_data["updated_at"]) {
        if (0 === $media_data["archive"]) {
          $can_archive = false;

          // 削除されている場合 => ファイルを削除
          if (!$media_data["status"]) {
            $can_archive = true;

            // 削除されていない場合 => 関連が存在しなければ => ファイルを削除
          } else {
            $can_archive = true;

            $rel_data_set = RDS::fetchAll("SELECT `type`, `status`, `content` FROM `media-relation` WHERE `media`=?;", [
              $media_id,
            ]);

            if ($rel_data_set) {
              array_multisort(array_column($rel_data_set, "type"), SORT_ASC, $rel_data_set);

              foreach ($rel_data_set as $rel_data) {
                if ($rel_data["status"]) {
                  $content_id = $rel_data["content"];

                  switch ($rel_data["type"]) {
                    case 1:
                      $exists = $exists_case_ids[$content_id] ?? null;

                      if (null === $exists) {
                        $exists_case_ids[$content_id] = $exists = self::existsCase($content_id);
                      }

                      if ($exists) $can_archive = false;
                      break;
                    case 2:
                      $exists = $exists_comment_ids[$content_id] ?? null;

                      if (null === $exists) {
                        $exists_comment_ids[$content_id] = $exists = self::existsComment($content_id);
                      }

                      if ($exists) $can_archive = false;
                      break;

                    case 3:
                      $exists = $live_case_ids[$content_id] ?? null;

                      if (null === $exists) {
                        $live_case_ids[$content_id] = $exists = self::isLiveCase($content_id);
                      }

                      if ($exists) $can_archive = false;
                      break;
                  }
                }

                if (!$can_archive) break;
              }
            }
          }

          // 削除フラグが立っていたら、凍結する
          if ($can_archive) {
            new Discord("queue", [
              "content" => "[シミュレート] `media={$media_id}`を凍結しました。",
            ]);

            // TODO
            // $archive_media_ids[] = $media_id;
          }

          $update_media_ids[] = $media_id;
        } else {
          new Discord("queue", [
            "content" => "[シミュレート] `media={$media_id}`のチェックを終了しました。",
          ]);

          // $delete_media_ids[] = $media_id;
        }
      } else {
        $update_media_ids[] = $media_id;
      }
    }

    if ($update_media_ids) {
      RDS::execute("UPDATE `batch` SET `updated_at`=? WHERE `type`=? AND `id` IN (" . implode(",", array_fill(0, count($update_media_ids), "?")) . ");", [
        $_SERVER["REQUEST_TIME"],
        4,
        ...$update_media_ids,
      ]);
    }

    if ($archive_media_ids) {
      RDS::execute("UPDATE `batch` SET `archive`=?, `updated_at`=? WHERE `type`=? AND `id` IN (" . implode(",", array_fill(0, count($archive_media_ids), "?")) . ");", [
        1,
        $_SERVER["REQUEST_TIME"],
        4,
        ...$archive_media_ids,
      ]);
    }

    if ($delete_media_ids) {
      RDS::execute("DELETE FROM `batch` WHERE `type`=? AND `id` IN (" . implode(",", array_fill(0, count($delete_media_ids), "?")) . ");", [
        4,
        ...$delete_media_ids,
      ]);

      RDS::execute("OPTIMIZE TABLE `batch`;");
    }

    new Discord("batch", [
      "content" => "`batch12`を実行しました。",
    ]);
  }

  static private function existsCase(int $case_id): bool
  {
    $case_data = RDS::fetch("SELECT `archive` FROM `case` WHERE `id`=?;", [
      $case_id,
    ]);

    return !!($case_data && !$case_data["archive"]);
  }

  static private function isLiveCase(int $case_id): bool
  {
    $case_data = RDS::fetch("SELECT `status` FROM `case` WHERE `id`=?;", [
      $case_id,
    ]);

    return !!($case_data && $case_data["status"]);
  }

  static private function existsComment(int $comment_id): bool
  {
    $comment_data = RDS::fetch("SELECT `archive`, `parent` FROM `comment` WHERE `id`=?;", [
      $comment_id,
    ]);

    return !!($comment_data && !$comment_data["archive"]);
  }
}
