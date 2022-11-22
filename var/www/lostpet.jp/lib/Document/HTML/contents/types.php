<?php
interface HTMLDocumentContentInterface
{
  static public function ready(): void;

  static public function create(): array;
}
