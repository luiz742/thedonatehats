<?php

use Spatie\ShortSchedule\ShortSchedule;

return function (ShortSchedule $shortSchedule) {
    $shortSchedule->command('donations:check')->everySeconds(30);
};
