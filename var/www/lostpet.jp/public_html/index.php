<?php

declare(strict_types=1);

set_error_handler(function ($no, $str, $file, $line) {
  new Discord('error', [
    "content" => "```{$no}: {$str}```\n{$file}:{$line}",
  ]);
});

register_shutdown_function(function () {
  $errors = error_get_last();

  if ($errors) {
    new Discord('error', [
      "content" => "```" . json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "```",
    ]);
  }
});

define("_DOMAIN_", "lostpet.jp");
define("_METHOD_", strtolower($_SERVER["REQUEST_METHOD"] ?? "GET"));
define("_DIR_", realpath("../"));

if (php_sapi_name() === "cli") {
  define("_IP_", "cli");
  define("_PATH_", $argv[1] ?? "/");
  define("_STAGE_", 3);
  define("_REQUEST_", 1);
  define("_UA_", "cli");

  Batch::run();
  exit;
} else {
  define("_IP_", $_SERVER["HTTP_X_FORWARDED_FOR"] ?? ($_SERVER["REMOTE_ADDR"] ?? "unknown"));
  define("_PATH_", $_SERVER["SCRIPT_NAME"]);
  define("_STAGE_", ("dev." . _DOMAIN_ === $_SERVER["SERVER_NAME"]) ? 2 : 1);
  define("_REQUEST_", "/api/" === substr(_PATH_, 0, 5) ? (0 === strpos(_PATH_, "/api/private/") ? 3 : 2) : 1);
  define("_UA_", $_SERVER["HTTP_USER_AGENT"] ?? "human");
}

if (3 === _REQUEST_ || (2 === _STAGE_ && _PATH_ === "/test")) {
  session_cache_limiter("");
  session_start();
}

Document::create(1 === _REQUEST_ ? new HTMLProcess : new APIProcess);

exit;
