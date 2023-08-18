<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types\Join;

enum JoinType
{
    case INNER;
    case LEFT;
    case RIGHT;
    case FULL;
}
