<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class TimelineWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.timeline-widget';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 1;
}
