<?php

DynamoDB::updateItem([
  "TableName" => 'ksvs',
  "Key" => [
    "sort" => "sort",
    "key" => "aaa",
  ],
  "UpdateExpression" => "SET #value=:value, #ttl=:ttl",
  "ExpressionAttributeNames" => [
    "#value" => "value",
    "#ttl"   => "ttl",
  ],
  "ExpressionAttributeValues" => [
    ":value" => $_SERVER["REQUEST_TIME"],
    ":ttl"   => $_SERVER["REQUEST_TIME"] + 86400,
  ],
  "ReturnValues" => "UPDATED_OLD",
], true);
