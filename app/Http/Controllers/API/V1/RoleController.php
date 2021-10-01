<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RoleControllerTrait;
use App\Traits\CRUDActions;

class RoleController extends Controller
{
    use CRUDActions, RoleControllerTrait;
}
