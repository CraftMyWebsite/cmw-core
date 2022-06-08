<?php
/***
 * This is dedicated to all the global const utils for the front devs and more...
 */

use CMW\Model\coreModel;

define("GET_IP", coreModel::getOptionValue("minecraft_ip"));
define("GET_NAME", coreModel::getOptionValue("name"));
define("GET_DESCRIPTION", coreModel::getOptionValue("description"));