<?php

declare(strict_types=1);

/**
 * JSONをHTMLに変換するclass
 * - サーバー側とクライアント側で同じJSONからHTMLを作成できるようにする目的。
 * - HTML文字列を返すのに名前が`Json2Node`なのは、JavaScriptのclassと統一するため。
 */
class Json2Node
{
  /**
   * - HTMLのまとまりを作成する。
   * - 文字列を返すことが保障されている。
   */
  static public function create(mixed $options): string
  {
    if (null === $options) {
      return '';
    } elseif (is_int($options['id' ?? null])) {
      foreach (self::$extensions as $extension) {
        if ($extension->id === $options['id']) {
          return $options = self::convertTo($extension->convertTo($options));
        }
      }

      return '';
    } elseif (is_string($options['tagName'] ?? null)) {
      return self::convertTo($options);
    } elseif (is_iterable($options)) {
      $html = '';

      foreach ($options as $option) {
        if (null !== $option) {
          $html .= self::create($option);
        }
      }

      return $html;
    }
    return (string)$options;
  }

  /**
   * JSONから個々の要素を作成する。
   * 
   * {
   *  attribute?: {属性名: 値}
   *  tagName: string
   *  children?: <tag>`self::create(children)`</tag>
   * }
   */
  static private function convertTo(array $options): string
  {
    $tag_name = $options['tagName'] ?? null;
    $attributes = $options['attribute'] ?? null;
    $children = $options['children'] ?? null;
    $content = null !== $children ? self::create($children) : '';

    $tokens = [];

    if ($attributes) {
      foreach ($attributes as $name => $value) {
        if (null !== $value && ('class' !== $name || $value)) {
          $tokens[] = $name . (true !== $value ? "=\"{$value}\"" : '');
        }
      }

      sort($tokens);
    }

    /**
     * 閉じタグを省略できる一覧は、必要に応じて追加する。
     */
    return '<' . $tag_name . ($tokens ? " " . implode(" ", $tokens) : "") . '>' . (in_array($tag_name, [
      'br',
      'img',
      'input',
      'link',
      'meta',
      'path',
      'source',
    ], true) ? "" : ($content . '</' . $tag_name . '>'));
  }
}
