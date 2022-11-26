<?php

/**
 * opcacheでpreloadするclassの一覧
 */

foreach ([
  "Batch",
  "Cases",
  "Discord",
  "Document",
  "Encode",
  "Etag",
  "Json2Node",
  "Log",
  "Media",
  "MediaRelation",
  "Queue",
  "RDS",
  "Recaptcha",
  "S3",
  "SES",
  "Secret",
  "utils",
] as $name) {
  require __DIR__ . "/{$name}/index.php";
}
