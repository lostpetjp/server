<?php

/**
 * opcacheでpreloadするclassの一覧
 */

foreach ([
  "Animal",
  "Batch",
  "Cases",
  "CaseCount",
  "CaseIndex",
  "CaseVersion",
  "Comment",
  "Discord",
  "Document",
  "DynamoDB",
  "Encode",
  "Etag",
  "Json2Node",
  "Log",
  "Matter",
  "Me",
  "Media",
  "MediaRelation",
  "Prefecture",
  "Queue",
  "RDS",
  "Recaptcha",
  "S3",
  "Search",
  "Session",
  "SES",
  "Secret",
  "Slack",
  "Trip",
  "utils",
] as $name) {
  require __DIR__ . "/{$name}/index.php";
}
