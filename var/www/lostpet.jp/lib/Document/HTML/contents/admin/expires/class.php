<?php

declare(strict_types=1);

class HTMLDocumentAdminExpiresContent
{
  static public function create()
  {
    $rows = RDS::fetchAll("SELECT * FROM `case` WHERE `expires_at`>0 AND `publish`=1 AND `status`=1;");

    echo '<!DOCTYPE html>'
      . '<html>'
      . '<head>'
      .   '<title>掲載期限がある案件</title>'
      . '</head>'
      . '<body>'
      . '<h1>掲載期限がある案件</h1>'
      . '<ul>'
      .   implode("", array_map(fn (array $row) => '<li><a href="https://lostpet.jp/' . $row["id"] . '">' . $row["id"] . '</a> (' . date("Y/m/d", $row["expires_at"]) . ')</li>', $rows))
      . '</ul>'
      . '</body>'
      . '</html>';

    exit;
  }
}
