<?php

namespace App\Core\Scout\Specifications;

interface UserSpecification
{
    public function apply($query): void;
}
