<?php

declare (strict_types = 1);

namespace iutnc\onlyfilms\render;

use iutnc\onlyfilms\video\tracks\Episode;

interface Renderer {
    public const COMPACT = 1;
    public const LONG = 2;

    public function render(int $selector) : string;
}