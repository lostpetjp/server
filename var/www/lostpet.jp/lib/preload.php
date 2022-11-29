<?php

/**
 * opcacheでpreloadするclassの一覧
 */

foreach ([
  "Animal",
  "Batch",
  "Cases",
  "CaseCount",
  "Discord",
  "Document",
  "Encode",
  "Etag",
  "Json2Node",
  "Log",
  "Matter",
  "Media",
  "MediaRelation",
  "Prefecture",
  "Queue",
  "RDS",
  "Recaptcha",
  "S3",
  "Search",
  "SES",
  "Secret",
  "utils",
] as $name) {
  require __DIR__ . "/{$name}/index.php";
}
