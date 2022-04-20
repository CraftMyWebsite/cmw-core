<?php

/// DETECT THE PHP VERSION
///
/// / ! \ CMW Require php 7.4 or higher / ! \
///

$currentPHPVersion = phpversion();

if ($currentPHPVersion <= 7.4){

    include "required.view.php";

    die();
}