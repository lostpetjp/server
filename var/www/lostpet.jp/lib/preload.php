<?php

/**
 * opcacheでpreloadするclassの一覧
 */

foreach ([
  'Discord',
  'Document',
  'Etag',
  'Json2Node',
  'RDS',
  'Secret',
  'utils',
] as $name) {
  require __DIR__ . "/{$name}/index.php";
}
