<?php

namespace App\Controllers\Dashboard;

use App\Models\{Log,Templates};
use App\Controllers\Controller;
use Gealtec\Datatables\Datatables;

class DataTableController extends Controller
{
    public function getTemplates($request,$response)
    {
        $row = Templates::query();

        return Datatables::response($row, $request);
    }
}