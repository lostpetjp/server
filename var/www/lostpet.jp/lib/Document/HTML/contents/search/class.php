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
      Etag::generate(_PATH_, max(filemtime(__FILE__), $version, $_SERVER["REQUEST_TIME"] - (1 === _STAGE_ ? 3600 : 10))); // 304
    } else {
      $version = CaseVersion::update($matter_id, $animal_id, $prefecture_id, $sort_id);
    }

    self::$pathname = Search::createUrl($object);

    // create title
    $p = "";

    if (0 === $prefecture_id) {
      $p = "全国の";
    } else {
      $_ = Prefecture::$data[$prefecture_id]["title"];
      $p = (1 !== $prefecture_id ? mb_substr($_, 0, -1) : $_) . "で";
    }

    $m = "";

    if (2 === $matter_id) {
      $m = "保護、目撃された";
    } else {
      $m = "迷子になった";
    }

    $a = "";

    if (0 === $animal_id || 99 === $animal_id) {
      $a = "ペット";
    } else {
      $a = Animal::$data[$animal_id]["title"];
    }

    self::$title = "{$p}{$m}{$a} ({$count}件) - 迷子ペットのデータベース";
    self::$description = "{$p}{$m}{$a}は{$count}件、登録されています。些細な情報でも、知っている方は掲示板に提供をお願いします。";

    self::$head[] = [
      "attribute" => [
        "href" => "https://" . _SERVER_ . Search::createUrl(["page" => 1,] + $object),
        "rel" => "canonical",
      ],
      "tagName" => "link",
    ];

    $case_ids = CaseIndex::get($matter_id, $animal_id, $prefecture_id, $sort_id, $page_id, $version, $count);

    $items = $case_ids ? RDS::fetchAll("SELECT `id`, `matter`, `animal`, `prefecture`, `created_at`, `updated_at`, `starts_at`, `expires_at`, `head` FROM `case` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($case_ids)), "?")) . ") LIMIT {$limit};", [
      ...$case_ids,
    ]) : [];

    if ($items) {
      array_multisort(array_column($items, 1 === $sort_id ? "updated_at" : "starts_at"), SORT_DESC, $items);

      $items = array_map(fn (array $row) => [
        "head" => json_decode($row["head"], true),
      ] + $row, $items);
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
      "count" => $counts,
      "matter" => $matter_id,
      "animal" => $animal_id,
      "prefecture" => $prefecture_id,
      "sort" => $sort_id,
      "page" => $page_id,
      "items" => $items,
      "title" => "{$p}{$m}{$a} ({$count}件)",
      "total_pages" => $total_pages,
    ];
  }
}