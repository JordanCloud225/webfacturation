<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use App\Twig\Runtime\DateFormatExtensionRuntime;

class DateFormatExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('date_format', [DateFormatExtensionRuntime::class, 'dateFormat']),
        ];
    }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('date_format', [DateFormatExtensionRuntime::class, 'dateFormat']),
            new TwigFunction('stringToDateTime', [DateFormatExtensionRuntime::class, 'stringToDateTime']),
            new TwigFunction('stringToDate', [DateFormatExtensionRuntime::class, 'stringToDate']),
            new TwigFunction('dateDiff', [DateFormatExtensionRuntime::class, 'dateDiff']),
            new TwigFunction('addDaysInDate', [DateFormatExtensionRuntime::class, 'addDaysInDate']),
            new TwigFunction('addMonthInDate', [DateFormatExtensionRuntime::class, 'addMonthInDate']),
            new TwigFunction('addYearInDate', [DateFormatExtensionRuntime::class, 'addYearInDate']),
        ];
    }
}