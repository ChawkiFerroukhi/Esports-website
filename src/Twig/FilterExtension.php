<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class FilterExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('formatEvent', [$this, 'formatEvent']),
        ];
    }

    public function formatEvent(string $nom): string
    {
        return str_replace(' ','-', $nom);
    }
}