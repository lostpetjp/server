<?php

declare(strict_types=1);

/**
 * Recaptcha認証の成功結果を数時間、保持する
 */
class JSONDocumentRecaptcha
{
  static public int $cache_time = 0;

  static public function create()
  {
    $can_skip = false;
    $recaptcha = $_SESSION["recaptcha"] ?? null;

    if (isset($recaptcha) && is_array($recaptcha)) {
      $expires_at = $recaptcha["expires_at"] ?? null;
      $can_skip = is_int($expires_at) && $expires_at > $_SERVER["REQUEST_TIME"];
    }

    if (!$can_skip && array_key_exists("recaptcha", $_SESSION)) {
      unset($_SESSION["recaptcha"]);
    }

    return [
      "status" => $can_skip,
    ];
  }
}
