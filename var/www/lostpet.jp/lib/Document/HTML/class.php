<?php

declare(strict_types=1);

class HTMLDocument
{
  private object $client;

  static public function getClient(string $pathname, int $mode): object
  {
    $content = "";

    if (2 === _STAGE_) {
      require __DIR__ . "/admin.php";
    }

    // sitemap
    if (false !== strpos($pathname, "/sitemap/")) {
      require __DIR__ . '/contents/sitemap/index.php';
    }

    // /terms
    if ("/terms" === $pathname) {
      require __DIR__ . '/contents/terms/index.php';
      $content = HTMLDocumentTermsContent::class;
      // privacy
    } elseif ("/privacy" === $pathname) {
      require __DIR__ . '/contents/privacy/index.php';
      $content = HTMLDocumentPrivacyContent::class;
      // contact
    } elseif ("/contact" === $pathname) {
      require __DIR__ . '/contents/contact/index.php';
      $content = HTMLDocumentContactContent::class;
      // search
    } elseif (0 === strpos($pathname, "/search") || 0 === strpos($pathname, "/case")) {
      require __DIR__ . '/contents/search/index.php';
      $content = HTMLDocumentSearchContent::class;
      // case
    } elseif (preg_match("/\A\/(case\/)?([0-9]+)/", $pathname, $matches)) {
      require __DIR__ . '/contents/case/index.php';
      $content = HTMLDocumentCaseContent::class;
      // fallback
    } else {
      require __DIR__ . '/contents/terms/index.php';
      $content = HTMLDocumentTermsContent::class;
    }

    if (!$content) Document::error(404);

    return new HTMLDocumentClient($content, $pathname, $mode);
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

    $style_bundle_path = '/styles/bundle/' . $this->createStyle() . '.css';

    $html =
      '<!DOCTYPE html>'
      . '<html class="t1">'
      .   '<head>'
      .     '<meta charset="UTF-8">'
      .     implode("", array_map(fn (int $id) => '<link as="script" crossOrigin="anonymous" href="/scripts/' . $id . '.js?v=' . Config::$version . '" rel="preload">', $this->client->js))
      .     '<link as="style" href="/style.css?v=' . Config::$version . '" rel="preload">'
      .     '<link as="style" href="' . $style_bundle_path . '?v=' . Config::$version . '" rel="preload">'
      .     '<link as="script" href="/script.js?v=' . Config::$version . '" rel="preload">'
      .     Json2Node::create($this->client->head)
      .     ($noindex ? '<meta name="robots" content="noindex">' : '')
      .     '<meta content="telephone=no" name="format-detection">'
      .     '<meta content="width=device-width,initial-scale=1.0" name="viewport">'
      .     '<meta content="LOSTPET.JP (????????????????????????????????????)" name="application-name">'
      .     '<meta content="LOSTPET.JP (????????????????????????????????????)" name="apple-mobile-web-app-title">'
      .     '<meta content="yes" name="mobile-web-app-capable">'
      .     '<meta content="yes" name="apple-mobile-web-app-capable">'
      .     '<meta content="#228ae6" name="apple-mobile-web-app-status-bar-style">'
      .     '<meta content="ja_JP" property="og:locale">'
      .     '<meta content="LOSTPET.JP (????????????????????????????????????)" name="og:site_name">'
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
      .     '<link href="' . $style_bundle_path . "?v=" . Config::$version . '" rel="stylesheet">'
      .     '<script>'
      .       '(function(){var t;("2"===(t=localStorage.getItem("t"))||"1"!==t&&matchMedia("(prefers-color-scheme:dark)").matches)&&document.documentElement.classList.replace("t1","t2"),("2"===(t=localStorage.getItem("r"))||"1"!==t&&matchMedia("(prefers-reduced-motion)").matches)&&document.documentElement.classList.add("r2")}());'
      .       'self.a=' . json_encode([
        "document" => [
          "template" => $this->client->template,
          "content" => $this->client->content,
          "pathname" => $this->client->pathname,
          "search" => $this->client->search,
        ] + ($this->client->data ? [
          "data" => $this->client->data,
        ] : []),
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
      .       '<a class="a3 d1b ht1" href="/">??????????????????</a>'
      .     '</header>'
      .     '<div class="d2">'
      .       Json2Node::create($this->client->body)  // <main class="d2a"> ... </main>
      .       '<nav class="d2b">'
      .         '<div class="d2b1">'
      .           '<h2 class="d2b1a">????????????</h2>'
      .           '<div class="d2b1b">'
      .             '<h5 class="d2b1b1">??????</h5>'
      .             '<ul class="d2b1b2">'
      .               '<li><a class="a2 d2b1b2a hb2" href="/search/lost">??????</a></li>'
      .               '<li><a class="a2 d2b1b2a hb2" href="/search/find">??????</a></li>'
      .             '</ul>'
      .           '</div>'
      .           '<div class="d2b1b d2b1c">'
      .             '<h5 class="d2b1b1">????????????</h5>'
      .             '<ul class="d2b1b2">'
      .               '<li><a class="a2 d2b1b2a hb2" href="/register">????????????????????????</a></li>'
      .               '<li><a class="a2 d2b1b2a hb2" href="/poster">???????????????????????????</a></li>'
      .             '</ul>'
      .           '</div>'
      .           '<div class="d2b1b d2b1c">'
      .             '<h5 class="d2b1b1">???????????????</h5>'
      .             '<ul class="d2b1b2">'
      .               '<li><a class="a2 d2b1b2a hb2" href="/terms">????????????</a></li>'
      .               '<li><a class="a2 d2b1b2a hb2" href="/privacy">??????????????????????????????</a></li>'
      .               '<li><a class="a2 d2b1b2a hb2" href="/contact">???????????????</a></li>'
      .             '</ul>'
      .           '</div>'
      .           '<div class="d2b1b d2b1c">'
      .             '<h5 class="d2b1b1">????????????????????????</h5>'
      .             '<ul class="d2b1b2">'
      .               '<li><a class="a2 d2b1b2a d2b1b2b hb2" role="button">??????????????????<svg height="12" viewBox="0 0 24 24" width="12"><path d="M2.484 5.699 12 15.215l9.516-9.516a1.456 1.456 0 0 1 2.058 2.057L13.029 18.301a1.455 1.455 0 0 1-2.058 0L.426 7.756a1.455 1.455 0 0 1 2.058-2.057Z" fill="currentColor"/></svg></a></li>'
      .               '<li><a class="a2 d2b1b2a d2b1b2b hb2" role="button">????????????<svg height="12" viewBox="0 0 24 24" width="12"><path d="M2.484 5.699 12 15.215l9.516-9.516a1.456 1.456 0 0 1 2.058 2.057L13.029 18.301a1.455 1.455 0 0 1-2.058 0L.426 7.756a1.455 1.455 0 0 1 2.058-2.057Z" fill="currentColor"/></svg></a></li>'
      .             '</ul>'
      .           '</div>'
      .         '</div>'
      .       '</nav>'
      .     '</div>'
      .     '<footer class="d3">'
      .     '</footer>'
      .     '<script src="/script.js?v=' . Config::$version . '"></script>'
      .     ($this->client->schema ? '<script type="application/ld+json">' . json_encode($this->client->schema) . '</script>' : '')
      .   '</body>'
      . '</html>';

    if (1024 > strlen($html)) {
      $html = gzencode($html, 4);
      header('content-encoding:gzip');
    }

    header('cache-control:max-age=' . (property_exists($this->client, 'cache_time') ? $this->client->cache_time . ",stale-while-revalidate=" . $this->client->cache_time : 0) . ',public,immutable,stale-if-error=86400');  // ,must-revalidate
    if ($noindex) header('x-robots-tag:noindex');
    header('cross-origin-embedder-policy:require-corp');
    header('cross-origin-opener-policy:same-origin');
    // header('expect-ct:max-age=86400,enforce');
    header('referrer-policy:no-referrer-when-downgrade');

    http_response_code(200);
    header('content-length:' . strlen($html));
    echo $html;

    exit;
  }

  public function createStyle(): string
  {
    $default_css = [];
    $all_css_text = "";

    $style_ids = [
      3,
      ...$this->client->css,
    ];

    $name = implode(":", $style_ids);

    $version = max([
      strtotime(date("Y-m-01 00:00:00")),
      ...array_map(fn (int $id) => filemtime(_DIR_ . "/public_html/styles/{$id}.css"), $style_ids),
    ]);

    $row = RDS::fetch("SELECT * FROM `css` WHERE `name`=? LIMIT 1;", [
      $name,
    ]);

    if ($row && $version === $row["version"]) {
      return ($row["id"] . $version);
    }

    $style_id = $row ? $row["id"] : RDS::insert("INSERT INTO `css` (`name`) VALUES (?);", [
      $name,
    ]);

    $style_map = [
      "global" => "",
      "min360" => "",
      "min480" => "",
      "min600" => "",
      "min768" => "",
      "min1024" => "",
      "min1120" => "",
      "min1280" => "",
      "max359" => "",
      "max479" => "",
      "max599" => "",
      "max767" => "",
      "max1023" => "",
      "max1119" => "",
      "max1279" => "",
      "hover" => "",
      "light" => "",
      "dark" => "",
      "motion" => "",
    ];

    foreach ($style_ids as $id) {
      $css_text = file_get_contents(_DIR_ . "/public_html/styles/{$id}.css");

      $block_positions = [];

      foreach ([
        "@media screen and (min-width:360px){",
        "@media screen and (min-width:480px){",
        "@media screen and (min-width:600px){",
        "@media screen and (min-width:768px){",
        "@media screen and (min-width:1024px){",
        "@media screen and (min-width:1120px){",
        "@media screen and (min-width:1280px){",
        "@media screen and (max-width:359px){",
        "@media screen and (max-width:479px){",
        "@media screen and (max-width:599px){",
        "@media screen and (max-width:767px){",
        "@media screen and (max-width:1023px){",
        "@media screen and (max-width:1119px){",
        "@media screen and (max-width:1279px){",
        "@media (hover:hover) and (prefers-color-scheme:light){",
        "@media (hover:hover) and (prefers-color-scheme:dark){",
        "@media (prefers-color-scheme:light){",
        "@media (prefers-color-scheme:dark){",
        "@media (hover:hover){",
        "@media (prefers-reduced-motion:no-preference){",
      ] as $prefix) {
        $position = strpos($css_text, $prefix);
        if (false !== $position) $block_positions[] = $position;
      }

      sort($block_positions);

      $css_texts = [];

      if ($block_positions) {
        if ($block_positions[0]) $css_texts[] = substr($css_text, 0, $block_positions[0]);

        for ($index = 0, $l = count($block_positions); $l > $index; $index++) {
          $position = $block_positions[$index];
          $slice_options = [$position];
          if (($index + 1) !== $l) $slice_options[] = ($block_positions[1 + $index] - $position);
          $css_texts[] = substr($css_text, ...$slice_options);
        }
      } else {
        $css_texts[] = $css_text;
      }

      foreach ($css_texts as $css_text) {
        if (0 === strpos($css_text, "@")) {
          $char = substr($css_text, 0, 35);
          $char8 = $char[8];

          if ("h" === $char8) {
            if (isset($css_text[47]) && "(" === $char[25]) {
              if ("l" === $css_text[47]) {
                $type = "hover:light";
                $start = 54;
              } else {
                $type = "hover:dark";
                $start = 53;
              }
            } else {
              $type = "hover";
              $start = 21;
            }
          } elseif ("r" === $char[16]) {
            $type = "motion";
            $start = 46;
          } elseif ("p" === $char8) {
            if ("l" === $char[29]) {
              $type = "light";
              $start = 36;
            } else {
              $type = "dark";
              $start = 35;
            }
          } else {
            $is_max = "a" === $char[20];
            $size = (int)substr($char, -6);
            $type = ($is_max ? "max" : "min") . "{$size}";
            $start = (1023 === $size || 1119 === $size || 1120 === $size || 1279 === $size || 1280 === $size || 1024 === $size) ? 37 : 36;
          }
        } else {
          $type = "global";
          $start = 0;
        }

        $style_map["hover" === substr($type, 0, 5) ? "hover" : (in_array($type, ["light", "motion", "dark",], true) ? "global" : $type)] .= ($css_text = ($start ? substr($css_text, $start, -1) : $css_text));

        $default_css[] = [
          "id" => $id,
          "text" => $css_text,
          "type" => $type,
          "position" => [],
        ];
      }
    }

    foreach ([
      "global",
      "min360",
      "min480",
      "min600",
      "min768",
      "min1024",
      "min1120",
      "min1280",
      "max359",
      "max479",
      "max599",
      "max767",
      "max1023",
      "max1119",
      "max1279",
      "hover",
      "light",
      "dark",
      "motion",
    ] as $key) {
      if ($style_map[$key]) {
        $prefix = "hover" === $key ? "@media (hover:hover){" : ("global" === $key ? "" : "@media screen and (" . ("a" === $key[1] ? "max" : "min") . "-width:" . substr($key, 3) . "px){");
        $all_css_text .= $prefix . $style_map[$key] . ($prefix ? "}" : "");
      }
    }

    foreach ($default_css as $index => $entry) {
      $text = $entry["text"];
      $default_css[$index]["position"] = "" === $text ? [0, 0,] : [strpos($all_css_text, $text), strlen($text)];
    }

    $filename = ($style_id . $version);

    S3::putObject(Config::$bucket, "dist/styles/bundle/{$filename}.css", [
      "Body" => $all_css_text,
      "CacheControl" => "max-age=2592000,public,immutable",
      "ContentType" => "text/css;charset=utf-8",
    ]);

    RDS::execute("UPDATE `css` SET `version`=?, `map`=? WHERE `id`=? LIMIT 1;", [
      $version,
      json_encode(array_map(fn (array $entry) => [
        "id" => $entry["id"],
        "position" => $entry["position"],
        "type" => $entry["type"],
      ], $default_css)),
      $style_id,
    ]);

    return $filename;
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
  public array $schema = [];

  // SSR???????????????????????????
  public array $data = [];

  public function __construct(string $content, string $pathname, int $mode)
  {
    $template = $content::$template;

    $template::$css = [
      ...$template::$css,
    ];

    $this->body = $content::create($pathname);
    $this->cache_time = $content::$cache_time;
    $this->pathname = $content::$pathname;
    $this->search = $content::$search;
    $this->schema = $content::$schema;

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
            || ("title" === $entry["tagName"])
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
      ...$content::$css,  // require content after template
    ])];

    $this->js = [...array_unique([
      $template::$id,
      $content::$id,
      ...$template::$js,
      ...$content::$js,
    ])];

    $this->data = $content::$data + $template::$data;

    if (1 === $mode) {
      $this->body = $template::create($this);
    }
  }
}
