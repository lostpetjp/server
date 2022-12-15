<?php
class HTMLDocumentDocTemplate implements HTMLDocumentTemplateInterface
{
  static public int $id = 1;

  static public array $css = [
    1, 9,
    7,
  ];

  static public array $js = [];
  static public array $data = [];

  static public function create(HTMLDocumentClient $client): array
  {
    $pathname = $client->pathname;
    $object = $client->body;

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
            "children" => [
              [
                "attribute" => [
                  "class" => "c1",
                ],
                "children" => $object["title"],
                "tagName" => "h1",
              ],
              [
                "children" => $object["description"],
                "tagName" => "p",
              ],
            ],
            "tagName" => "header",
          ],
          $object["body"],
        ],
        "tagName" => "article",
      ],
      "tagName" => "main",
    ];
  }
}
