<?php

declare(strict_types=1);

class HTMLDocument
{
  private object $client;

  static public function getClient(string $path, int $mode): object
  {
    // /terms
    if ("/terms" === $path) {
      require __DIR__ . '/clients/terms/index.php';
      return new HTMLDocumentTermsClient($mode);
    }

    require __DIR__ . '/clients/terms/index.php';
    return new HTMLDocumentTermsClient($mode);
  }

  public function create(): void
  {
    $this->client = self::getClient(_PATH_, 1);

    $this->echo();
  }

  public function echo(): void
  {
    Etag::echo();

    $noindex = $this->client->noindex;

    // create head
    $head = [
      [
        "attribute" => [
          "content" => "",
          "property" => "description",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "website",
          "property" => "og:type",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "https://" . _DOMAIN_ . _PATH_,
          "property" => "og:url",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "",
          "property" => "og:title",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "",
          "property" => "og:description",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "/icon.png",
          "property" => "og:image",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "image/png",
          "property" => "og:image:type",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "1000",
          "property" => "og:image:height",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => "1000",
          "property" => "og:image:width",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "href" => "https://" . $_SERVER['SERVER_NAME'] . _PATH_,
          "rel" => "canonical",
        ],
        "tagName" => "link",
      ],
      [
        "children" => "",
        "tagName" => "title",
      ],
    ];

    foreach ($this->client->head as $entry) {
      foreach ($head as $index => $default_entry) {
        $tag_name = $entry["tagName"];

        if ($default_entry["tagName"] === $tag_name) {
          if (
            "meta" === $tag_name && $default_entry["attribute"]["property"] === $entry["attribute"]["property"]
            || "link" === $tag_name && $default_entry["attribute"]["rel"] === $entry["attribute"]["rel"]
            || "title" === $tag_name
          ) {
            $head[$index] = $entry;
            break;
          }
        }
      }
    }

    $html =
      '<!DOCTYPE html>'
      . '<html>'
      .   '<head>'
      .     '<meta charset="UTF-8">'
      .     implode("", array_map(fn (int $id) => '<link as="style" href="/styles/' . $id . '.css?v=' . Config::$version . '" rel="preload">', $this->client->css))
      .     implode("", array_map(fn (int $id) => '<link as="script" crossOrigin="anonymous" href="/scripts/' . $id . '.js?v=' . Config::$version . '" rel="preload">', [
        $this->client->template,
        $this->client->content,
        ...$this->client->js,
      ]))
      .     '<link as="style" href="/style.css?v=' . Config::$version . '" rel="preload">'
      .     '<link as="script" href="/script.js?v=' . Config::$version . '" rel="preload">'
      .     Json2Node::create($head)
      .     ($noindex ? '<meta name="robots" content="noindex">' : '')
      .     '<meta content="telephone=no" name="format-detection">'
      .     '<meta content="width=device-width,initial-scale=1.0" name="viewport">'
      .     '<meta content="LOSTPET.JP (迷子ペットのデータベース)" name="application-name">'
      .     '<meta content="LOSTPET.JP (迷子ペットのデータベース)" name="apple-mobile-web-app-title">'
      .     '<meta content="yes" name="mobile-web-app-capable">'
      .     '<meta content="yes" name="apple-mobile-web-app-capable">'
      .     '<meta content="#228ae6" name="apple-mobile-web-app-status-bar-style">'
      .     '<meta content="ja_JP" property="og:locale">'
      .     '<meta content="LOSTPET.JP (迷子ペットのデータベース)" name="og:site_name">'
      .     '<meta content="' . _DOMAIN_ . '" name="twitter:domain">'
      .     '<meta content="@lostpetjp" name="twitter:site">'
      .     '<meta content="@arayutw" name="twitter:creator">'
      .     '<meta content="summary" name="twitter:card">'
      .     '<meta content="#228ae6" name="msapplication-TileColor">'
      .     '<meta content="#228ae6" name="theme-color">'
      .     '<link href="/humans.txt" rel="author">'
      .     '<link href="/apple-touch-icon.png" rel="apple-touch-icon" sizes="180x180">'
      .     '<link href="/favicon-32x32.png" rel="icon" sizes="32x32" type="image/png">'
      .     '<link href="/favicon-16x16.png" rel="icon" sizes="16x16" type="image/png">'
      .     '<link href="/manifest.webmanifest" rel="manifest">'
      .     '<link color="#228ae6" href="/safari-pinned-tab.svg" rel="mask-icon">'
      .     '<link href="/style.css?v=' . Config::$version . '" rel="stylesheet">'
      .     implode("", array_map(fn (int $id) => '<link href="/styles/' . $id . '.css?v=' . Config::$version . '" rel="stylesheet">', $this->client->css))
      .     '<script>'
      .       '(function(){var t=localStorage.getItem("t");("2"===t||"1"!==t&&matchMedia("(prefers-color-scheme:dark)").matches)&&document.documentElement.classList.replace("t1","t2")});'
      .       'self.a=' . json_encode([
        "document" => [
          "template" => $this->client->template,
          "content" => $this->client->content,
          "pathname" => $this->client->pathname,
          "search" => $this->client->search,
        ],
        "version" => 0,
      ])
      .     ';document.currentScript.remove()</script>'
      .   '</head>'
      .   '<body>'
      .     '<header class="d1">'
      .       '<a class="d1a" href="/">'
      .         '<svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 380 380">'
      .           '<path fill="#228ae6" d="M0 0h380v380H0z" />'
      .           '<circle fill="#fff" cx="111" cy="142" r="55" />'
      .           '<circle fill="#333" cx="140.5" cy="144.5" r="25.5" />'
      .           '<circle fill="#fff" cx="271" cy="142" r="55" />'
      .           '<circle fill="#333" cx="300.5" cy="144.5" r="25.5" />'
      .         '</svg>'
      .       '</a>'
      .       '<a class="d1b" href="">登録</a>'
      .     '</header>'
      .     '<div class="d2">'
      .       '<main>'
      .         Json2Node::create($this->client->body)
      .       '</main>'
      .       '<nav>'
      .       '</nav>'
      .     '</div>'
      .     '<footer class="d3">'
      .     '</footer>'
      .     '<script src="/script.js?v=' . Config::$version . '"></script>'
      .   '</body>'
      . '</html>';

    if (1024 > strlen($html)) {
      $html = gzencode($html, 4);
      header('content-encoding:gzip');
    }

    header('cache-control:max-age=' . (property_exists($this->client, 'cache_time') ? $this->client->cache_time : 0) . ',immutable');
    if ($noindex) header('x-robots-tag:noindex');
    header('cross-origin-embedder-policy:require-corp');
    header('cross-origin-opener-policy:same-origin');
    header('expect-ct:max-age=86400,enforce');
    header('referrer-policy:no-referrer-when-downgrade');

    http_response_code(200);
    header('content-length:' . strlen($html));
    echo $html;

    exit;
  }
}
