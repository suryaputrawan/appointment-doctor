<?php

use App\Models\Hospital;

function dataHospitals()
{
    return Hospital::get();
}
