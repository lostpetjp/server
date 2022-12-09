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

    $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? LIMIT 1;", [
      $case_id,
    ]);
    if (!$case_data) Document::redirect("/search/?status=404", 600); // 存在しない記事の場合
    if (0 === $case_data["status"]) Document::redirect("/search/?status=404", 86400); // 削除された記事の場合

    // Etag::generate(_PATH_, max(filemtime(__FILE__), $case_data["updated_at"]));


    [$case_data,] = Cases::parse([$case_data,]);

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
      "data" => $case_data,
      "breadcrumb" => $breadcrumb_items,
    ];
  }
}
