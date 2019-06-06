<?php

namespace App\Controllers\Dashboard;

use App\Models\{Log,Templates};
use App\Controllers\Controller;
use Gealtec\Datatables\Datatables;

class DataTableController extends Controller
{
    public function getTemplates($request,$response)
    {
        //$row = Templates::get();



        $draw = isset($_POST["draw"]) ? $_POST["draw"] : 1;
        $order = isset($_POST['order']['0']['column']) && $_POST['order']['0']['column'] == 0 ? 'name' : 'body';
        $dir = isset($_POST['order']['0']['dir']) && $_POST['order']['0']['dir'] == "asc" ? "asc" : "desc";


        $start = isset($_POST['start']) ?? $_POST['start'];
        $length = isset($_POST['length']) ?? $_POST['length'];

        $user = new Templates();
        $todos = Templates::count();
        $recordsFiltered = $todos;
        if (isset($_POST["search"]["value"]) && $_POST["search"]["value"]) {
            $users = $user
                ->select('name', 'body')
                ->where('name', 'like', $_POST["search"]["value"] . '%')
                ->orWhere('body', 'like', $_POST["search"]["value"] . '%')
               // ->offset($start)
               // ->take($length)
               ->orderBy($order, $dir)
                ->get();

            $recordsFiltered = $users->count();
        } else {
            $users = $user
                ->select('name', 'body')
              //  ->offset($start)
               // ->take($length)
                ->orderBy($order, $dir)
                ->get();
        }

        $output = array(
            "draw"                    =>     intval($draw),
            "recordsTotal"          =>      intval($todos),
            "recordsFiltered"     =>    intval($recordsFiltered),
            "data"                    =>    $users
        );

        header("Content-type: application/json");
        echo json_encode($output);
    }

}