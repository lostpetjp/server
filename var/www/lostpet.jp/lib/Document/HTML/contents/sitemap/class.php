<?php

declare(strict_types=1);

preg_match("/\A\/sitemap\/([0-9]+)\.xml\z/", $pathname, $matches);

if ($matches) {
  $page = (int)$matches[1];
  $start = (($page - 1) * 1000) + 1;
  $end = $start + (1000 - 1);

  Etag::generate(_PATH_, max(filemtime(__FILE__), (int)RDS::fetchColumn("SELECT MAX(`modified_at`) FROM `case` WHERE ?>=`id` AND `id`>=? AND `status`=?;", [
    $end,
    $start,
    1,
  ])));

  $rows = RDS::fetchAll("SELECT `id`, `created_at`, `modified_at` FROM `case` WHERE ?>=`id` AND `id`>=? AND `status`=?;", [
    $end,
    $start,
    1,
  ]);

  if ($rows) {
    $body = '<?xml version="1.0" encoding="UTF-8"?>'
      . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($rows as $row) {
      $is_new = ($row["created_at"] > ($_SERVER["REQUEST_TIME"] - (30 * 86400)));

      $body .= '<url>'
        . '<loc>https://lostpet.jp/' . $row["id"] . '</loc>'
        . '<priority>' . ($is_new ? '1.0' : '0.5') . '</priority>'
        . '<changefreq>' . ($is_new ? 'daily' : 'weekly') . '</changefreq>'
        . '<lastmod>' . date("Y-m-d", $row["modified_at"]) . '</lastmod>'
        . '</url>';
    }

    $body .= '</urlset>';

    _echo($body);
  } else {
    Document::redirect("/sitemap/index.xml", 3600);
  }
} else {
  Document::redirect("/sitemap/index.xml", 86400);
}

// index file
Etag::generate(_PATH_, max(filemtime(__FILE__), RDS::fetchColumn("SELECT MAX(`modified_at`) FROM `case`;")));

Document::redirect("/sitemap/index.xml", 600);

$max_id = RDS::fetchColumn("SELECT MAX(`id`) FROM `case` LIMIT 1;");
$max_page = ceil($max_id / 1000);

$body = '<?xml version="1.0" encoding="UTF-8"?>'
  . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

for ($i = $max_page; $i >= 1; $i--) {
  $start = (($i - 1) * 1000) + 1;
  $end = $start + (1000 - 1);

  $modified_at = RDS::fetchColumn("SELECT MAX(`modified_at`) FROM `case` WHERE ?>=`id` AND `id`>=? AND `status`=?;", [
    $end,
    $start,
    1,
  ]);

  $body .= '<sitemap>'
    . '<loc>https://lostpet.jp/sitemap/' . $i . '.xml</loc>'
    . '<lastmod>' . date("Y-m-d", $modified_at) . '</lastmod>'
    . '</sitemap>';
}

$body .= '</sitemapindex>';

_echo($body);

function _echo(string $body)
{
  Etag::echo();

  header('cache-control:max-age=3600,public,immutable,stale-if-error=86400');  // ,must-revalidate

  http_response_code(200);
  header('content-length:' . strlen($body));
  header('content-type:application/xml;charset=utf-8');

  echo $body;

  exit;
}
