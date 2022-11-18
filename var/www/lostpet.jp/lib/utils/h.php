<?php
function h($str = "")
{
  return htmlspecialchars((string)$str, ENT_QUOTES);
}
