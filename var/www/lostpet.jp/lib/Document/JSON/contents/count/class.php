<?php

declare(strict_types=1);

class JSONDocumentCount
{
  static public int $cache_time = 1;

  static public function create()
  {
    $version = CaseVersion::get(0, 0, 0, 0);

    self::$cache_time = 60;
    Etag::generate(_PATH_,  max(filemtime(__FILE__), $version));

    $rows = RDS::fetchAll("SELECT `matter`, `animal`, `prefecture`, `count` FROM `case-count` WHERE `matter`>0 AND `animal`>0 AND `prefecture`>0;");

    $count_map = [];

    foreach ($rows as $row) {
      $count_map[$row["matter"]][$row["animal"]][$row["prefecture"]] = $row["count"];
    }

    return $count_map;
  }
}
