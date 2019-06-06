<?php

namespace App\Controllers\Dashboard;

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
        $table = 'templates';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 'id'],
            ['db' => 'name', 'dt' => 'name'],
            ['db' => 'body', 'dt' => 'body'],
            ['db' => 'prior', 'dt' => 'prior'],
            ['db' => 'categoryId', 'dt' => 'categoryId'],
            ['db' => 'created_at', 'dt' => 'created_at'],
            ['db' => 'updated_at', 'dt' => 'updated_at'],
        ];

        header('Content-Type: application/json');
        echo json_encode(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }


}