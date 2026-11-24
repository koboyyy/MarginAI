<?php

namespace App\Enums;

enum LaborRateType: string
{
    case PerHour = 'per_hour';

    case PerDay = 'per_day';

    case PerUnit = 'per_unit';

    case PerBatch = 'per_batch';
}
