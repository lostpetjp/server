<?php
class HTMLDocumentTemplate
{
}

interface HTMLDocumentTemplateInterface
{
  static public function create(HTMLDocumentClient $client): array;
}
