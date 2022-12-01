<?php
class HTMLDocumentSearchTemplate implements HTMLDocumentTemplateInterface
{
  static public int $id = 14;

  static public array $css = [
    1,
    8,
    9,
    25,
    26,
    27,
  ];

  static public array $js = [];

  static public function create(array $object): array
  {
    $matter_id = $object["matter"];
    $animal_id = $object["animal"];
    $prefecture_id = $object["prefecture"];
    $sort_id = $object["sort"];
    $page_id = $object["page"];
    $items = $object["items"];
    $counts = $object["count"];
    $total_pages = $object["total_pages"];

    $matter = $matter_id ? array_filter(Matter::$data, fn (array $entry) => $entry["search"])[$matter_id] ?? Matter::$data[99] : null;
    $animal = $animal_id ? array_filter(Animal::$data, fn (array $entry) => $entry["search"])[$animal_id] ?? Animal::$data[99] : null;
    $prefecture = $prefecture_id ? array_filter(Prefecture::$data, fn (array $entry) => $entry["search"])[$prefecture_id] ?? Prefecture::$data[99] : null;

    $media_ids = [];

    foreach ($items as $item) {
      $head = $item["head"];
      $photos = $head["photos"] ?? [];

      foreach ($photos as $photo) {
        $media_ids = [...$media_ids, ...$photo,];
      }
    }

    $rows = RDS::fetchAll("SELECT `id`, `name` FROM `media` WHERE `id` IN (" . implode(",", array_fill(0, ($limit = count($media_ids)), "?")) . ") AND `status`=? LIMIT {$limit};", [
      ...$media_ids,
      1,
    ]);

    $media_map = array_combine(array_column($rows, "id"), array_column($rows, "name"));

    return [
      "attribute" => [
        "class" => "d2a",
        "role" => "main",
      ],
      "children" => [
        "attribute" => [
          "class" => "c8 d2a1",
        ],
        "children" => [
          [
            "attribute" => [
              "class" => "c9 c25g",
            ],
            "children" => [
              [
                [
                  "attribute" => [
                    "class" => "c1",
                  ],
                  "children" => $object["title"],
                  "tagName" => "h1",
                ],
              ],
              [
                "attribute" => [
                  "class" => "c25w",
                ],
                "children" => [
                  [
                    "attribute" => [
                      "class" => "c25a",
                    ],
                    "children" => [
                      [
                        "children" => [
                          "attribute" => [
                            "class" => "a2 c25a1a hb3",
                            "role" => "button",
                          ],
                          "children" => [
                            !$matter ? "全状況" : $matter["title"],
                            [
                              "attribute" => [
                                "height" => "12",
                                "viewBox" => "0 0 24 24",
                                "width" => "12",
                              ],
                              "children" => [
                                "attribute" => [
                                  "d" => "M2.484 5.699 12 15.215l9.516-9.516a1.456 1.456 0 0 1 2.058 2.057L13.029 18.301a1.455 1.455 0 0 1-2.058 0L.426 7.756a1.455 1.455 0 0 1 2.058-2.057Z",
                                  "fill" => "currentColor",
                                ],
                                "tagName" => "path",
                              ],
                              "tagName" => "svg",
                            ],
                          ],
                          "tagName" => "a",
                        ],
                        "tagName" => "li",
                      ],
                      [
                        "children" => [
                          "attribute" => [
                            "class" => "a2 c25a1a hb3",
                            "role" => "button",
                          ],
                          "children" => [
                            !$animal ? "全動物" : $animal["title"],
                            [
                              "attribute" => [
                                "height" => "12",
                                "viewBox" => "0 0 24 24",
                                "width" => "12",
                              ],
                              "children" => [
                                "attribute" => [
                                  "d" => "M2.484 5.699 12 15.215l9.516-9.516a1.456 1.456 0 0 1 2.058 2.057L13.029 18.301a1.455 1.455 0 0 1-2.058 0L.426 7.756a1.455 1.455 0 0 1 2.058-2.057Z",
                                  "fill" => "currentColor",
                                ],
                                "tagName" => "path",
                              ],
                              "tagName" => "svg",
                            ],
                          ],
                          "tagName" => "a",
                        ],
                        "tagName" => "li",
                      ],
                      [
                        "children" => [
                          "attribute" => [
                            "class" => "a2 c25a1a hb3",
                            "role" => "button",
                          ],
                          "children" => [
                            !$prefecture ? "全国" : $prefecture["title"],
                            [
                              "attribute" => [
                                "height" => "12",
                                "viewBox" => "0 0 24 24",
                                "width" => "12",
                              ],
                              "children" => [
                                "attribute" => [
                                  "d" => "M2.484 5.699 12 15.215l9.516-9.516a1.456 1.456 0 0 1 2.058 2.057L13.029 18.301a1.455 1.455 0 0 1-2.058 0L.426 7.756a1.455 1.455 0 0 1 2.058-2.057Z",
                                  "fill" => "currentColor",
                                ],
                                "tagName" => "path",
                              ],
                              "tagName" => "svg",
                            ],
                          ],
                          "tagName" => "a",
                        ],
                        "tagName" => "li",
                      ],
                    ],
                    "tagName" => "ul",
                  ],
                  [
                    "attribute" => [
                      "class" => "c25b",
                    ],
                    "children" => [
                      [
                        "attribute" => [
                          "class" => "c25b1",
                          "href" => Search::createUrl([
                            "sort" => 0,
                          ] + $object),
                        ],
                        "children" => [
                          "attribute" => [
                            "class" => "a2 c25b1a c25b1b hb3" . (1 !== $sort_id ? " c25b1s" : ""),
                          ],
                          "children" => "発生順",
                          "tagName" => "a",
                        ],
                        "tagName" => "li",
                      ],
                      [
                        "attribute" => [
                          "class" => "c25b1",
                          "href" => Search::createUrl([
                            "sort" => 1,
                          ] + $object),
                        ],
                        "children" => [
                          "attribute" => [
                            "class" => "a2 c25b1a c25b1c hb3" . (1 === $sort_id ? " c25b1s" : ""),
                          ],
                          "children" => "新着順",
                          "tagName" => "a",
                        ],
                        "tagName" => "li",
                      ],
                    ],
                    "tagName" => "ul",
                  ],
                  $matter_id || $animal_id || $prefecture_id ? [
                    "attribute" => [
                      "class" => "c25c",
                    ],
                    "children" => [
                      "attribute" => [
                        "class" => "a2 c25c1 hb2",
                        "href" => Search::createUrl([
                          "matter" => 0,
                          "animal" => 0,
                          "prefecture" => 0,
                          "page" => 1,
                        ] + $object),
                      ],
                      "children" => [
                        [
                          "attribute" => [
                            "height" => "12",
                            "viewBox" => "0 0 48 48",
                            "width" => "12",
                          ],
                          "children" => [
                            "attribute" => [
                              "d" => "M3.692 48 0 44.308 20.308 24 0 3.692 3.692 0 24 20.308 44.308 0 48 3.692 27.692 24 48 44.308 44.308 48 24 27.692 3.692 48Z",
                              "fill" => "currentColor",
                            ],
                            "tagName" => "path",
                          ],
                          "tagName" => "svg",
                        ],
                        "絞り込みをしない",
                      ],
                      "tagName" => "a",
                    ],
                    "tagName" => "div"
                  ] : null,
                ],
                "tagName" => "div",
              ],
              [
                "attribute" => [
                  "class" => "c25e",
                ],
                "children" => [
                  [
                    "children" => [
                      "attribute" => [
                        "class" => "a2 c25e1a hb2" . (0 === $matter_id ? " c25e1s" : "") . (!$counts[0] ? " c25e1d" : ""),
                        "href" => Search::createUrl([
                          "matter" => 0,
                        ] + $object),
                      ],
                      "children" => [
                        "すべて",
                        [
                          "attribute" => [
                            "class" => "c25e1a1",
                            "title" => $counts[0] . "件",
                          ],
                          "children" => $counts[0],
                          "tagName" => "span",
                        ],
                      ],
                      "tagName" => "a",
                    ],
                    "tagName" => "li",
                  ],
                  [
                    "children" => [
                      "attribute" => [
                        "class" => "a2 c25e1a hb2" . (1 === $matter_id ? " c25e1s" : "") . (!$counts[1] ? " c25e1d" : ""),
                        "href" => Search::createUrl([
                          "matter" => 1,
                        ] + $object),
                      ],
                      "children" => [
                        "迷子",
                        [
                          "attribute" => [
                            "class" => "c25e1a1",
                            "title" => $counts[1] . "件",
                          ],
                          "children" => $counts[1],
                          "tagName" => "span",
                        ],
                      ],
                      "tagName" => "a",
                    ],
                    "tagName" => "li",
                  ],
                  [
                    "children" => [
                      "attribute" => [
                        "class" => "a2 c25e1a hb2" . (2 === $matter_id ? " c25e1s" : "") . (!$counts[2] ? " c25e1d" : ""),
                        "href" => Search::createUrl([
                          "matter" => 2,
                        ] + $object),
                      ],
                      "children" => [
                        "保護、目撃",
                        [
                          "attribute" => [
                            "class" => "c25e1a1",
                            "title" => $counts[2] . "件",
                          ],
                          "children" => $counts[2],
                          "tagName" => "span",
                        ],
                      ],
                      "tagName" => "a",
                    ],
                    "tagName" => "li",
                  ],
                ],
                "tagName" => "ul",
              ],
              [
                "attribute" => [
                  "class" => "c25d",
                ],
                "children" => [
                  [
                    "attribute" => [
                      "class" => "c25d1",
                    ],
                    "children" => $counts[$matter_id],
                    "tagName" => "span",
                  ],
                  "件ヒットしました。",
                  $matter_id || $animal_id || $prefecture_id ? [
                    "(",
                    [
                      "attribute" => [
                        "class" => "a1",
                        "href" => "/search/",
                      ],
                      "children" => "全部見る",
                      "tagName" => "a",
                    ],
                    ")"
                  ] : null,
                ],
                "tagName" => "div",
              ],
            ],
            "tagName" => "header",
          ],
          self::createPager($page_id,  $total_pages),
          [
            "attribute" => [
              "class" => "c25f c26",
            ],
            "children" => array_map(fn (array $item) => self::createItem($item, $media_map), $object["items"]),
            "tagName" => "div",
          ],
          self::createPager($page_id,  $total_pages),
          (string)count($object["items"]),
          // $object["body"],
        ],
        "tagName" => "article",
      ],
      "tagName" => "main",
    ];
  }

  static private function createItem(array $item, array $media_map): array
  {
    $matter_id = $item["matter"];
    $matter = Matter::$data[$matter_id];
    $animal_id = $item["animal"];
    $animal = Animal::$data[$animal_id];
    $prefecture_id = $item["prefecture"];
    $prefecture = Prefecture::$data[$prefecture_id];

    $head = $item["head"];
    $photos = $head["photos"] ?? [];
    $id = $photos[0][0] ?? null;
    $name = $id ? ($media_map[$id] ?? null) : null;
    $info = $name ? Media::parse($name) : null;
    if ($info) $name = $info["prefix"] . "-w600a43" . $info["suffix"];

    return [
      "attribute" => [
        "class" => "c26i",
        "href" => "/" . $item["id"],
      ],
      "children" => [
        "attribute" => [
          "class" => "c26a",
        ],
        "children" => [
          [
            "attribute" => [
              "class" => "o26a1",
            ],
            "children" => [
              [
                "attribute" => [
                  "class" => "o26a1a",
                ],
                "children" => [
                  $head["title"],
                ],
                "tagName" => "h2",
              ],
              [
                "attribute" => [
                  "class" => "o26a1d",
                ],
                "children" => (1 === $matter_id ? (isset($head["pet"]) ? $head["pet"] : "名無し") : (99 === $animal_id ? "その他" : $animal["title"])),
                "tagName" => "span",
              ],
              [
                "attribute" => [
                  "class" => "o26a1b l" . $matter_id,
                ],
                "children" => $matter["title"],
                "tagName" => "div",
              ],
              $info ? [
                "attribute" => [
                  "class" => "o26a1c",
                ],
                "children" => [
                  [
                    "attribute" => [
                      "srcset" => "/media/{$name}.avif",
                      "type" => "image/avif",
                    ],
                    "tagName" => "source",
                  ],
                  [
                    "attribute" => [
                      "srcset" => "/media/{$name}.webp",
                      "type" => "image/webp",
                    ],
                    "tagName" => "source",
                  ],
                  [
                    "attribute" => [
                      "class" => "c26g",
                      "decoding" => "async",
                      "height" => "450",
                      "loading" => "lazy",
                      "src" => "/media/{$name}",
                      "width" => "600",
                    ],
                    "tagName" => "img",
                  ],
                ],
                "tagName" => "picture",
              ] : [
                "attribute" => [
                  "class" => "o26a1c c26g",
                  "decoding" => "async",
                  "loading" => "lazy",
                  "src" => "/icon.svg",
                ],
                "tagName" => "img",
              ],
            ],
            "tagName" => "header",
          ],
          [
            "attribute" => [
              "class" => "o26a2" . ($item["created_at"] > ($_SERVER["REQUEST_TIME"] - 172800) ? " o26a2n" : ""),
            ],
            "children" => [
              [
                "attribute" => [
                  // "class" => "o3a2a",
                ],
                "tagName" => "div",
                "children" => $prefecture["title"] . " " . $head["location"],
              ],
              [
                "attribute" => [
                  "class" => "o26a2b",
                  "datetime" => date(DATE_ISO8601, $item["starts_at"]),
                ],
                "tagName" => "time",
                "children" => date("Y/m/d", $item["starts_at"]),
              ],
            ],
            "tagName" => "section",
          ],
        ],
        "tagName" => "article",
      ],
      "tagName" => "a",
    ];
  }

  static private function createPager(int $page_id, int $total_pages): array
  {
    /*
    <ul class="pager top">
      <li><a class="arrowLeft btn ico neg pager-prev" href="./:2">前ページ</a></li>
      <li><span class="pager-navi after arrowDown icoa on">3 / 184</span></li>
      <li><a class="arrowRight btn icoa neg pager-next" href="./:4">次ページ</a></li>
    </ul>
    */
    return [
      "attribute" => [
        "class" => "c27",
      ],
      "children" => [
        [
          "children" => [
            "attribute" => [
              "class" => "a2 hb2 c27a" . (!($page_id > 1) ? " c27d" : ""),
              "href" => $page_id > 1 ? ("./" . ($page_id - 1)) : null,
            ],
            "children" => [
              [
                "attribute" => [
                  "height" => "12",
                  "viewBox" => "0 0 48 48",
                  "width" => "12",
                ],
                "children" => [
                  "attribute" => [
                    "d" => "m32.959 48-24-24 24-24 4.282 4.282L17.523 24l19.718 19.718L32.959 48Z",
                    "fill" => "currentColor",
                  ],
                  "tagName" => "path",
                ],
                "tagName" => "svg",
              ],
              "前ページ",
            ],
            "tagName" => "a",
          ],
          "tagName" => "li",
        ],
        [
          "children" => [
            "attribute" => [
              "class" => "a2 hb2 c27a" . (2 > $total_pages ? " c27e" : ""),
              "role" => "button",
            ],
            "children" => [
              "{$page_id} / {$total_pages}",
              [
                "attribute" => [
                  "height" => "12",
                  "viewBox" => "0 0 24 24",
                  "width" => "12",
                ],
                "children" => [
                  "attribute" => [
                    "d" => "M2.484 5.699 12 15.215l9.516-9.516a1.456 1.456 0 0 1 2.058 2.057L13.029 18.301a1.455 1.455 0 0 1-2.058 0L.426 7.756a1.455 1.455 0 0 1 2.058-2.057Z",
                    "fill" => "currentColor",
                  ],
                  "tagName" => "path",
                ],
                "tagName" => "svg",
              ],
            ],
            "tagName" => "a",
          ],
          "tagName" => "li",
        ],
        [
          "children" => [
            "attribute" => [
              "class" => "a2 hb2 c27a" . (!($total_pages > $page_id) ? " c27d" : ""),
              "href" => $total_pages > $page_id ? ("./" . ($page_id + 1)) : null,
            ],
            "children" => [
              "次ページ",
              [
                "attribute" => [
                  "height" => "12",
                  "viewBox" => "0 0 48 48",
                  "width" => "12",
                ],
                "children" => [
                  "attribute" => [
                    "d" => "m13.841 48-4.282-4.282L29.277 24 9.559 4.282 13.841 0l24 24-24 24Z",
                    "fill" => "currentColor",
                  ],
                  "tagName" => "path",
                ],
                "tagName" => "svg",
              ],
            ],
            "tagName" => "a",
          ],
          "tagName" => "li",
        ],
      ],
      "tagName" => "ul",
    ];
  }
}
