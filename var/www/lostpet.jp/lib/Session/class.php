<?php

declare(strict_types=1);

class Session
{
  static public function create(): void
  {
    self::load();

    if (!Me::$session) {
      $id = RDS::insert("INSERT INTO `session` (`status`, `created_at`, `updated_at`, `expires_at`) VALUES (?, ?, ?, ?);", [
        1,
        $_SERVER["REQUEST_TIME"],
        ($_SERVER["REQUEST_TIME"] - 86400), // strict updateをするためのhack
        (600 + $_SERVER["REQUEST_TIME"]),
      ]);

      $_SESSION["session"] = [
        "expires_at" => 1,
        "id" => $id,
      ];

      Me::$session = null;

      self::load(true);
    }
  }

  static public function delete(): void
  {
    Session::load(true);

    if (Me::$session) {
      RDS::execute("UPDATE `session` SET `status`=?, `updated_at`=? WHERE `id`=? LIMIT 1;", [
        0,
        $_SERVER["REQUEST_TIME"],
        Me::$session,
      ]);

      Me::$session = null;
      if (array_key_exists("session", $_SESSION)) unset($_SESSION["session"]);
    }

    $name = session_name();

    if (isset($_COOKIE[$name])) {
      setcookie($name, $_COOKIE[$name], [
        "expires" => 1,
        "httponly" => true,
        "path" => "/api/private/",
        "secure" => true,
        "samesite" => "Lax",
      ]);
    }
  }

  static public function load(bool $strict = false): void
  {
    if (null === Me::$session) {
      if (PHP_SESSION_NONE === session_status()) session_start();

      $session = $_SESSION["session"] ?? null;

      if (is_array($session)) {
        $id = $session["id"] ?? null;
        $expires_at = $session["expires_at"] ?? null;

        if ($id && $expires_at && is_int($id) && is_int($expires_at)) {
          if ($strict || $_SERVER["REQUEST_TIME"] > $expires_at) {
            $row = RDS::fetch("SELECT `id`, `status`, `expires_at`, `updated_at` FROM `session` WHERE `id`=? LIMIT 1;", [
              $id,
            ]);

            if ($row && 1 === $row["status"] && $row["expires_at"] > $_SERVER["REQUEST_TIME"]) {
              Me::$session = $id;

              if ($_SERVER["REQUEST_TIME"] > (600 + $row["updated_at"])) {
                RDS::execute("UPDATE `session` SET `expires_at`=?, `updated_at`=? WHERE `id`=? LIMIT 1;", [
                  (86400 + $_SERVER["REQUEST_TIME"]),
                  $_SERVER["REQUEST_TIME"],
                  $id,
                ]);

                Log::create("session/{$id}", $_SERVER["REQUEST_TIME"], [
                  "id" => $id,
                ]);

                // cookieの期限延長
                $name = session_name();

                if (isset($_COOKIE[$name])) {
                  setcookie($name, $_COOKIE[$name], [
                    "expires" => ((2 * 86400) + $_SERVER["REQUEST_TIME"]),
                    "httponly" => true,
                    "path" => "/api/private/",
                    "secure" => true,
                    "samesite" => "Lax",
                  ]);
                }
              }

              $_SESSION["session"] = [
                "expires_at" => (600 + $_SERVER["REQUEST_TIME"]),
                "id" => Me::$session,
              ];
            }
          } else {
            Me::$session = $id;
          }
        }
      }

      if (null === Me::$session) {
        Me::$session = 0;
      }
    }
  }

  /*
    type:
    1: case
    2: comment
    3: contact
    4: media
  */
  static public function createRelation(int $type, int $content): void
  {
    RDS::execute("INSERT INTO `session-relation` (`type`, `session`, `content`, `status`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `status`=VALUES(`status`), `updated_at`=VALUES(`updated_at`);", [
      $type,
      Me::$session,
      $content,
      1,
      $_SERVER["REQUEST_TIME"],
      $_SERVER["REQUEST_TIME"],
    ]);

    Log::create("session-relation/" . Me::$session, "{$type}/{$content}", [
      "content" => $content,
      "type" => $type,
    ]);
  }
}
