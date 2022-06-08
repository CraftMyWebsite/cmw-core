<?php
/***
 * This is dedicated to all the global const utils for the front devs and more...
 */

use CMW\Model\coreModel;

define("GLOBAL_GET_IP", coreModel::getOptionValue("minecraft_ip"));
define("GLOBAL_GET_NAME", coreModel::getOptionValue("name"));
define("GLOBAL_GET_DESCRIPTION", coreModel::getOptionValue("description"));