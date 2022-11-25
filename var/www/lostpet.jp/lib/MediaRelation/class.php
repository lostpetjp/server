<?php

declare(strict_types=1);

class MediaRelation
{
  static public function update(int $type, int $content, array $new_media_ids)
  {
    $old_media_ids = array_column(RDS::fetchAll("SELECT `media` FROM `media-relation` WHERE `type`=? AND `content`=? AND `status`=?;", [
      $type,
      $content,
      1,
    ]), "media");

    $values = [];

    $delete_media_ids = array_diff($old_media_ids, $new_media_ids);

    if ($delete_media_ids) {
      foreach ($delete_media_ids as $media) {
        $values = [...$values, ...[$type, $media, $content, 0, $_SERVER["REQUEST_TIME"], $_SERVER["REQUEST_TIME"],]];
      }
    }

    $create_media_ids = array_diff($new_media_ids, $old_media_ids);

    if ($create_media_ids) {
      foreach ($create_media_ids as $media) {
        $values = [...$values, ...[$type, $media, $content, 1, $_SERVER["REQUEST_TIME"], $_SERVER["REQUEST_TIME"],]];
      }
    }

    if ($values) {
      RDS::execute("INSERT INTO `media-relation` (`type`, `media`, `content`, `status`, `created_at`, `updated_at`) VALUES " . implode(",", array_fill(0, count($values) / 6, "(?, ?, ?, ?, ?, ?)")) . " ON DUPLICATE KEY UPDATE `updated_at`=VALUES(`updated_at`), `status`=VALUES(`status`);", [
        ...$values,
      ]);
    }
  }
}
