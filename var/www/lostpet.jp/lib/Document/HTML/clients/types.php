<?php
class HTMLDocumentClient
{
  public bool $noindex = false;
  public int $cache_time = 0;

  public int $template = 0;
  public int $content = 0;

  public string $pathname = "/";
  public string $search = "";

  public array $css = [];
  public array $js = [];

  public array $head = [];
  public array $body = [];
}
