<?php

declare(strict_types=1);

class Batch13
{
  static public int $span = 30;
  static public array $hours = [23, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9,];

  static public function dispatch(): void
  {
    $media_data_set = RDS::fetchAll("SELECT * FROM `media` WHERE `archive`=? AND ? > `updated_at` LIMIT 10", [
      1,
      $_SERVER["REQUEST_TIME"] - (7 * 86400),
    ]);

    $archive_media_ids = [];

    foreach ($media_data_set as $media_data) {
      $media_id = $media_data["id"];
      $tokens = explode(".", $media_data["name"]);
      $prefix = array_shift($tokens);

      foreach ([
        S3::listObjects(Config::$bucket, Media::createS3Key($media_id, $prefix)),
        ...(2 === $media_data["type"] ? S3::listObjects(Config::$bucket, "dist/video/media/{$prefix}") : []),
      ] as $result) {
        foreach ($result['Contents'] as $object) {
          // S3::deleteObject(Config::$bucket, $object["Key"]);

          new Discord("queue", [
            "content" => "[シミュレート] `" . $object["Key"] . "`を削除しました。",
          ]);
        }
      }

      // $archive_media_ids[] = $media_id;
    }

    if ($archive_media_ids) {
      RDS::execute("UPDATE `media` SET `archive`=?, `updated_at`=? WHERE `id` IN (" . implode(",", array_fill(0, count($archive_media_ids), "?")) . ");", [
        2,
        $_SERVER["REQUEST_TIME"],
        ...$archive_media_ids,
      ]);
    }
  }
}
