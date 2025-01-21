<?php

namespace Biblioverse\TypesenseBundle\Type;

enum FacetStrategyEnum: string
{
    case AUTOMATIC = 'automatic';
    case EXHAUSTIVE = 'exhaustive';
    case TOP_VALUES = 'top_values';
}
