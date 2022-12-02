<?php

declare(strict_types=1);

class JSONDocumentData
{
  static public int $cache_time = 1;

  static public function create()
  {
    self::$cache_time = 1 === _STAGE_ ? 3600 : 600;
    Etag::generate(_PATH_,  max(filemtime(__FILE__), (int)strtotime(date("Y-m-d " . (1 === _STAGE_ ? "00:00" : "H:00") . ":00"))));

    return [
      "matter" => Matter::$data,
      "animal" => Animal::$data,
      "prefecture" => Prefecture::$data,
    ];
  }
}
