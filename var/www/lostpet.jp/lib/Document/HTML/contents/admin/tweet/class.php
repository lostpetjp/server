<?php

declare(strict_types=1);

class HTMLDocumentAdminTweetContent
{
  static public function create(int $id)
  {
    $case_data = RDS::fetch("SELECT * FROM `case` WHERE `id`=? LIMIT 1;", [
      $id,
    ]);
    $matter_id = $case_data["matter"];
    $animal_id = $case_data["animal"];
    $animal_title = 99 === $animal_id ? null : Animal::$data[$animal_id]["title"];
    $prefecture_id = $case_data["prefecture"];

    $head = json_decode($case_data["head"], true);
    $pet = $head["pet"] ?? null;
    $body = json_decode($case_data["body"], true);

    $prefix = date("Y/n/j", $case_data["starts_at"]) . "、" . Prefecture::$data[$prefecture_id]["title"] . " " . $head["location"] . "で";
    $suffix = "\n\n";
    $hashtag = "";

    if (1 === $matter_id) {
      $prefix .= "迷子になった";

      if ($animal_title && $pet) {
        $prefix .= "{$animal_title}の {$pet}ちゃん ";
      } elseif ($animal_title) {
        $prefix .= "{$animal_title}";
      } elseif ($pet) {
        $prefix .= " {$pet}ちゃん ";
      } else {
        $prefix .= "ペット";
      }

      $prefix .= "を探しています。";

      $suffix .= "↓ページにある掲示板に情報提供をお願いします。";
    } elseif (2 === $matter_id) {
      if ($animal_title) {
        $prefix .= $animal_title;
      } else {
        $prefix .= "ペット";
      }

      $prefix .= "が保護されました。";

      $suffix .= "飼い主に心当たりがある方は、↓ページにある掲示板にご連絡下さい。";
    } elseif (3 === $matter_id) {
      if ($animal_title) {
        $prefix .= $animal_title;
      } else {
        $prefix .= "ペット";
      }

      $prefix .= "が目撃されました。";

      $suffix .= "↓ページにある掲示板に情報提供をお願いします。";
    } elseif (4 === $matter_id) {
      if ($animal_title) {
        $prefix .= $animal_title;
      } else {
        $prefix .= "ペット";
      }

      $prefix .= "のご遺体が発見されました。";

      $suffix .= "飼い主に心当たりがある方は↓ページにある掲示板にご連絡下さい。";
    }

    if ($animal_title) {
      $hashtag .= "迷子{$animal_title}";
    } else {
      $hashtag .= "迷子ペット";
    }

    echo '<!DOCTYPE html>'
      . '<html>'
      . '<head>'
      .   '<title>ツイート</title>'
      . '</head>'
      . '<body>'
      . '<h1>案件のシェア</h1>'

      .  '<textarea id="text" style="width:80vw;height:300px;display:block">'
      .  implode("\n", [
        $head["title"],
        $body["description"],
      ])
      .  '</textarea>'

      .  '<ul id="list" style="margin-top:24px;list-style-type:none;margin:36px 0;"></ul>'

      . '<textarea id="result" style="width:80vw;height:300px;display:block"></textarea>'

      . '<a href="" id="share" style="margin-top:24px;text-align:center;display:block;height:40px;line-height:40px;background-color:#f8ca8e;color:#000;width:100px;border-radius:6px;text-decoration:none;">送信</a>'

      . '<script>
      const data = ' . json_encode([
        "prefix" => $prefix,
        "suffix" => "{$suffix}\n\nhttps://lostpet.jp/{$id}",
        "hashtags" => $hashtag,
      ]) . ';
      ' . file_get_contents(__DIR__ . "/tweet.js") . '
      </script>'
      . '</body>'
      . '</html>';

    exit;
  }
}
