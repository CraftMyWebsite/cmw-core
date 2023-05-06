<?php

/** @var $loader Loader global router */
use CMW\Utils\Loader;

Loader::createSimpleRoute("/pages/test", "pagedeFou", "pages", weight: 10);