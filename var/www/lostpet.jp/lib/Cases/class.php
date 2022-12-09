<?php

declare(strict_types=1);

class Cases
{
  static public function parse(array $items)
  {
    $items = array_map(fn (array $row) => [
      "body" => is_string($row["body"] ?? null) ? json_decode($row["body"], true) : null,
      "head" => is_string($row["head"] ?? null) ? json_decode($row["head"], true) : null,
    ] + $row, $items);

    $media_ids = [];

    for ($i = 0; count($items) > $i; $i++) {
      foreach ($items[$i]["head"]["photos"] ?? [] as $photo) $media_ids = [...$media_ids, ...$photo,];
      if (is_int($items[$i]["head"]["cover"] ?? null)) $media_ids[] = $items[$i]["head"]["cover"];
      foreach ($items[$i]["body"]["videos"] ?? [] as $video) $media_ids[] = $video;
      if (is_int($items[$i]["body"]["opengraph"] ?? null)) $media_ids[] = $items[$i]["body"]["opengraph"];
    }

    $media_ids = [...array_unique($media_ids)];

    $rows = RDS::fetchAll("SELECT `id`, `name` FROM `media` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($media_ids)), "?")) . ") AND `status`=? LIMIT {$limit};", [
      ...$media_ids,
      1,
    ]);

    $media_map = array_combine(array_column($rows, "id"), array_column($rows, "name"));

    for ($i = 0; count($items) > $i; $i++) {
      if (is_array($items[$i]["head"]["photos"] ?? null)) {
        $items[$i]["head"]["photos"] = array_filter(array_map(fn (array $entry) => [
          $media_map[$entry[0]] ?? null,
          $media_map[$entry[1]] ?? null,
        ], $items[$i]["head"]["photos"]), fn (array $entry) => is_string($entry[0]) && is_string($entry[1]));
      }

      if (is_int($items[$i]["head"]["cover"] ?? null)) {
        $items[$i]["head"]["cover"] = $media_map[$items[$i]["head"]["cover"]] ?? null;
        if (!$items[$i]["head"]["cover"]) unset($items[$i]["head"]["cover"]);
      }

      if (is_array($items[$i]["body"]["videos"] ?? null)) {
        $items[$i]["body"]["videos"] = array_filter(array_map(fn (int $entry) => $media_map[$entry] ?? null, $items[$i]["body"]["videos"]), fn (string | null $entry) => is_string($entry));
      }

      if (is_int($items[$i]["body"]["opengraph"] ?? null)) {
        $items[$i]["body"]["opengraph"] = $media_map[$items[$i]["body"]["opengraph"]] ?? null;
        if (!$items[$i]["body"]["opengraph"]) unset($items[$i]["body"]["opengraph"]);
      }

      if (array_key_exists("body", $items[$i]) && null === $items[$i]["body"]) unset($items[$i]["body"]);
      if (array_key_exists("ends_at", $items[$i]) && null === $items[$i]["ends_at"]) unset($items[$i]["ends_at"]);
      if (array_key_exists("expires_at", $items[$i]) && null === $items[$i]["expires_at"]) unset($items[$i]["expires_at"]);
    }

    return $items;
  }
}
