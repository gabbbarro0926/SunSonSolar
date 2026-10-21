<?php

namespace App\Controllers\Api\Admin;

use App\Models\AvailabilityModel;

class AvailabilityController extends AbstractCrudController
{
    protected string $modelClass = AvailabilityModel::class;
    protected string $label      = 'Availability slot';
    protected string $orderBy    = 'day_of_week';
    protected string $orderDir   = 'ASC';
    protected array $filters     = ['day_of_week'];
    protected array $rules       = [
        'day_of_week' => 'required|in_list[0,1,2,3,4,5,6]',
        'start_time' => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]',
        'end_time' => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]',
        'max_bookings' => 'permit_empty|is_natural_no_zero',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];
}
