<?php

/**
 * opcacheでpreloadするclassの一覧
 */

foreach ([
  'Document',
  'Etag',
  'utils',
] as $name) {
  require __DIR__ . "/{$name}/index.php";
}
