<?php

declare(strict_types=1);

class Media
{
  static public function createS3Key(int $id, string $name)
  {
    $start1 = floor($id / 100000000) * 100000000;
    $start2 = floor($id / 1000000) * 1000000;
    $start3 = floor($id / 10000) * 10000;
    $start4 = floor($id / 100) * 100;

    return "upload/src/media/" . max(1, ($start1)) . "-" . ($start1 + 100000000 - 1) . "/" . max(1, ($start2)) . "-" . ($start2 + 1000000 - 1) . "/" . max(($start3), 1) . "-" . ($start3 + 10000 - 1) . "/" . max(($start4), 1) . "-" . ($start4 + 100 - 1) . "/" . $name;
  }

  // m138417s1599x1199z.jpg

  static public function parse(string $name): ?array
  {
    if (preg_match("/\Am([0-9]+)s([0-9]+)x([0-9]+)z\.(jpg|png)\z/", $name, $matches)) {
      $id = (int)$matches[1];
      $height = (int)$matches[3];
      $width = (int)$matches[2];
      $extension = $matches[4];

      return [
        "id" => $id,
        "height" => $height,
        "width" => $width,
        "extension" => $extension,
        "prefix" => "m{$id}s{$width}x{$height}z",
        "suffix" => ".{$extension}",
      ];
    }

    return null;
  }
}
