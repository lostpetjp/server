<?php
class HTMLDocumentAdminContactContent
{
  static public function create(int $id)
  {
    $body = S3::getObjectBody(Config::$bucket, "logs/contact/{$id}.json.gz");

    $data = json_decode(gzdecode($body), true);

    $data["created_at"] = date("Y-m-d H:i:s", $data["created_at"]);
    $email = Encode::decode("/email/salt.txt", $data["email"]);
    $data["email"] = '<a href="mailto:' . $email . '">' . $email . '</a>';
    $ip = Encode::decode("/ip/salt.txt", $data["ip"]);
    $data["ip"] = $ip . ' (' . gethostbyaddr($ip) . ')';
    $data["ua"] = Encode::decode("/ua/salt.txt", $data["ua"]);

    foreach ($data as $key => $value) {
      echo "<h2>{$key}</h2>";
      echo "<pre><code>{$value}</code></pre>";
    }

    exit;
  }
}
