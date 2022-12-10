<?php

declare(strict_types=1);

class HTMLDocumentAdminContactContent
{
  static public function create(int $id)
  {
    $row = DynamoDB::getItem([
      "TableName" => "ksvs",
      "Key" => [
        "sort" => "contact",
        "key"   => (string)$id,
      ],
      "ProjectionExpression"     => "#value",
      "ExpressionAttributeNames" => [
        "#value" => "value",
      ],
    ]);

    $data = json_decode($row["value"], true);

    $data["created_at"] = date("Y-m-d H:i:s", $data["created_at"]);
    $email = Encode::decode("/email/salt.txt", $data["email"]);
    $data["email"] = '<a href="mailto:' . $email . '">' . $email . '</a>';
    $ip = Encode::decode("/ip/salt.txt", $data["ip"]);
    $data["ip"] = $ip . ' (' . gethostbyaddr($ip) . ')';
    $data["ua"] = Encode::decode("/ua/salt.txt", $data["ua"]);

    echo '<!DOCTYPE html>'
      . '<html>'
      . '<head>'
      .   '<title>問い合わせの管理</title>'
      . '</head>'
      . '<body>'
      . '<h1>問い合わせの管理</h1>';

    foreach ($data as $key => $value) {
      echo "<h2>{$key}</h2>";
      echo "<pre><code>{$value}</code></pre>";
    }

    echo '</body>'
      . '</html>';

    exit;
  }
}
