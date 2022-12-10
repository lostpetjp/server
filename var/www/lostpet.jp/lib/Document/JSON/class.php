<?php

declare(strict_types=1);

class JSONDocument
{
  private object $client;

  public function create(): void
  {
    if ("get" === _METHOD_) {
      $value = $_GET["v"] ?? "[]";
      if (isset($_GET["v"])) unset($_GET["v"]);

      if (($query = json_decode($value, true)) && is_array($query)) {
        foreach ($query as $key => $value) {
          $_GET[$key] = $value;
        }
      }
    } else {
      $_POST = json_decode(file_get_contents("php://input"), true);
    }

    $content = null;

    // public
    if (2 === _REQUEST_) {
      if ("get" === _METHOD_) {
        if ("/api/public/document" === _PATH_) {
          require __DIR__ . "/contents/document/index.php";
          $content = JSONDocumentDocument::class;
        } elseif ("/api/public/css" === _PATH_) {
          require __DIR__ . "/contents/css/index.php";
          $content = JSONDocumentCss::class;
        } elseif ("/api/public/count" === _PATH_) {
          require __DIR__ . "/contents/count/index.php";
          $content = JSONDocumentCount::class;
        } elseif ("/api/public/data" === _PATH_) {
          require __DIR__ . "/contents/data/index.php";
          $content = JSONDocumentData::class;
        } elseif ("/api/public/date" === _PATH_) {
          if (is_string($_GET["path"] ?? null)) {
            require __DIR__ . "/contents/date/index.php";
            $content = JSONDocumentDate::class;
          }
        }
      }

      // private
    } elseif (3 === _REQUEST_) {
      if ("post" === _METHOD_) {
        if ("/api/private/contact" === _PATH_) {
          require __DIR__ . "/contents/contact/index.php";
          $content = JSONDocumentContact::class;
        }
      } else {
        if ("/api/private/recaptcha" === _PATH_) {
          require __DIR__ . "/contents/recaptcha/index.php";
          $content = JSONDocumentRecaptcha::class;
        } elseif ("/api/private/session" === _PATH_) {
          require __DIR__ . "/contents/session/index.php";
          $content = JSONDocumentSession::class;
        }
      }
    }

    if (!$content) Document::error(404);

    $this->client = new JSONDocumentClient($content);
    $this->echo();
  }

  public function echo(): void
  {
    Etag::echo();

    $json = json_encode([
      "body" => $this->client->body,
    ] + (3 === _REQUEST_ ? [
      "version" => Config::$version,  // 現在のバージョン情報
    ] : []));

    if (1024 > strlen($json)) {
      $json = gzencode($json, 4);
      header('content-encoding:gzip');
    }

    header('cache-control:max-age=' . $this->client->cache_time . ',immutable');
    header('x-robots-tag:noindex');
    header('expect-ct:max-age=86400,enforce');

    http_response_code(200);
    header('content-length:' . strlen($json));
    echo $json;

    exit;
  }
}

class JSONDocumentClient
{
  public int $cache_time = 0;

  public array $body = [];

  public function __construct(string $content)
  {
    $this->body = $content::create();
    $this->cache_time = $content::$cache_time;
  }
}
