<?php

declare(strict_types=1);

class JSONDocumentComment
{
  static public int $cache_time = 1;

  static public function create()
  {
    if (is_string($_COOKIE["PHPSESSID"] ?? null)) Session::load(true);

    $case_id = $_GET["id"];

    $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? LIMIT 1;", [
      $case_id,
    ]);

    $version = $case_data["updated_at"];
    Etag::generate(_PATH_,  max(filemtime(__FILE__), $version));

    $case_data["head"] = json_decode($case_data["head"], true);
    $case_data["body"] = json_decode($case_data["body"], true);

    // for media
    [$case_data,] = Cases::parse([$case_data,]);

    var_dump($case_data);
    exit;

    return [
      "data" => $case_data,
      "status" => true,
    ];


    return [
      "status" => false,
    ];
  }
}
