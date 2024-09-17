<?php

namespace Differ\Functions;

function isAssociativeArray(mixed $data): bool
{
    if (!is_array($data)) {
        return false;
    }

    $keys = array_keys($data);
    if ($keys !== range(0, count($data) - 1)) {
        return true;
    }

    return false;
}
