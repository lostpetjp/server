<?php

declare(strict_types=1);

require __DIR__ . "/../../templates/doc/index.php";

class HTMLDocumentContactContent implements HTMLDocumentContentInterface
{
  static public string $template = HTMLDocumentDocTemplate::class;
  static public int $id = 4;

  static public int $cache_time = 1;
  static public string $pathname = "/contact";
  static public string $search = "";

  static public array $css = [
    4, 9, 15, 16, 22, 24,
  ];

  static public array $js = [];

  static public string $title = "問い合わせ";
  static public string $description = "LOSTPET.JP (迷子ペットのデータベース)に関するお問い合わせや報告、要望などを受け付けてます。";

  static public array $head = [];

  static public function ready(): void
  {
    if (1 === _STAGE_) {
      self::$cache_time = 600;
      Etag::generate(_PATH_, max(filemtime(__FILE__), $_SERVER["REQUEST_TIME"] - 3600));
    }
  }

  static public function create(): array
  {

    return [
      "title" => "問い合わせ",
      "description" => "LOSTPET.JP (迷子ペットのデータベース)に関するお問い合わせや報告、要望などを受け付けてます。お問い合わせの内容が公開されることはありません。",
      "body" => [
        "attribute" => [
          "action" => "",
          "class" => "c9",
          "method" => "get",
        ],
        "children" => [
          [
            "children" => [
              [
                "attribute" => [
                  "class" => "c15h",
                ],
                "children" => "タイトル",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c24w",
                ],
                "children" => [
                  "attribute" => [
                    "autocapitalize" => "none",
                    "autocomplete" => "off",
                    "class" => "c15i",
                    "inputmode" => "text",
                    "maxlength" => "50",
                    "minlength" => "5",
                    "name" => "title",
                    "required" => true,
                    "spellcheck" => "false",
                    "type" => "text",
                  ],
                  "tagName" => "input",
                ],
                "tagName" => "div",
              ],
            ],
            "tagName" => "div",
          ],
          [
            "children" => [
              [
                "attribute" => [
                  "class" => "c15h",
                ],
                "children" => "メールアドレス",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c24w",
                ],
                "children" => [
                  "attribute" => [
                    "autocapitalize" => "none",
                    "autocomplete" => "off",
                    "class" => "c15i",
                    "inputmode" => "email",
                    "maxlength" => "50",
                    "minlength" => "5",
                    "name" => "email",
                    "required" => true,
                    "spellcheck" => "false",
                    "type" => "email",
                  ],
                  "tagName" => "input",
                ],
                "tagName" => "div",
              ],
            ],
            "tagName" => "div",
          ],
          [
            "children" => [
              [
                "attribute" => [
                  "class" => "c15h",
                ],
                "children" => "本文",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c24w",
                ],
                "children" => [
                  "attribute" => [
                    "autocapitalize" => "none",
                    "autocomplete" => "off",
                    "class" => "c15t",
                    "class" => "c15t",
                    "inputmode" => "text",
                    "maxlength" => "2000",
                    "minlength" => "5",
                    "name" => "description",
                    "required" => true,
                    "spellcheck" => "false",
                  ],
                  "tagName" => "textarea",
                ],
                "tagName" => "div",
              ],
            ],
            "tagName" => "div",
          ],
          // recaptcha
          [
            "attribute" => [
              "class" => "c16",
            ],
            "tagName" => "div",
          ],
          // button
          [
            "attribute" => [
              "class" => "c4w",
            ],
            "children" => [
              "attribute" => [
                "class" => "a3 c4 ht1",
                "disabled" => true,
              ],
              "children" => "送信",
              "tagName" => "button",
            ],
            "tagName" => "div",
          ],
        ],
        "tagName" => "form",
      ],
    ];
  }
}
