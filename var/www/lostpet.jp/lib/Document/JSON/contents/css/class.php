<?php

declare(strict_types=1);

class JSONDocumentCss
{
  static public int $cache_time = 86400;

  static public function create()
  {
    $id = $_GET["id"];
    $response = [];

    if ($id) {
      $row = RDS::fetch("SELECT `version`, `map` FROM `css` WHERE `id`=? LIMIT 1;", [
        $id,
      ]);

      if ($row) {
        Etag::generate(_PATH_,  max(filemtime(__FILE__), $row["version"]));

        $response = json_decode($row["map"], true);
      }
    }

    return $response;
  }
}
