<?php

declare (strict_types = 1);

namespace iutnc\deefy\render;

interface Renderer {
    public const COMPACT = 1;
    public const LONG = 2;

    public function render(int $selector) : string;
}