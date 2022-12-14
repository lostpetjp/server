<?php

declare(strict_types=1);

require __DIR__ . "/../../templates/doc/index.php";

class HTMLDocumentPrivacyContent implements HTMLDocumentContentInterface
{
  static public string $template = HTMLDocumentDocTemplate::class;
  static public int $id = 2;

  static public int $cache_time = 1;
  static public string $pathname = "/privacy";
  static public string $search = "";

  static public array $css = [
    2,
  ];
  static public array $js = [];
  static public array $schema = [];
  static public array $data = [];

  static public string $title = "プライバシーポリシー";
  static public string $description = "LOSTPET.JP (迷子ペットのデータベース)の利用規約です。";

  static public array $head = [
    [
      "attribute" => [
        "content" => "article",
        "property" => "og:type",
      ],
      "tagName" => "meta",
    ],
  ];

  static public function create(string $pathname): array
  {
    self::$cache_time = 1 === _STAGE_ ? 600 : 1;
    Etag::generate(_PATH_,  filemtime(__FILE__));

    self::$schema[] = [
      "@context" => "https://schema.org",
      "@type" => "BreadcrumbList",
      "itemListElement" => [
        [
          "@type" => "ListItem",
          "position" => 1,
          "name" => "ホーム",
          "item" => "https://" . _DOMAIN_ . "/",
        ],
        [
          "@type" => "ListItem",
          "position" => 2,
          "name" => self::$title,
          "item" => "https://" . _DOMAIN_ . self::$pathname,
        ],
      ],
    ];

    return [
      "title" => "プライバシーポリシー",
      "description" => "迷子ペットのデータベースではプライバシーポリシーを定めてます。サイトを利用するにはこれに同意する必要があります。",
      "body" => [
        [
          "children" => [
            [
              "attribute" => [
                "class" => "c2",
              ],
              "children" => "個人情報の定義",
              "tagName" => "h2",
            ],
            [
              "children" => "本プライバシーポリシーにおいて個人情報とは、個人情報保護法第2条第1項により定義された個人情報を意味します。",
              "tagName" => "p",
            ],
          ],
          "tagName" => "section",
        ],
        [
          "children" => [
            [
              "attribute" => [
                "class" => "c2",
              ],
              "children" => "個人情報の利用目的",
              "tagName" => "h2",
            ],
            [
              "children" => "個人情報を、以下の目的で利用いたします。",
              "tagName" => "p",
            ],
            [
              "children" => array_map(fn (string $text) => [
                "children" => $text,
                "tagName" => "li",
              ], [
                "個人を特定できない範囲においての統計情報の作成および利用",
                "個人を特定できない範囲においての新規開発に必要なデータの解析や分析",
              ]),
              "tagName" => "ul",
            ],
          ],
          "tagName" => "section",
        ],
        [
          "children" => [
            [
              "attribute" => [
                "class" => "c2",
              ],
              "children" => "第三者のトラッキングシステム",
              "tagName" => "h2",
            ],
            [
              "children" => "本サービスは、統計を作成したり広告を配信するために第三者のツールを利用しています。cookieやウェブビーコンを通し、情報は、個人識別できない範囲で第三者が直接、取得する仕組みです。具体的に利用しているツールは以下です。詳細は各サイトをご覧下さい。",
              "tagName" => "p",
            ],
            [
              "children" => array_map(fn (array $text) => [
                "children" => $text,
                "tagName" => "li",
              ], [
                [
                  "Google Analytics (",
                  [
                    "attribute" => [
                      "class" => "a1",
                      "href" => "//www.google.com/analytics/",
                      "rel" => "noopener",
                      "target" => "_blank",
                    ],
                    "children" => "https://www.google.com/analytics/",
                    "tagName" => "a",
                  ],
                  ")",
                ],
                [
                  "Google Adsense (",
                  [
                    "attribute" => [
                      "class" => "a1",
                      "href" => "//policies.google.com/technologies/ads?hl=ja",
                      "rel" => "noopener",
                      "target" => "_blank",
                    ],
                    "children" => "https://policies.google.com/technologies/ads?hl=ja",
                    "tagName" => "a",
                  ],
                  ")",
                ],
              ]),
              "tagName" => "ul",
            ],
          ],
          "tagName" => "section",
        ],
        [
          "children" => [
            [
              "attribute" => [
                "class" => "c2",
              ],
              "children" => "個人情報の開示",
              "tagName" => "h2",
            ],
            [
              "children" => "個人情報保護法その他の法令により開示の義務を負う場合、請求に従って遅滞なく開示を行います。",
              "tagName" => "p",
            ],
          ],
          "tagName" => "section",
        ],
        [
          "children" => [
            [
              "attribute" => [
                "class" => "c2",
              ],
              "children" => "改定",
              "tagName" => "h2",
            ],
            [
              "children" => "将来、必要に応じて本プライバシーポリシーを改定することがあります。ユーザーは改定された利用規約に同意したものとみなされます。改定した場合、ウェブサイト上でユーザーに通知します。",
              "tagName" => "p",
            ],
          ],
          "tagName" => "section",
        ],
        [
          "children" => [
            [
              "attribute" => [
                "class" => "c2",
              ],
              "children" => "附則",
              "tagName" => "h2",
            ],
            [
              "children" => "将来、必要に応じて本プライバシーポリシーを改定することがあります。ユーザーは改定された利用規約に同意したものとみなされます。改定した場合、ウェブサイト上でユーザーに通知します。",
              "tagName" => "p",
            ],
            [
              "children" => array_map(fn (string $text) => [
                "children" => $text,
                "tagName" => "li",
              ], [
                "2018年3月18日 制定",
                "2019年5月1日 改定",
              ]),
              "tagName" => "ul",
            ],
          ],
          "tagName" => "section",
        ],
      ],
    ];
  }
}
