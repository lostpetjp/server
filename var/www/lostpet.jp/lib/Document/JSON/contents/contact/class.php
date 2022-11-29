<?php

declare(strict_types=1);

class JSONDocumentContact
{
  static public int $cache_time = 0;

  static public function create()
  {
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

    $email_encode = Encode::encode("/email/salt.txt", $email_decode);

    if (
      !$description
      || 5 > strlen($description)
      || strlen($description) > 2500
    ) {
      $error = "「本文」を5〜100文字くらいで入力して下さい。";
    }

    if (!$error) {
    }

    return ($error ? [
      "error" => $error,
    ] : []) + [
      "status" => true,
    ];
  }
}
