<?php

declare(strict_types=1);

class Log
{
  static public function create(string $path, array $body): void
  {

    S3::putObject(Config::$bucket, "logs{$path}.json.gz", [
      "Body" => gzencode(json_encode([
        "created_at" => $_SERVER["REQUEST_TIME"],
        "ip" => Encode::encode("/ip/salt.txt", _IP_),
        "ua" => Encode::encode("/ua/salt.txt", _UA_),
      ]), 9),
      "ContentEncoding" => "gzip",
      "ContentType" => "application/json;charset=utf-8",
    ]);
  }
}
