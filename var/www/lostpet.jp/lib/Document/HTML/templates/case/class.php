<?php
class HTMLDocumentCaseTemplate implements HTMLDocumentTemplateInterface
{
  static public int $id = 22;

  static public array $css = [
    1,
    2,
    4,
    6,
    7,
    9,
    15,
    22,
    26,
    31,
    34,
    37,
  ];

  static public array $js = [];

  static public function create(HTMLDocumentClient $client): array
  {
    $pathname = $client->pathname;
    $object = $client->body;

    $breadcrumb = $object["breadcrumb"];
    $case_data = $object["data"];
    $comment_data_set = $object["comment"];
    $has_email = $case_data["email"];
    $expires_at = $case_data["expires_at"] ?? null;
    $expires_soon = $expires_at && (($_SERVER["REQUEST_TIME"] + (7 * 86400)) > $expires_at);
    $publish = $case_data["publish"];
    $archive = $case_data["archive"];
    $starts_at = $case_data["starts_at"];
    $ends_at = !$publish ? $case_data["ends_at"] : null;

    $head = $case_data["head"];
    $body = $case_data["body"];
    $cover = $head["cover"] ?? null;
    $photos = $body["photos"] ?? [];
    $videos = $body["videos"] ?? [];
    $report = !$publish ? ($body["report"] ?? null) : null;
    $description = !$publish ? $report : $body["description"];

    $pk_items = $object["pickup"];

    return [
      "attribute" => [
        "class" => "d2a",
        "role" => "main",
      ],
      "children" => [
        "attribute" => [
          "class" => "c7 c9 d2a1",
        ],
        "children" => [
          [
            "attribute" => [
              "class" => "c9",
            ],
            "children" => [
              [
                "attribute" => [
                  "class" => "c31",
                ],
                "children" => array_map(fn (array $item) => [
                  "attribute" => [
                    "class" => "c31a",
                  ],
                  "children" => ($item["here"] ?? false) ? $item["title"] : [
                    [
                      "attribute" => [
                        "class" => "c31a1",
                        "href" => $item["pathname"],
                      ],
                      "children" => $item["title"],
                      "tagName" => "a",
                    ],
                    (($item["here"] ?? false) ? null : [
                      "attribute" => [
                        "height" => "8",
                        "viewBox" => "0 0 48 48",
                        "width" => "8",
                      ],
                      "children" => [
                        "attribute" => [
                          "d" => "m13.841 48-4.282-4.282L29.277 24 9.559 4.282 13.841 0l24 24-24 24Z",
                          "fill" =>  "currentColor",
                        ],
                        "tagName" => "path",
                      ],
                      "tagName" => "svg",
                    ]),
                  ],
                  "tagName" => "li",
                ], $breadcrumb),
                "tagName" => "ul",
              ],
              [
                "attribute" => [
                  "class" => "c1 c34h1",
                ],
                "children" => $case_data["head"]["title"],
                "tagName" => "h1",
              ],
              [
                "attribute" => [
                  "class" => "c34e",
                ],
                "children" => [
                  !$archive ? [
                    "attribute" => [
                      "class" => "c34e1",
                    ],
                    "children" => [
                      [
                        "attribute" => [
                          "class" => "a3 c34e1a ht2",
                        ],
                        "children" => "編集する",
                        "tagName" => "a",
                      ],
                      [
                        "attribute" => [
                          "class" => "a3 c34e1a ht2",
                          "href" => "/poster?id=" . $case_data["id"],
                        ],
                        "children" => "ポスター",
                        "tagName" => "a",
                      ],
                      [
                        "attribute" => [
                          "class" => "a3 c34e1a ht2",
                        ],
                        "children" => "掲載終了",
                        "tagName" => "a",
                      ],
                    ],
                    "tagName" => "div",
                  ] : null,
                  [
                    "attribute" => [
                      "class" => "c34e2",
                    ],
                    "children" => [
                      "attribute" => [
                        "class" => "c34e2a",
                        "datetime" => date(DATE_ISO8601, $case_data["modified_at"]),
                      ],
                      "children" => date("Y/m/d H:i", $case_data["modified_at"]),
                      "tagName" => "time",
                    ],
                    "tagName" => "div",
                  ],
                  [
                    "attribute" => [
                      "class" => "c34e3 c34e3c4",
                    ],
                    "children" => [
                      [
                        "attribute" => [
                          "class" => "a2 c34e3a",
                        ],
                        "children" => [
                          "attribute" => [
                            "height" => "28",
                            "viewBox" => "0 0 48 48",
                            "width" => "28",
                          ],
                          "children" => [
                            "attribute" => [
                              "d" => "M7.2 48q-1.44 0-2.52-1.08T3.6 44.4V8.22h3.6V44.4h28.44V48H7.2Zm7.2-7.2q-1.44 0-2.52-1.08T10.8 37.2V3.6q0-1.44 1.08-2.52T14.4 0h26.4q1.44 0 2.52 1.08T44.4 3.6v33.6q0 1.44-1.08 2.52T40.8 40.8H14.4Zm0-3.6h26.4V3.6H14.4v33.6Zm0 0V3.6v33.6Z",
                              "fill" => "currentColor",
                            ],
                            "tagName" => "path",
                          ],
                          "tagName" => "svg",
                        ],
                        "tagName" => "a",
                      ],
                      [
                        "attribute" => [
                          "class" => "a2 c34e3a",
                        ],
                        "children" => [
                          "attribute" => [
                            "height" => "28",
                            "viewBox" => "0 0 248 204",
                            "width" => "28",
                          ],
                          "children" => [
                            "attribute" => [
                              "d" => "M221.95 51.29c.15 2.17.15 4.34.15 6.53 0 66.73-50.8 143.69-143.69 143.69v-.04c-27.44.04-54.31-7.82-77.41-22.64 3.99.48 8 .72 12.02.73 22.74.02 44.83-7.61 62.72-21.66-21.61-.41-40.56-14.5-47.18-35.07a50.338 50.338 0 0 0 22.8-.87C27.8 117.2 10.85 96.5 10.85 72.46v-.64a50.18 50.18 0 0 0 22.92 6.32C11.58 63.31 4.74 33.79 18.14 10.71a143.333 143.333 0 0 0 104.08 52.76 50.532 50.532 0 0 1 14.61-48.25c20.34-19.12 52.33-18.14 71.45 2.19 11.31-2.23 22.15-6.38 32.07-12.26a50.69 50.69 0 0 1-22.2 27.93c10.01-1.18 19.79-3.86 29-7.95a102.594 102.594 0 0 1-25.2 26.16z",
                              "fill" => "#69ACE0",
                            ],
                            "tagName" => "path",
                          ],
                          "tagName" => "svg",
                        ],
                        "tagName" => "a",
                      ],
                      [
                        "attribute" => [
                          "class" => "a2 c34e3a",
                        ],
                        "children" => [
                          "attribute" => [
                            "height" => "28",
                            "viewBox" => "0 0 14222 14222",
                            "width" => "28",
                          ],
                          "children" => [
                            [
                              "attribute" => [
                                "cx" => "7111",
                                "cy" => "7112",
                                "fill" => "#1977f3",
                                "r" => "7111",
                              ],
                              "tagName" => "circle",
                            ],
                            [
                              "attribute" => [
                                "d" => "m9879 9168 315-2056H8222V5778c0-562 275-1111 1159-1111h897V2917s-814-139-1592-139c-1624 0-2686 984-2686 2767v1567H4194v2056h1806v4969c362 57 733 86 1111 86s749-30 1111-86V9168z",
                                "fill" => "#fff",
                              ],
                              "tagName" => "path",
                            ],
                          ],
                          "tagName" => "svg",
                        ],
                        "tagName" => "a",
                      ],
                      [
                        "attribute" => [
                          "class" => "a2 c34e3a",
                        ],
                        "children" => [
                          "attribute" => [
                            "height" => "28",
                            "viewBox" => "0 0 24 24",
                            "width" => "28",
                          ],
                          "children" => [
                            "attribute" => [
                              "d" => "M3 9c-1.65 0-3 1.35-3 3s1.35 3 3 3 3-1.35 3-3-1.35-3-3-3Zm18 0c-1.65 0-3 1.35-3 3s1.35 3 3 3 3-1.35 3-3-1.35-3-3-3Zm-9 0c-1.65 0-3 1.35-3 3s1.35 3 3 3 3-1.35 3-3-1.35-3-3-3Z",
                              "fill" => "currentColor",
                            ],
                            "tagName" => "path",
                          ],
                          "tagName" => "svg",
                        ],
                        "tagName" => "a",
                      ],
                    ],
                    "tagName" => "div",
                  ],
                ],
                "tagName" => "div",
              ],


            ],
            "tagName" => "header",
          ],
          !$publish || $expires_soon ? [
            "attribute" => [
              "class" => "c32 c34a",
            ],
            "children" => !$publish ? "この案件は掲載終了となりました。" : "もうすぐ掲載期限切れです。延長する場合は「編集」から設定して下さい。",
            "tagName" => "div",
          ] : null,
          !$publish && $cover ? [
            "attribute" => [
              "class" => "c43c1",
              "id" => "g",
            ],
            "children" => self::createPhoto("c43g", 600, 450, "-w600a43", $cover),
            "tagName" => "section",
          ] : null,
          $publish && $photos ? [
            "children" => [
              [
                "attribute" => [
                  "class" => "c2",
                  "id" => "a",
                ],
                "children" => "写真",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c43c" . min(3, count($photos)),
                ],
                "children" => array_map(fn (array $pair) => self::createPhoto("c43g", 600, 450, "-w600a43", ...$pair), $photos),
                "tagName" => "div",
              ],
            ],
            "tagName" => "section",
          ] : null,
          $publish && $videos ? [
            "children" => [
              [
                "attribute" => [
                  "class" => "c2",
                  "id" => "b",
                ],
                "children" => "動画",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c44c" . count($videos),
                ],
                "children" => array_map(fn (string $src) => self::createVideo("c44a", "c44a1", 320, 180, "-w600a169", $src), $videos),
                "tagName" => "div",
              ],
            ],
            "tagName" => "section",
          ] : null,
          [
            "children" => [
              [
                "attribute" => [
                  "class" => "c2",
                  "id" => "c",
                ],
                "children" => "詳細",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c34d",
                ],
                "children" => array_map(fn (array $entry) => [
                  "attribute" => [
                    "class" => "c34d1",
                  ],
                  "children" => [
                    [
                      "attribute" => [
                        "class" => "c34d1a",
                      ],
                      "children" => $entry[0],
                      "tagName" => "h3",
                    ],
                    [
                      "attribute" => [
                        "class" => "c34d2b c34d2b" . $entry[2],
                      ],
                      "children" => $entry[1],
                      "tagName" => "div",
                    ],
                  ],
                  "tagName" => "div",
                ], array_filter([
                  ["ID", $case_data["id"], 1,],
                  ["用件", [
                    "attribute" => [
                      "class" => "c34d3 l" . $case_data["matter"],
                    ],
                    "children" => Matter::$data[$case_data["matter"]]["title"],
                    "tagName" => "span",
                  ], 1,],
                  99 !== $case_data["animal"] ? ["動物", Animal::$data[$case_data["animal"]]["title"], 1,] : null,
                  is_string($head["pet"] ?? null) ? ["名前", $head["pet"], 1,] : null,
                  $publish && is_int($body["sex"] ?? null) ? ["性別", ["不明", "オス", "メス",][$body["sex"]], 1,] : null,
                  $publish && is_string($body["age"] ?? null) ? ["年齢", $body["age"], 1,] : null,
                  ["場所", Prefecture::$data[$case_data["prefecture"]]["title"] . ($publish ? " " . $head["location"] : ""), 2,],
                  ["発生日", date("Y年n月j日", $starts_at) . "(" . ["日", "月", "火", "水", "木", "金", "土",][date("w", $starts_at)] . ")", 1,],
                  !$publish ? ["終了日", date("Y年n月j日", $ends_at) . "(" . ["日", "月", "火", "水", "木", "金", "土",][date("w", $ends_at)] . ")", 1,] : null,
                  $expires_at ? ["掲載期限", date("Y年n月j日", $expires_at) . "(" . ["日", "月", "火", "水", "木", "金", "土",][date("w", $expires_at)] . ")", 1,] : null,
                  [1 === $case_data["matter"] ? "飼い主" : (2 === $case_data["matter"] ? "保護者" : (3 === $case_data["matter"] ? "目撃者" : "発見者")), is_string($body["author"] ?? null) ? $body["author"] : "名無し", 1,],
                  $publish && is_string($body["contact"] ?? null) ? ["連絡先", Json2Node::autolink($body["contact"]), 1,] : null,
                ], fn (array | null $entry) => $entry)),
                "tagName" => "div",
              ],
              $description ? [
                "attribute" => [
                  "class" => "c34d4",
                ],
                "children" => Json2Node::autolink($description),
                "tagName" => "p",
              ] : null,
            ],
            "tagName" => "section",
          ],
          $publish ? [
            "children" => [
              [
                "attribute" => [
                  "class" => "c2",
                  "id" => "d",
                ],
                "children" => [
                  "掲示板",
                  [
                    "attribute" => [
                      "class" => "c42h",
                    ],
                    "children" => "情報提供はこちら",
                    "tagName" => "span",
                  ],
                ],
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c34f",
                ],
                "children" => [
                  $has_email ? [
                    "attribute" => [
                      "class" => "c34a c39",
                    ],
                    "children" => "書き込んだコメントは、オーナーのメールに通知されます。",
                    "tagName" => "div",
                  ] : null,
                  [
                    "attribute" => [
                      "class" => "c42f",
                      "role" => "button",
                    ],
                    "children" => "コメントを投稿する",
                    "tagName" => "a",
                  ],
                  [
                    "attribute" => [
                      "class" => "c4w",
                    ],
                    "children" => [
                      "attribute" => [
                        "class" => "a3 c4 ht1",
                        "disabled" => true,
                      ],
                      "children" => "投稿する",
                      "tagName" => "button",
                    ],
                    "tagName" => "div",
                  ],
                ],
                "tagName" => "div",
              ],
              $comment_data_set ? [
                "attribute" => [
                  "class" => "c42",
                ],
                "children" => [
                  [
                    "attribute" => [
                      "class" => "c42a",
                    ],
                    "children" => number_format(count($comment_data_set)) . "件のコメント",
                    "tagName" => "h3",
                  ],
                  [
                    "attribute" => [
                      "class" => "c42b",
                    ],
                    "children" => array_map(fn (int $id) => self::createComment($id, $comment_data_set, $body), [...array_unique(array_map(fn (array $item) => $item["parent"] ? $item["parent"] : $item["id"], $comment_data_set))]),
                    "tagName" => "div",
                  ],
                ],
                "tagName" => "div",
              ] : [
                "attribute" => [
                  "class" => "c42e",
                ],
                "children" => [
                  "まだ、コメントはありません。",
                  [
                    "tagName" => "br",
                  ],
                  "みなさまからの情報提供をお待ちしています。",
                ],
                "tagName" => "div",
              ],
            ],
            "tagName" => "section",
          ] : null,
          [
            "children" => [
              [
                "attribute" => [
                  "class" => "c2",
                  "id" => "e",
                ],
                "children" => "新着の案件",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c26",
                ],
                "children" => array_map(fn (array $item) => Cases::createCard($item), $pk_items[0]),
                "tagName" => "div",
              ],
              [
                "attribute" => [
                  "class" => "c4w",
                ],
                "children" => [
                  "attribute" => [
                    "class" => "a3 c4 c34l ht1",
                    "href" => "/search/",
                  ],
                  "children" => "すべて見る",
                  "tagName" => "button",
                ],
                "tagName" => "div",
              ],
            ],
            "tagName" => "section",
          ],
          [
            "children" => [
              [
                "attribute" => [
                  "class" => "c2",
                  "id" => "f",
                ],
                "children" => "ピックアップ",
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "c26",
                ],
                "children" => array_map(fn (array $item) => Cases::createCard($item), $pk_items[1]),
                "tagName" => "div",
              ],
              [
                "attribute" => [
                  "class" => "c4w",
                ],
                "children" => [
                  "attribute" => [
                    "class" => "a3 c4 c34l ht1",
                    "href" => "/search/",
                  ],
                  "children" => "すべて見る",
                  "tagName" => "button",
                ],
                "tagName" => "div",
              ],
            ],
            "tagName" => "section",
          ],
        ],
        "tagName" => "article",
      ],
      "tagName" => "main",
    ];
  }

  static private function createComment(int $id, array $comment_data_set, array $case_body): array
  {
    $thread_comments = [];

    foreach ($comment_data_set as $comment_data) {
      if ($id === $comment_data["id"] || $id === $comment_data["parent"]) {
        $thread_comments[] = $comment_data;
      }
    }

    array_multisort(array_column($thread_comments, "id"), SORT_ASC, $thread_comments);

    $children = [];
    $thread_title = null;
    $is_private = null;

    foreach ($thread_comments as $comment) {
      $is_thread = $id === $comment["id"];
      $is_private = $comment["private"];
      $head = $comment["head"];
      $body = $is_private ? [] : $comment["body"];
      if ($is_thread) $thread_title = $head["title"];

      $photos = $body["photos"] ?? [];
      $videos = $body["videos"] ?? [];
      $owner = $head["owner"] ?? false;
      $author = $owner ? ($case_body["author"] ?? "名無し") : $head["author"] ?? "名無し";

      if ($is_thread || !$is_private) {
        $children[] = [
          "attribute" => [
            "class" => "c42t",
          ],
          "children" => [
            [
              "attribute" => [
                "class" => "c42d" . ($is_thread ? "" : " c42m"),
              ],
              "children" => [
                [
                  "attribute" => [
                    "class" => "c42d1",
                  ],
                  "children" => [
                    [
                      "attribute" => [
                        "class" => "c42d1a",
                      ],
                      "children" => $author,
                      "tagName" => "span",
                    ],
                    [
                      "attribute" => [
                        "class" => "c42d1b c42d1b" . ($owner ? "o" : "g"),
                      ],
                      "children" => $owner ? "オーナー" : [
                        "ゲスト",
                        [
                          "attribute" => [
                            "class" => "c42d1b1",
                          ],
                          "children" => "",
                          "tagName" => "span",
                        ],
                      ],
                      "tagName" => "span",
                    ],
                  ],
                  "tagName" => "div",
                ],
              ],
              "tagName" => "div",
            ],
            [
              "attribute" => [
                "class" => "c42y" . ($is_thread ? "" : " c42m"),
              ],
              "children" => $is_private ? [
                "attribute" => [
                  "class" => "c42yp",
                ],
                "children" => [
                  [
                    "attribute" => [
                      "class" => "c42yp2",
                      "height" => "40",
                      "viewBox" => "0 0 48 48",
                      "width" => "40",
                    ],
                    "children" => [
                      "attribute" => [
                        "d" => "M9.143 48q-1.429 0-2.429-1-1-1-1-2.429v-24.8q0-1.428 1-2.428t2.429-1h4v-5.486q0-4.514 3.171-7.686Q19.486 0 24 0q4.514 0 7.686 3.171 3.171 3.172 3.171 7.686v5.486h4q1.429 0 2.429 1 1 1 1 2.428v24.8q0 1.429-1 2.429-1 1-2.429 1H9.143Zm0-3.429h29.714v-24.8H9.143v24.8Zm14.857-8q1.829 0 3.114-1.257 1.286-1.257 1.286-3.028 0-1.715-1.286-3.115-1.285-1.4-3.114-1.4t-3.114 1.4q-1.286 1.4-1.286 3.115 0 1.771 1.286 3.028 1.285 1.257 3.114 1.257Zm-7.429-20.228h14.858v-5.486q0-3.086-2.172-5.257Q27.086 3.429 24 3.429T18.743 5.6q-2.172 2.171-2.172 5.257v5.486ZM9.143 44.571v-24.8 24.8Z",
                        "fill" => "currentColor",
                      ],
                      "tagName" => "path",
                    ],
                    "tagName" => "svg",
                  ],
                  [
                    "attribute" => [
                      "class" => "c42yp2",
                    ],
                    "children" => "オーナーか、コメントした本人だけが閲覧できます。",
                    "tagName" => "span",
                  ],
                  [
                    "attribute" => [
                      "class" => "a3 c42yp1 ht2",
                    ],
                    "children" => "見る",
                    "tagName" => "a",
                  ],
                ],
                "tagName" => "div",
              ] : Json2Node::autolink($body["description"]),
              "tagName" => "div",
            ],
            ($photos || $videos) ? [
              "attribute" => [
                "class" => "c42n" . ($is_thread ? "" : " c42m"),
              ],
              "children" => [
                ...($photos ? array_map(fn (string $src) => self::createPhoto("c42n1", 100, 100, "-w300a11", $src), $photos) : []),
                ...($videos ? array_map(fn (string $src) => self::createVideo("c42n2 c44a", "c42n1", 100, 100, "-w300a11", $src), $videos) : []),
              ],
              "tagName" => "div",
            ] : null,
            [
              "attribute" => [
                "class" => "c42o" . ($is_thread ? "" : " c42m"),
              ],
              "children" => [
                [
                  "attribute" => [
                    "class" => "c42o1",
                  ],
                  "children" => date("Y/m/d H:i", $comment["created_at"]),
                  "tagName" => "div",
                ],
                !$is_private ? [
                  "attribute" => [
                    "class" => "c42o2",
                  ],
                  "children" => [
                    "attribute" => [
                      "class" => "c42o2a",
                    ],
                    "children" => "削除",
                    "tagName" => "a",
                  ],
                  "tagName" => "div",
                ] : null,
              ],
              "tagName" => "div",
            ],
          ],
          "tagName" => "div",
        ];
      }
    }

    return [
      "attribute" => [
        "class" => "c42b1",
      ],
      "children" => [
        [
          "attribute" => [
            "class" => "c42b1a",
          ],
          "children" => $thread_title,
          "tagName" => "h4",
        ],
        $children,
        !$is_private ? [
          "attribute" => [
            "class" => "c42b1b",
          ],
          "children" => [
            "attribute" => [
              "class" => "c42f",
              "role" => "button",
            ],
            "children" => "このトピックに返信する",
            "tagName" => "a",
          ],
          "tagName" => "div",
        ] : null,
      ],
      "tagName" => "div",
    ];
  }

  static private function createPhoto(string $token, int $width, int $height, string $suffix, string $thumbnail, ?string $original = null): array
  {
    $info = Media::parse($thumbnail);

    return [
      "children" => [
        [
          "attribute" => [
            "srcset" => "/media/" . $info["prefix"] . $suffix . $info["suffix"] . ".avif",
            "type" => "image/avif",
          ],
          "tagName" => "source",
        ],
        [
          "attribute" => [
            "srcset" => "/media/" . $info["prefix"] . $suffix . $info["suffix"] . ".webp",
            "type" => "image/webp",
          ],
          "tagName" => "source",
        ],
        [
          "attribute" => [
            "class" => $token . " c35",
            "decoding" => "async",
            "height" => $height,
            "src" => "/media/" . $info["prefix"] . $suffix . $info["suffix"],
            "width" => $width,
          ] + ($original ? [
            "data-original" => $original,
          ] : []),
          "tagName" => "img",
        ],
      ],
      "tagName" => "picture",
    ];
  }


  static private function createVideo(string $token1, string $token2, int $width, int $height, string $suffix, string $name): array
  {
    $info = Media::parse($name);

    return [
      "attribute" => [
        "class" => $token1 . " c35",
      ],
      "children" => [
        "attribute" => [
          "class" => $token2,
          "height" => $height,
          "poster" => "/media/" . $info["prefix"] . $suffix . ".jpg.webp",
          "preload" => "metadata",
          "width" => $width,
        ],
        "children" => [
          [
            "attribute" => [
              "src" => "/media/" . $info["prefix"] . ".m3u8",
              "type" => "application/x-mpegURL",
            ],
            "tagName" => "source",
          ],
          [
            "attribute" => [
              "src" => "/media/" . $info["prefix"] . ".mp4",
              "type" => "video/mp4",
            ],
            "tagName" => "source",
          ],
        ],
        "tagName" => "video",
      ],
      "tagName" => "div",
    ];
  }
}
