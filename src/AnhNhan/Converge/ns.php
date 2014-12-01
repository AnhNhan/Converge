<?php

use AnhNhan\Converge\Events\ArrayDataEvent;

function arrayDataEvent(array $data = [])
{
    return new ArrayDataEvent($data);
}
