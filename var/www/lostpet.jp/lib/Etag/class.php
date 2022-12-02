<?php

declare(strict_types=1);

// cloudfrontは、nameが同一だと200を返しても、304を返してしまう？ (要確認)
// timeをnameに含めているので、実質的にtimeを比較する意味はない
class Etag
{
  static public ?string $time = null;
  static public ?string $name = null;

  static public function generate(string $name, int $time): void
  {
    // $i_time = $_SERVER["HTTP_IF_MODIFIED_SINCE"] ?? null;
    $i_name = $_SERVER["HTTP_IF_NONE_MATCH"] ?? null;

    self::$time = gmdate('D, d M Y H:i:s T', $time);
    self::$name = md5($name . self::$time);

    if ($i_name && false !== strpos($i_name, '"' . self::$time . '"')) { // $i_time && strtotime($i_time) >= strtotime(self::$time) && 
      http_response_code(304);
      header('cache-control: max-age=1,public');
      self::echo();
      exit;
    }
  }

  static public function echo(): void
  {
    if (self::$name && self::$time) {
      header('etag:"' . self::$name . '"');
      header("last-modified:" . self::$time);
    }
  }
}
