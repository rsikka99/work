<?php

$this->partial("{$commonPartsBasePath}/docx/titlepage.phtml", $titledata);
$this->partial("{$basePath}/purchasequote.phtml", $data);
$this->partial("{$basePath}/albuquerque/scope-of-work.phtml", $data);
$this->partial("{$basePath}/albuquerque/obligations-and-exclusions.phtml", $data);
$this->partial("{$basePath}/signatures.phtml", $data);
