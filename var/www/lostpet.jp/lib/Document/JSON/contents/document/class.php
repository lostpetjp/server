<?php

declare(strict_types=1);

class JSONDocumentDocument
{
  static public int $cache_time = 0;

  static public function create()
  {
    $search = $_GET["search"];  // TODO:

    $html = HTMLDocument::getClient($_GET["pathname"], 2);

    return [
      "status" => true,
      "body" => [
        "pathname" => $html->pathname,
        "search" => $html->search,
        "template" => $html->template,
        "content" => $html->content,
        "head" => $html->head,
        "body" => $html->body,
      ],
    ];
  }
}
