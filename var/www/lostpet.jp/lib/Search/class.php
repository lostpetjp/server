<?php

declare(strict_types=1);

class Search
{
  static public function parseUrl(string $pathname): array
  {
    // 旧URLの修正
    // /case/:lost:bird:aichi:2
    $pathname = str_replace("/case/", "/search/", $pathname); // /{search}/:lost:bird:aichi:2
    $pathname = str_replace(":other", "/others", $pathname); // ":other" => ":others"
    $pathname = preg_replace("/\:b(\:|\z)/", "/new/", $pathname);
    $pathname = str_replace(":", "/", $pathname); // /{search}/{/}lost{/}bird{/}aichi{/}2

    $object = [
      "matter" => 0,
      "animal" => 0,
      "prefecture" => 0,
      "sort" => 0,
      "page" => 1,
    ];

    foreach (Matter::$data as $matter) {
      if (false !== strpos($pathname, $matter["name"]) && $matter["search"]) {
        $object["matter"] = $matter["id"];
        break;
      }
    }

    foreach (Animal::$data as $animal) {
      if (false !== strpos($pathname, $animal["name"]) && $animal["search"]) {
        $object["animal"] = $animal["id"];
        break;
      }
    }

    foreach (Prefecture::$data as $prefecture) {
      if (false !== strpos($pathname, $prefecture["name"]) && $prefecture["search"]) {
        $object["prefecture"] = $prefecture["id"];
        break;
      }
    }

    if (false !== strpos($pathname, "new")) {
      $object["sort"] = 1;
    }

    if (preg_match("/\/([0-9]+)/", $pathname, $matches)) {
      $object["page"] = (int)$matches[1];
    }

    if (2 > $object["page"]) $object["page"] = 1;

    return $object;
  }

  static public function createUrl(array $object): string
  {
    // "/search/lost/dog/tokyo/new/"
    $matter_id = $object["matter"];
    $animal_id = $object["animal"];
    $prefecture_id = $object["prefecture"];
    $sort_id = $object["sort"];
    $page_id = $object["page"];

    $matter = $matter_id ? array_filter(Matter::$data, fn (array $entry) => $entry["search"])[$matter_id] ?? null : null;
    $animal = $animal_id ? array_filter(Animal::$data, fn (array $entry) => $entry["search"])[$animal_id] ?? null : null;
    $prefecture = $prefecture_id ? array_filter(Prefecture::$data, fn (array $entry) => $entry["search"])[$prefecture_id] ?? null : null;

    return "/search/" . implode("/", array_filter([
      $matter ? $matter["name"] : null,
      $animal ? $animal["name"] : null,
      $prefecture ? $prefecture["name"] : null,
      $sort_id ? "new" : null,
      $page_id > 1 ? $page_id : null,
    ], fn (string|int|null $token) => $token));
  }
}
