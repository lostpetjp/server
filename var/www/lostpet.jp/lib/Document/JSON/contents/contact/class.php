<?php

declare(strict_types=1);

class JSONDocumentContact
{
  static public int $cache_time = 0;

  static public function create()
  {
    if (!Recaptcha::check()) {
      Document::error(400);
    }

    Session::create();

    $title = $_POST["title"] ?? null;
    $email_decode = $_POST["email"] ?? null;
    $description = $_POST["description"] ?? null;

    $error = null;

    if (
      !$title
      || 5 > strlen($title)
      || strlen($title) > 150
    ) {
      $error = "「タイトル」を5〜100文字くらいで入力して下さい。";
    }

    if (
      !$email_decode
    ) {
      $error = "「メールアドレス」を入力して下さい。";
    }

    if (
      !$description
      || 5 > strlen($description)
      || strlen($description) > 2500
    ) {
      $error = "「本文」を5〜100文字くらいで入力して下さい。";
    }

    if (!$error) {
      $update_data = [
        "title" => $title,
        "created_at" => $_SERVER["REQUEST_TIME"],
      ];

      $id = RDS::insert("INSERT INTO `contact` (" . implode(",", array_map(fn (string $key) => "`{$key}`", array_keys($update_data))) . ") VALUES (" . implode(",", array_fill(0, count($update_data), "?")) . ");", [
        ...array_values($update_data),
      ]);

      new Discord("contact", [
        "content" => "問い合わせがありました。\n```\n" . implode("\n", [
          "id: " . $id,
          "title: " . $title,
        ]) . "\n```\n" . Config::$admin . "/contact/{$id}",
      ]);

      Session::createRelation(3, $id);

      Log::create("contact", $id, [
        "description" => $description,
        "email" => Encode::encode("/email/salt.txt", $email_decode),
      ]);
    }

    return ($error ? [
      "error" => $error,
    ] : []) + [
      "status" => $error ? false : true,
    ];
  }
}
