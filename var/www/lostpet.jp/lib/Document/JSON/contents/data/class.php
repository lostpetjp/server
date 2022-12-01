<?php

declare(strict_types=1);

class JSONDocumentData
{
  static public int $cache_time = 86400;

  static public function create()
  {
    return [
      "matter" => Matter::$data,
      "animal" => Animal::$data,
      "prefecture" => Prefecture::$data,
    ];
  }
}
