<?php

namespace CMW\Manager\ORM\Attributes;

enum ColumnType
{
    case INT;
    case BOOLEAN;
    case TEXT;
    case DATETIME;
    case FLOAT;
    case BLOB;
    case JSON;
}
