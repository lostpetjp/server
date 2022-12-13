<?php

declare(strict_types=1);

class Comment
{
  static public function getMediaIds(array $items): array
  {
    $items = array_map(fn (array $row) => [
      "body" => is_string($row["body"] ?? null) ? json_decode($row["body"], true) : (is_array($row["body"] ?? null) ? $row["body"] : null),
      "head" => is_string($row["head"] ?? null) ? json_decode($row["head"], true) : (is_array($row["head"] ?? null) ? $row["head"] : null),
    ] + $row, $items);

    $media_ids = [];

    for ($i = 0; count($items) > $i; $i++) {
      foreach ($items[$i]["body"]["photos"] ?? [] as $photo) $media_ids[] = $photo;
      foreach ($items[$i]["body"]["videos"] ?? [] as $video) $media_ids[] = $video;
    }

    return [...array_unique($media_ids)];
  }

  static public function parse(array $items, ?array $media_map = null)
  {
    if (null === $media_map) {
      $media_ids = self::getMediaIds($items);

      $rows = $media_ids ? RDS::fetchAll("SELECT `id`, `name` FROM `media` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($media_ids)), "?")) . ") AND `status`=? LIMIT {$limit};", [
        ...$media_ids,
        1,
      ]) : [];

      $media_map = array_combine(array_column($rows, "id"), array_column($rows, "name"));
    }

    $items = array_map(fn (array $row) => [
      "body" => is_string($row["body"] ?? null) ? json_decode($row["body"], true) : (is_array($row["body"] ?? null) ? $row["body"] : null),
      "head" => is_string($row["head"] ?? null) ? json_decode($row["head"], true) : (is_array($row["head"] ?? null) ? $row["head"] : null),
    ] + $row, $items);

    for ($i = 0; count($items) > $i; $i++) {
      if (is_array($items[$i]["body"]["photos"] ?? null)) {
        $items[$i]["body"]["photos"] = array_filter(array_map(fn (int $entry) => $media_map[$entry] ?? null, $items[$i]["body"]["photos"]), fn (string | null $entry) => is_string($entry));
      }

      if (is_array($items[$i]["body"]["videos"] ?? null)) {
        $items[$i]["body"]["videos"] = array_filter(array_map(fn (int $entry) => $media_map[$entry] ?? null, $items[$i]["body"]["videos"]), fn (string | null $entry) => is_string($entry));
      }

      if (array_key_exists("body", $items[$i]) && null === $items[$i]["body"]) unset($items[$i]["body"]);
    }

    return $items;
  }
}
