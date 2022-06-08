<?php

/// DETECT THE PHP VERSION
///
/// / ! \ CMW Require php 8.0 or higher / ! \
///


if (phpversion() <= "8.0"){

    include "required.view.php";

    die();
}