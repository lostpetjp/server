<?php

declare(strict_types=1);

class HTMLDocument
{
  private object $client;

  static public function getClient(string $path, int $mode): object
  {
    $content = "";

    // /terms
    if ("/terms" === $path) {
      require __DIR__ . '/contents/terms/index.php';
      $content = HTMLDocumentTermsContent::class;
      // privacy
    } elseif ("/privacy" === $path) {
      require __DIR__ . '/contents/privacy/index.php';
      $content = HTMLDocumentPrivacyContent::class;
      // fallback
    } else {
      require __DIR__ . '/clients/terms/index.php';
      $content = HTMLDocumentTermsContent::class;
    }


    return new HTMLDocumentClient($content, $mode);
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

    $html =
      '<!DOCTYPE html>'
      . '<html class="t1">'
      .   '<head>'
      .     '<meta charset="UTF-8">'
      .     implode("", array_map(fn (int $id) => '<link as="style" href="/styles/' . $id . '.css?v=' . Config::$version . '" rel="preload">', $this->client->css))
      .     implode("", array_map(fn (int $id) => '<link as="script" crossOrigin="anonymous" href="/scripts/' . $id . '.js?v=' . Config::$version . '" rel="preload">', $this->client->js))
      .     '<link as="style" href="/style.css?v=' . Config::$version . '" rel="preload">'
      .     '<link as="script" href="/script.js?v=' . Config::$version . '" rel="preload">'
      .     Json2Node::create($this->client->head)
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
      .       '(function(){var t=localStorage.getItem("t");("2"===t||"1"!==t&&matchMedia("(prefers-color-scheme:dark)").matches)&&document.documentElement.classList.replace("t1","t2")}());'
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
      .         '<picture>'
      .            '<source srcset="/logo.svg" media="(min-width: 480px)">'
      .            '<img class="d1a1" src="/icon.svg">'
      .         '</picture>'
      .       '</a>'
      .       '<a class="a2 d1b" href="/">サイトに掲載</a>'
      .     '</header>'
      .     '<div class="d2">'
      //.       '<main class="d2a">'
      .         Json2Node::create($this->client->body)  // <main class="d2a"> ... </main>
      //.       '</main>'
      .       '<nav class="d2b">'
      .         '<div>'
      .           '<div>'
      .             '<h5>検索</h5>'
      .             '<ul>'
      .               '<li><a href="/search/lost/">迷子</a></li>'
      .               '<li><a href="/search/find/">保護</a></li>'
      .             '</ul>'
      .           '</div>'
      .           '<div>'
      .             '<h5>お役立ち</h5>'
      .             '<ul>'
      .               '<li><a href="/register">サイトに掲載する</a></li>'
      .               '<li><a href="/poster">ポスターを作成する</a></li>'
      .             '</ul>'
      .           '</div>'
      .           '<div>'
      .             '<h5>サイト案内</h5>'
      .             '<ul>'
      .               '<li><a href="/terms">利用規約</a></li>'
      .               '<li><a href="/privacy">プライバシーポリシー</a></li>'
      .               '<li><a href="/contact">問い合わせ</a></li>'
      .             '</ul>'
      .           '</div>'
      .         '</div>'
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

class HTMLDocumentClient
{
  public bool $noindex = false;
  public int $cache_time = 0;

  public array $template = [];
  public array $content = [];

  public string $pathname = "/";
  public string $search = "";

  public array $head = [];
  public array $body = [];

  public array $css = [];

  public function __construct(string $content, int $mode)
  {
    $template = $content::$template;

    $template::$css = [
      ...$template::$css,
      3, 4, 5, 6,
    ];

    $this->pathname = $content::$pathname;
    $this->search = $content::$search;

    $this->head = [
      ...$this->head,
      [
        "children" => $content::$title,
        "tagName" => "title",
      ],
      [
        "attribute" => [
          "content" => $content::$description,
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
          "content" => "https://" . _DOMAIN_ . $this->pathname . $this->search,
          "property" => "og:url",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "href" => "https://" . $_SERVER['SERVER_NAME'] . $this->pathname . $this->search,
          "rel" => "canonical",
        ],
        "tagName" => "link",
      ],
      [
        "attribute" => [
          "content" => $content::$title,
          "property" => "og:title",
        ],
        "tagName" => "meta",
      ],
      [
        "attribute" => [
          "content" => $content::$description,
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
    ];

    foreach ($content::$head as $entry) {
      $overwrite = false;

      foreach ($this->head as $index => $default_entry) {
        if (
          $entry["tagName"] === $default_entry["tagName"]
          && (
            ("link" === $entry["tagName"] && $entry["attribute"]["rel"] === $default_entry["attribute"]["rel"])
            || ("meta" === $entry["tagName"] && $entry["attribute"]["property"] === $default_entry["attribute"]["property"])
          )
        ) {
          $this->head[$index] = $entry;
          $overwrite = true;
          break;
        }
      }

      if (!$overwrite) {
        $this->head[] = $entry;
      }
    }

    $this->template = [
      "component" => $template::$id,
      "css" => $template::$css,
      "js" => $template::$js,
    ];

    $this->content = [
      "component" => $content::$id,
      "css" => $content::$css,
      "js" => $content::$js,
    ];

    $this->css = [...array_unique([
      ...$template::$css,
      ...$content::$css,
    ])];

    $this->js = [...array_unique([
      $template::$id,
      $content::$id,
      ...$template::$js,
      ...$content::$js,
    ])];

    $content::ready();

    $this->body = $content::create();

    if (1 === $mode) {
      $this->body = $template::create($this->body);
    }
  }
}
