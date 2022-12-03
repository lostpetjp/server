<?php

declare(strict_types=1);

class JSONDocumentDate
{
  static public int $cache_time = 1;

  static public function create()
  {
    $pathname = $_GET["path"];

    $object = Search::parseUrl($pathname);
    $matter_id = $object["matter"];
    $animal_id = $object["animal"];
    $prefecture_id = $object["prefecture"];
    $sort_id = $object["sort"];
    $page_id = $object["page"];

    $version = CaseVersion::get($matter_id, $animal_id, $prefecture_id, $sort_id);

    if ($version) {
      Etag::generate(_PATH_, max(filemtime(__FILE__), $version));
    } else {
      $version = CaseVersion::update($matter_id, $animal_id, $prefecture_id, $sort_id);
    }

    $column = 1 === $sort_id ? "updated_at" : "starts_at";

    $case_ids = CaseIndex::get($matter_id, $animal_id, $prefecture_id, $sort_id, $page_id, $version);
    $count = count($case_ids);
    $case_ids = $count > 1 ? [$case_ids[0], $case_ids[$count - 1],] : (1 === $count ? [$case_ids[0], $case_ids[0],] : []);

    $items = $case_ids ? RDS::fetchAll("SELECT `{$column}` FROM `case` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($case_ids)), "?")) . ") LIMIT {$limit};", [
      ...$case_ids,
    ]) : [];

    if ($items) array_multisort(array_column($items, $column), SORT_ASC, $items);

    return [
      "data" => array_map(fn (int $time) => date("Y/m/d", $time), array_column($items, $column)),
    ];
  }
}
