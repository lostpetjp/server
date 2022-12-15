<?php

declare(strict_types=1);

require __DIR__ . "/../../templates/case/index.php";

class HTMLDocumentCaseContent implements HTMLDocumentContentInterface
{
  static public string $template = HTMLDocumentCaseTemplate::class;
  static public int $id = 23;

  static public int $cache_time = 1;
  static public string $pathname = "/";
  static public string $search = "";

  static public array $css = [];
  static public array $js = [];
  static public array $schema = [];
  static public array $data = [];

  static public string $title = "";
  static public string $description = "";

  static public array $head = [
    [
      "attribute" => [
        "content" => "article",
        "property" => "og:type",
      ],
      "tagName" => "meta",
    ],
  ];

  static public function create(string $pathname): array
  {
    preg_match("/\A\/(case\/)?([0-9]+)/", $pathname, $matches);
    if (!$matches) Document::redirect("/search/", 86400); // 不正なURLの場合 (例外)

    $case_id = (int)$matches[2];

    self::$pathname = "/{$case_id}";

    Document::redirect(self::$pathname, 3600);

    $case_data = RDS::fetch("SELECT `id`, `status`, `publish`, `matter`, `animal`, `prefecture`, `created_at`, `updated_at`, `modified_at`, `starts_at`, `ends_at`, `expires_at`, `head`, `body`, `archive`, `email` IS NOT NULL as `email` FROM `case` WHERE `id`=? LIMIT 1;", [
      $case_id,
    ]);

    // for HTML
    if (1 === _REQUEST_) {
      if (!$case_data) Document::redirect("/search/?status=404", 600); // 存在しない記事の場合
      if (0 === $case_data["status"]) Document::redirect("/search/?status=404", 86400); // 削除された記事の場合

      // for JSON
    } else {
      if (!$case_data || 0 === $case_data["status"]) {
        return [
          "status" => false,
        ];
      }
    }

    // Etag::generate(_PATH_, max(filemtime(__FILE__), $case_data["updated_at"]));

    $matter_id = $case_data["matter"];
    $animal_id = $case_data["animal"];
    $animal_title = 99 === $animal_id ? null : Animal::$data[$animal_id]["title"];
    $prefecture_id = $case_data["prefecture"];
    $starts_at = $case_data["starts_at"];
    $expires_at = $case_data["expires_at"] ?? null;
    $expires_soon = $expires_at && (($_SERVER["REQUEST_TIME"] + (7 * 86400)) > $expires_at);
    $publish = $case_data["publish"];

    $cm_data_set = $publish ? RDS::fetchAll("SELECT `id`, `body`, `head`, `status`, `private`, `case`, `parent`, `created_at`, `updated_at`, `verified` FROM `comment` WHERE `case`=? AND `status`=?;", [
      $case_id,
      1,
    ]) : [];

    if (!$publish) {
      $case_data["body"] = json_decode($case_data["body"], true);
      if (is_array($case_data["body"]["photos"] ?? null)) unset($case_data["body"]["photos"]);
      if (is_array($case_data["body"]["videos"] ?? null)) unset($case_data["body"]["videos"]);
    }

    // for pickup
    $pk_ids = [];
    $pk_data = [
      [], // new arivals
      [], // random
    ];

    // new arivals
    $pk_ids1 = RDS::fetchColumn("SELECT `index` FROM `case-index` WHERE `matter`=? AND `animal`=? AND `prefecture`=? AND `sort`=? AND `page`=? LIMIT 1;", [
      1 === $case_data["matter"] ? 2 : 1,
      $case_data["animal"],
      0,
      1,
      1,
    ]);
    if ($pk_ids1 && is_string($pk_ids1)) $pk_ids1 = json_decode($pk_ids1, true);
    if (!$pk_ids1) $pk_ids1 = CaseIndex::get(1 === $case_data["matter"] ? 2 : 1, $case_data["animal"], 0, 1, 1, $_SERVER["REQUEST_TIME"], null);
    $pk_ids1 = array_slice($pk_ids1, 0, 24);

    // random
    $max_case_id = CaseCount::get(0, 0, 0);
    $pk_ids2 = [];

    while (96 > count($pk_ids2)) {
      $id = rand(1, $max_case_id);

      if ($id !== $case_id && !in_array($id, $pk_ids1, true)) {
        $pk_ids2[] = $id;
      }
    }

    $pk_ids = [...array_unique([...$pk_ids1, ...$pk_ids2,]),];

    $pk_data_set = $pk_ids ? RDS::fetchAll("SELECT `id`, `matter`, `animal`, `prefecture`, `created_at`, `modified_at`, `starts_at`, `expires_at`, `head` FROM `case` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($pk_ids)), "?")) . ") AND `publish`=? AND `status`=? LIMIT {$limit};", [
      ...$pk_ids,
      1,
      1,
    ]) : [];

    $media_ids = [...array_unique([
      ...Cases::getMediaIds($pk_data_set),
      ...Cases::getMediaIds([$case_data,]),
      ...Comment::getMediaIds($cm_data_set),
    ])];

    $rows = $media_ids ? RDS::fetchAll("SELECT `id`, `name` FROM `media` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($media_ids)), "?")) . ") AND `status`=? LIMIT {$limit};", [
      ...$media_ids,
      1,
    ]) : [];

    $media_map = array_combine(array_column($rows, "id"), array_column($rows, "name"));

    [$case_data,] = Cases::parse([$case_data,], $media_map);
    $pk_data_set = Cases::parse($pk_data_set, $media_map);

    $pk_data_map = array_combine(array_column($pk_data_set, "id"), $pk_data_set);

    foreach ($pk_ids1 as $id) {
      if ($pk_data_map[$id] ?? null) {
        $pk_data[0][] = $pk_data_map[$id];
      }
    }

    foreach ($pk_ids2 as $id) {
      if ($pk_data_map[$id] ?? null) $pk_data[1][] = $pk_data_map[$id];
      if (count($pk_data[1]) >= 24) break;
    }

    $cm_data_set = Comment::parse($cm_data_set, $media_map);

    $cm_has_video = false;

    $cm_map = array_combine(array_column($cm_data_set, "id"), array_fill(0, count($cm_data_set), true));

    $cm_data_set = [...array_filter($cm_data_set, fn (array $item) => !($item["parent"] && !($cm_map[$item["parent"]] ?? null))),];

    for ($i = 0; count($cm_data_set) > $i; $i++) {
      if ($cm_data_set[$i]["private"]) {
        unset($cm_data_set[$i]["body"]);
      } else {
        if ($cm_data_set[$i]["body"]["videos"] ?? null) $cm_has_video = true;
      }
    }

    array_multisort(array_map(fn ($cm_data) => (!$cm_data["parent"] ? $cm_data["updated_at"] : -1), $cm_data_set), SORT_DESC, array_column($cm_data_set, "id"), SORT_ASC, $cm_data_set);

    self::$data = [
      "data" => [
        "id" => $case_data["id"],
        "animal" => $case_data["animal"],
      ],
    ];

    $head = $case_data["head"];
    $pet = $head["pet"] ?? null;

    $description = date("Y年n月j日", $starts_at) . "(" . ["日", "月", "火", "水", "木", "金", "土",][date("w", $starts_at)] . ")、" . Prefecture::$data[$prefecture_id]["title"] . $head["location"] . "で";

    if (1 === $matter_id) {
      $description .= "迷子になった";

      if ($animal_title && $pet) {
        $description .= "{$animal_title}の{$pet}ちゃん";
      } elseif ($animal_title) {
        $description .= "{$animal_title}";
      } elseif ($pet) {
        $description .= " {$pet}ちゃん ";
      } else {
        $description .= "ペット";
      }

      $description .= "を探しています。保護、目撃、似た動物を飼っている方はご連絡下さい。";
    } elseif (2 === $matter_id) {
      if ($animal_title) {
        $description .= $animal_title;
      } else {
        $description .= "ペット";
      }

      $description .= "が保護されました。飼い主に心当たりがある方は情報提供をお願いします。";
    } elseif (3 === $matter_id) {
      if ($animal_title) {
        $description .= $animal_title;
      } else {
        $description .= "ペット";
      }

      $description .= "が目撃されました。飼い主に心当たりがある方は情報提供をお願いします。";
    } elseif (4 === $matter_id) {
      if ($animal_title) {
        $description .= $animal_title;
      } else {
        $description .= "ペット";
      }

      $description .= "のご遺体が発見されました。飼い主に心当たりがある方は情報提供をお願いします。";
    }

    self::$description = $description;

    // 掲載終了中
    if ($publish) self::$css[] = 42;
    if (!$publish || $expires_soon) self::$css[] = 32;
    if ((!$publish && ($case_data["head"]["cover"] ?? null)) || ($case_data["body"]["photos"] ?? null)) self::$css[] = 43;
    if ($cm_has_video || ($case_data["body"]["videos"] ?? null)) self::$css[] = 44;

    // for comment
    $has_email = $case_data["email"];
    if ($has_email) self::$css[] = 39;

    // for opengraph
    $opengraph = $case_data["body"]["opengraph"] ?? null;
    $info = $opengraph ? Media::parse($opengraph) : null;

    if ($info) {
      $size = 600;

      foreach ([1200, 900,] as $width) {
        if ($info["width"] >= $width) {
          $size = $width;
          break;
        }
      }

      self::$head = [
        ...self::$head,
        [
          "attribute" => [
            "content" => "/media/" . $info["prefix"] . "-w{$size}a21" . $info["suffix"],
            "property" => "og:image",
          ],
          "tagName" => "meta",
        ],
        [
          "attribute" => [
            "content" => "image/" . ("png" === $info["extension"] ? "png" : "jpeg"),
            "property" => "og:image:type",
          ],
          "tagName" => "meta",
        ],
        [
          "attribute" => [
            "content" => $size / 2,
            "property" => "og:image:height",
          ],
          "tagName" => "meta",
        ],
        [
          "attribute" => [
            "content" => $size,
            "property" => "og:image:width",
          ],
          "tagName" => "meta",
        ],
      ];
    }

    self::$title = (!$case_data["publish"] ? "[掲載終了] " : "") . $case_data["head"]["title"];

    self::$head = [
      ...self::$head,
      [
        "children" => self::$title . " - 迷子ペットのデータベース",
        "tagName" => "title",
      ],
    ];

    $matter_id = $case_data["matter"];
    $animal_id = $case_data["animal"];
    $prefecture_id = $case_data["prefecture"];

    // breadcrumb
    $breadcrumb_items = [
      [
        "title" => "ホーム",
        "pathname" => "/",
      ],
    ];

    $breadcrumb_location = [
      "matter" => 0,
      "animal" => 0,
      "prefecture" => 0,
      "sort" => 0,
      "page" => 1,
    ];

    $schema_items = [
      [
        "@type" => "ListItem",
        "position" => 1,
        "name" => "ホーム",
        "item" => "/",
      ],
      [
        "@type" => "ListItem",
        "position" => 2,
        "name" => "検索",
        "item" => "/search/",
      ],
    ];

    $schema_items[] = [
      "@type" => "ListItem",
      "position" => count($schema_items) + 1,
      "name" => ($t = Matter::$data[$matter_id]["title"]),
      "item" => Search::createUrl($breadcrumb_location = [
        "matter" => $matter_id,
      ] + $breadcrumb_location),
    ];

    $breadcrumb_items[] = [
      "title" => $t,
      "pathname" => Search::createUrl([
        "matter" => $matter_id,
        "animal" => 0,
        "prefecture" => 0,
        "sort" => 0,
        "page" => 1,
      ]),
    ];

    $schema_items[] = [
      "@type" => "ListItem",
      "position" => count($schema_items) + 1,
      "name" => ($t = Animal::$data[$animal_id]["title"]),
      "item" => Search::createUrl($breadcrumb_location = [
        "animal" => $animal_id,
      ] + $breadcrumb_location),
    ];

    $breadcrumb_items[] = [
      "title" => $t,
      "pathname" => Search::createUrl([
        "matter" => 0,
        "animal" => $animal_id,
        "prefecture" => 0,
        "sort" => 0,
        "page" => 1,
      ]),
    ];

    $schema_items[] = [
      "@type" => "ListItem",
      "position" => count($schema_items) + 1,
      "name" => ($t = Prefecture::$data[$prefecture_id]["title"]),
      "item" => Search::createUrl($breadcrumb_location = [
        "prefecture" => $prefecture_id,
      ] + $breadcrumb_location),
    ];

    $breadcrumb_items[] = [
      "title" => $t,
      "pathname" => Search::createUrl([
        "matter" => 0,
        "animal" => 0,
        "prefecture" => $prefecture_id,
        "sort" => 0,
        "page" => 1,
      ]),
    ];

    $schema_items[] = [
      "@type" => "ListItem",
      "position" => count($schema_items) + 1,
      "name" => ($t = "No: " . $case_id),
      "item" => "/{$case_id}",
    ];

    $breadcrumb_items[] = [
      "title" => $t,
      "pathname" => "/{$case_id}",
      "here" => true,
    ];

    self::$schema[] = [
      "@context" => "https://schema.org",
      "@type" => "BreadcrumbList",
      "itemListElement" => array_map(fn (array $item) => [
        "item" => "https://" . _DOMAIN_ . $item["item"],
      ] + $item, $schema_items),
    ];

    return [
      "comment" => $cm_data_set,
      "data" => $case_data,
      "breadcrumb" => $breadcrumb_items,
      "pickup" => $pk_data,
      "status" => true,
    ];
  }
}
