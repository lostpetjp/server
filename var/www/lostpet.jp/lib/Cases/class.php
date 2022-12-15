<?php

declare(strict_types=1);

class Cases
{
  static public function getMediaIds(array $items): array
  {
    $items = array_map(fn (array $row) => [
      "body" => is_string($row["body"] ?? null) ? json_decode($row["body"], true) : (is_array($row["body"] ?? null) ? $row["body"] : null),
      "head" => is_string($row["head"] ?? null) ? json_decode($row["head"], true) : (is_array($row["head"] ?? null) ? $row["head"] : null),
    ] + $row, $items);

    $media_ids = [];

    for ($i = 0; count($items) > $i; $i++) {
      foreach ($items[$i]["body"]["photos"] ?? [] as $photo) $media_ids = [...$media_ids, ...$photo,];
      if (is_int($items[$i]["head"]["cover"] ?? null)) $media_ids[] = $items[$i]["head"]["cover"];
      foreach ($items[$i]["body"]["videos"] ?? [] as $video) $media_ids[] = $video;
      if (is_int($items[$i]["body"]["opengraph"] ?? null)) $media_ids[] = $items[$i]["body"]["opengraph"];
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
        $items[$i]["body"]["photos"] = array_filter(array_map(fn (array $entry) => [
          $media_map[$entry[0]] ?? null,
          $media_map[$entry[1]] ?? null,
        ], $items[$i]["body"]["photos"]), fn (array $entry) => is_string($entry[0]) && is_string($entry[1]));
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


  static public function createCard(array $item): array
  {
    $matter_id = $item["matter"];
    $matter = Matter::$data[$matter_id];
    $animal_id = $item["animal"];
    $animal = Animal::$data[$animal_id];
    $prefecture_id = $item["prefecture"];
    $prefecture = Prefecture::$data[$prefecture_id];

    $head = $item["head"];
    $name = $head["cover"] ?? null;
    $info = $name ? Media::parse($name) : null;
    if ($info) $name = $info["prefix"] . "-w600a43" . $info["suffix"];

    return [
      "attribute" => [
        "class" => "c26i",
        "href" => "/" . $item["id"],
        "role" => "listitem",
      ],
      "children" => [
        "attribute" => [
          "class" => "c26a",
        ],
        "children" => [
          [
            "attribute" => [
              "class" => "o26a1",
            ],
            "children" => [
              [
                "attribute" => [
                  "class" => "o26a1a",
                ],
                "children" => [
                  $head["title"],
                ],
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "o26a1d",
                ],
                "children" => (1 === $matter_id ? (isset($head["pet"]) ? $head["pet"] : "名無し") : (99 === $animal_id ? "その他" : $animal["title"])),
                "tagName" => "div",
              ],
              [
                "attribute" => [
                  "class" => "o26a1b l" . $matter_id,
                ],
                "children" => $matter["title"],
                "tagName" => "div",
              ],
              [
                "attribute" => [
                  "class" => "o26a1c",
                ],
                "children" => [
                  ...($info ? [
                    [
                      "attribute" => [
                        "srcset" => "/media/{$name}.avif",
                        "type" => "image/avif",
                      ],
                      "tagName" => "source",
                    ],
                    [
                      "attribute" => [
                        "srcset" => "/media/{$name}.webp",
                        "type" => "image/webp",
                      ],
                      "tagName" => "source",
                    ],
                  ] : []),
                  [
                    "attribute" => [
                      "class" => "c26g",
                      "decoding" => "async",
                      "height" => "450",
                      "loading" => "lazy",
                      "src" => $info ? "/media/{$name}" : "/noimage.svg",
                      "width" => "600",
                    ],
                    "tagName" => "img",
                  ],
                ],
                "tagName" => "picture",
              ],
            ],
            "tagName" => "header",
          ],
          [
            "attribute" => [
              "class" => "o26a2" . ($item["created_at"] > ($_SERVER["REQUEST_TIME"] - 172800) ? " o26a2n" : ""),
            ],
            "children" => [
              [
                "attribute" => [
                  // "class" => "o3a2a",
                ],
                "tagName" => "div",
                "children" => $prefecture["title"] . " " . $head["location"],
              ],
              [
                "attribute" => [
                  "class" => "o26a2b",
                  "datetime" => date(DATE_ISO8601, $item["starts_at"]),
                ],
                "tagName" => "time",
                "children" => date("Y/m/d", $item["starts_at"]),
              ],
            ],
            "tagName" => "section",
          ],
        ],
        "tagName" => "article",
      ],
      "tagName" => "a",
    ];
  }
}
