<?php
require __DIR__ . '/class.php';

echo implode("\n\n", [
  // <br>
  Json2Node::create([
    "tagName" => "br",
  ]),
  // <a href="https://example.com/">hello</a>
  Json2Node::create([
    'attribute' => [
      'href' => 'https://example.com/',
    ],
    "tagName" => "a",
    'children' => 'hello',
  ]),

  // <a href="https://example.com/">hello <span>my</span> friend!</a>
  Json2Node::create([
    'attribute' => [
      'href' => 'https://example.com/',
    ],
    "tagName" => "a",
    'children' => [
      'hello ',
      [
        'tagName' => 'span',
        'children' => 'my',
      ],
      ' friend!',
    ],
  ])
]);;
