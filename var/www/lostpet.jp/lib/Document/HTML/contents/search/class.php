<?php

declare(strict_types=1);

require __DIR__ . "/../../templates/search/index.php";

class HTMLDocumentSearchContent implements HTMLDocumentContentInterface
{
  static public string $template = HTMLDocumentSearchTemplate::class;
  static public int $id = 15;

  static public int $cache_time = 1;
  static public string $pathname = "/";
  static public string $search = "";

  static public array $css = [];
  static public array $js = [];

  static public string $title = "";
  static public string $description = "";

  static public array $head = [];

  static public array $schema = [];
  static public array $data = [];

  static public function create(string $pathname): array
  {
    $object = Search::parseUrl($pathname);
    $matter_id = $object["matter"];
    $animal_id = $object["animal"];
    $prefecture_id = $object["prefecture"];
    $sort_id = $object["sort"];
    $page_id = $object["page"];

    Document::redirect(Search::createUrl($object), 86400);  // normalize url

    $count = null;

    while (null === $count || $prefecture_id || $matter_id || $animal_id) {
      $count = CaseCount::get($matter_id, $animal_id, $prefecture_id);

      if ($count) {
        break;
      } else {
        if ($prefecture_id) {
          $prefecture_id = $object["prefecture"] = 0;
        } elseif ($matter_id) {
          $matter_id = $object["matter"] = 0;
        } elseif ($animal_id) {
          $animal_id = $object["animal"] = 0;
        } else {
          break;
        }
      }
    }

    Document::redirect(Search::createUrl($object)); // Loosen the search criteria if the count is zero.

    $total_pages = ceil($count / 60);

    if ($page_id > $total_pages) {
      $diff = $page_id - $total_pages;
      $page_id = $object["page"] = $total_pages;

      Document::redirect(Search::createUrl($object), $diff > 1 ? 10800 : 600);  // min(current page, total page)
    }

    $version = CaseVersion::get($matter_id, $animal_id, $prefecture_id, $sort_id);

    if ($version) {
      Etag::generate(_PATH_, max(filemtime(__FILE__), $version));
    } else {
      $version = CaseVersion::update($matter_id, $animal_id, $prefecture_id, $sort_id);
    }

    self::$pathname = Search::createUrl($object);

    Document::redirect(self::$pathname, 3600);

    // create title
    $p = "";

    if (0 === $prefecture_id) {
      $p = "?????????";
    } else {
      $_ = Prefecture::$data[$prefecture_id]["title"];
      $p = (1 !== $prefecture_id ? mb_substr($_, 0, -1) : $_) . "???";
    }

    $m = "";

    if (2 === $matter_id) {
      $m = "????????????????????????";
    } else {
      $m = "??????????????????";
    }

    $a = "";

    if (0 === $animal_id || 99 === $animal_id) {
      $a = "?????????";
    } else {
      $a = Animal::$data[$animal_id]["title"];
    }

    self::$title = "{$p}{$m}{$a}(" . number_format($count) . "???)";
    self::$description = "{$p}{$m}{$a}???{$count}????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????";

    self::$head = [
      ...self::$head,
      [
        "attribute" => [
          "href" => "https://" . _SERVER_ . Search::createUrl([
            "page" => 1,
            "sort" => 0,  // ????????????????????????
          ] + $object),
          "rel" => "canonical",
        ],
        "tagName" => "link",
      ],
      [
        "children" => self::$title . " - ????????????????????????????????????",
        "tagName" => "title",
      ],
    ];

    $breadcrumb_items = [
      [
        "title" => "?????????",
        "pathname" => "/",
      ],
      [
        "title" => "??????",
        "pathname" => Search::createUrl([
          "matter" => 0,
          "animal" => 0,
          "prefecture" => 0,
          "sort" => $sort_id,
          "page" => 1,
        ]),
      ],
    ];

    $breadcrumb_location = [
      "matter" => 0,
      "animal" => 0,
      "prefecture" => 0,
      "sort" => $sort_id,
      "page" => 1,
    ];

    $schema_items = [
      [
        "@type" => "ListItem",
        "position" => 1,
        "name" => "?????????",
        "item" => "/",
      ],
      [
        "@type" => "ListItem",
        "position" => 2,
        "name" => "??????",
        "item" => "/search/",
      ],
    ];

    if ($matter_id) {
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
          "sort" => $sort_id,
          "page" => 1,
        ]),
      ];
    }

    if ($animal_id) {
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
          "sort" => $sort_id,
          "page" => 1,
        ]),
      ];
    }

    if ($prefecture_id) {
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
          "sort" => $sort_id,
          "page" => 1,
        ]),
      ];
    }

    if ($page_id > 1) {
      $schema_items[] = [
        "@type" => "ListItem",
        "position" => count($schema_items) + 1,
        "name" => "{$page_id}?????????",
        "item" => Search::createUrl($breadcrumb_location = [
          "page" => $page_id,
        ] + $breadcrumb_location),
      ];
    }

    self::$schema[] = [
      "@context" => "https://schema.org",
      "@type" => "BreadcrumbList",
      "itemListElement" => array_map(fn (array $item) => [
        "item" => "https://" . _DOMAIN_ . $item["item"],
      ] + $item, $schema_items),
    ];

    if (!($sort_id || $page_id > 1)) {
      $breadcrumb_items[count($breadcrumb_items) - 1]["here"] = true;
    }

    $case_ids = CaseIndex::get($matter_id, $animal_id, $prefecture_id, $sort_id, $page_id, $version, $count);

    $items = $case_ids ? RDS::fetchAll("SELECT `id`, `matter`, `animal`, `prefecture`, `created_at`, `modified_at`, `starts_at`, `expires_at`, `head` FROM `case` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($case_ids)), "?")) . ") LIMIT {$limit};", [
      ...$case_ids,
    ]) : [];

    if ($items) {
      array_multisort(array_column($items, 1 === $sort_id ? "modified_at" : "starts_at"), SORT_DESC, array_column($items, "id"), SORT_DESC, $items);
      $items = Cases::parse($items);
    }

    $counts = [];

    foreach ([0, 1, 2,] as $id) {
      if ($id === $matter_id) {
        $counts[] = $count;
      } else {
        $counts[] = CaseCount::get($id, $animal_id, $prefecture_id);
      }
    }

    return [
      "breadcrumb" => $breadcrumb_items,
      "count" => $counts,
      "items" => $items,
      "title" => "{$p}{$m}{$a}(" . number_format($count) . "???)",
      "total_pages" => $total_pages,
    ];
  }
}
