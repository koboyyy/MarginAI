<?php

namespace App\Enums;

enum PricingMethod: string
{
    case Markup = 'markup';

    case Margin = 'margin';
}
