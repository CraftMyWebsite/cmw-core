<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types\Where;

enum WhereOperator
{
    case EQUALS;
    case NOT_EQUALS;
    case GREATER_THAN;
    case GREATER_THAN_OR_EQUALS;
    case LESS_THAN;
    case LESS_THAN_OR_EQUALS;
    case LIKE;
    case BETWEEN;
    case IN;
    case IS_NULL;
    case IS_NOT_NULL;
}
