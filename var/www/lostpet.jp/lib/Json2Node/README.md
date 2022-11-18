# Json2Node
- JSONをHTMLに変換する。
- SPAとSSRを両立するために必要。
- SEOとOpengraphの問題が解消したらSSRは不要。

## examples
1つの要素を表すオブジェクト。

```json
{
  attribute: {
    href: "https://example.com/",
  },
  tagName: "a",
  children: "リンク"
}
```

classを用いた高度な応用例。
```php
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
]);
```