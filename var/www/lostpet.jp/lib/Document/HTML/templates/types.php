<?php
class HTMLDocumentTemplate
{
}

interface HTMLDocumentTemplateInterface
{
  static public function create(string $pathname, array $object): array;
}
