<?php

namespace Biblioverse\TypesenseBundle\Type;

enum InfixEnum: string
{
    case OFF = 'off';
    case ALWAYS = 'always';
    case FALLBACK = 'fallback';
}
