<?php

declare(strict_types=1);

define("_SERVER_", $_SERVER["SERVER_NAME"] ?? "lostpet.jp");

if ("dev.lostpet.jp" === _SERVER_) {
  ini_set("opcache.enable", "0");
  header("x-robots-tag: noindex");
  header("cache-control: no-cache,no-store,must-revalidate,must-understand,private");
} elseif ("localhost" === _SERVER_) {
  require __DIR__ . "/../lib/preload.php";
}

// TODO: op-cache有効時に解除
require __DIR__ . "/../lib/preload.php";

require __DIR__ . "/../lib/Config/index.php";

set_error_handler(function ($no, $str, $file, $line) {
  new Discord('error', [
    "content" => "```{$no}: {$str}```\n{$file}:{$line}",
  ]);
});

register_shutdown_function(function () {
  $errors = error_get_last();

  if ($errors) {
    new Discord('error', [
      "content" => "```" . json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "```\n" . _PATH_,
    ]);
  }
});

define("_DOMAIN_", "lostpet.jp");
define("_METHOD_", strtolower($_SERVER["REQUEST_METHOD"] ?? "GET"));

if (php_sapi_name() === "cli") {
  define("_DIR_", "/var/www/lostpet.jp");
  define("_IP_", "cli");
  define("_PATH_", $argv[1] ?? "/");
  define("_STAGE_", 3);
  define("_REQUEST_", 1);
  define("_UA_", "cli");

  require __DIR__ . "/cli" . _PATH_ . ".php";

  exit;
} else {
  define("_DIR_", realpath("../"));
  define("_IP_", $_SERVER["HTTP_X_FORWARDED_FOR"] ?? ($_SERVER["REMOTE_ADDR"] ?? "unknown"));
  define("_PATH_", $_SERVER["SCRIPT_NAME"]);
  define("_STAGE_", ("dev." . _DOMAIN_ === _SERVER_) ? 2 : 1);
  define("_REQUEST_", "/api/" === substr(_PATH_, 0, 5) ? (0 === strpos(_PATH_, "/api/private/") ? 3 : 2) : 1);
  define("_UA_", $_SERVER["HTTP_USER_AGENT"] ?? "human");
}

if (2 === _STAGE_) {
  if (_IP_ !== Secret::get("/admin/ip.txt")) {
    Document::error(500);
  }

  if (false !== strpos(_PATH_, ".")) {
    $pathinfo = pathinfo(_PATH_);

    if (in_array($pathinfo["extension"], ["css", "js",], true)) {
      $path = _DIR_ . "/public_html" . _PATH_;
      if (!file_exists($path)) Document::error(404);
      Etag::generate(_PATH_, filemtime($path));
      $body = gzencode(file_get_contents($path), 4);
      header("cache-control: max-age=1,immutable");
      header("content-type:" . ("js" === $pathinfo["extension"] ? "application/javascript" : "text/css") . ";charset=UTF-8");
      header("content-length:" . strlen($body));
      header("content-encoding:gzip");
      Etag::echo();
      echo $body;
      exit;
    }
  }
}

$is_test = (2 === _STAGE_ && _PATH_ === "/test");

if (3 === _REQUEST_ || $is_test) {
  session_cache_limiter("");
  session_start();
}

if ($is_test) {
  require __DIR__ . "/test.php";
  exit;
}

Document::create(1 === _REQUEST_ ? new HTMLDocument : new JSONDocument);

exit;
