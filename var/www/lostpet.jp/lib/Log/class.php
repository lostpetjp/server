<?php

declare(strict_types=1);

class Log
{
  static public function create(string $table, string|int $id, array $body, int $expires = 31536000): void
  {
    DynamoDB::putItem([
      "TableName" => "ksvs",
      "Item" => [
        "key" => (string)$table,
        "sort" => (string)$id,
        "value" => json_encode($body + [
          "created_at" => $_SERVER["REQUEST_TIME"],
          "ip" => Encode::encode("/ip/salt.txt", _IP_),
          "ua" => Encode::encode("/ua/salt.txt", _UA_),
          "session" => Me::$session,
        ]),
        "ttl" => $_SERVER["REQUEST_TIME"] + $expires,
      ],
    ]);
  }
}
