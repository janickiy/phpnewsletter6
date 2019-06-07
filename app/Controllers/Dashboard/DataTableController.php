<?php

namespace App\Controllers\Dashboard;

use App\Models\Category;
use App\Controllers\Controller;
use App\Helper\Ssp;
use Psr\Http\Message\RequestInterface as Resquest;
use Psr\Http\Message\ResponseInterface as Response;

class DataTableController extends Controller
{
    private function getDetails()
    {
        return ['user' => getenv('DB_USERNAME'), 'pass' => getenv('DB_PASSWORD'), 'db' => getenv('DB_DATABASE'), 'host' => getenv('DB_HOST'), 'charset' => getenv('DB_CHARSET')];
    }

    /**
     * @param Resquest $request
     * @param Response $response
     */
    public function getTemplates(Resquest $request, Response $response)
    {
        global
        $pdoHost, $pdoUser,
        $pdoPass, $pdoDatabase;




        $options = [
            'table' => 'templates',
            'alias' => 't',
            'primaryKey' => 'id',
            'columns' => [
                [ 'db' => 'id',       'dt' => 'id' ],
                [
                    'db' => 'categoryId',
                    'dt' => 'category',
                    'join' => [
                        'table' => 'category',
                        'on' => 'id',
                        'select' => 'name',
                        'alias' => 'c',
                        'as' => 'category'
                    ]
                ],

                [
                    'db' => 'name',
                    'dt' => 'name',

                ],
                [
                    'db' => 'body',
                    'dt' => 'body',

                ],
                [ 'db' => 'prior', 'dt' => 'prior' ],
                [ 'db' => 'created_at', 'dt' => 'created_at' ]
            ]
        ];







        header('Content-Type: application/json');
        echo json_encode(Ssp::process($request->getParams(), $this->getDetails(), $options));
    }


}