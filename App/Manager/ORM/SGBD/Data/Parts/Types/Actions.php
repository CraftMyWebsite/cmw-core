<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types;

enum Actions
{
    case SELECT;
    case INSERT;
    case UPDATE;
    case DELETE;
}
