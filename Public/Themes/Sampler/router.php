<?php

/*
 * THIS FILE IS JUST AN EXAMPLE / CONCEPT AND NOT WORK !
 */

/**
 * @var $loader \CMW\Manager\Loader\Loader global router
 */

use CMW\Manager\Loader\Loader;

/** @desc Create a simple route "/hello" with the file "hello.view.php" in the package folder "Core" */
Loader::createSimpleRoute('/hello', 'hello', 'CustomRoutes');
