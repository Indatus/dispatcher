<?php

return Symfony\CS\Config\Config::create()
    ->fixers(['-yoda_conditions', 'multiline_spaces_before_semicolon', 'ordered_use', 'short_array_syntax'])
    ->finder(Symfony\CS\Finder\DefaultFinder::create()->in(__DIR__));
