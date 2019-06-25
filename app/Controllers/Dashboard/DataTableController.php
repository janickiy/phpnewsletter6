<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Helper\Ssp;
use App\Helper\StringHelpers;
use App\Models\Templates;
use App\Models\{Attach};

class DataTableController extends Controller
{
    public function action($request, $response)
    {
        if ($request->getParam('action')) {
            switch ($request->getParam('action')) {
                case 'remove_attach':
                    $directory = $this->upload_directory;
                    $attach = Attach::where('id', $request->getParam('id'));
                    $f = $attach->first();
                    if ($f && file_exists($directory . '/' . $f->name)) {
                        unlink($directory . '/' . $f->name);
                    }
                    $attach->delete();

                    break;
            }
        }
    }

    private function getDetails()
    {
        return ['user' => getenv('DB_USERNAME'), 'pass' => getenv('DB_PASSWORD'), 'db' => getenv('DB_DATABASE'), 'host' => getenv('DB_HOST'), 'charset' => getenv('DB_CHARSET')];
    }

    /**
     * @param $request
     * @param $response
     */
    public function getTemplates($request, $response)
    {
        $table = 'templates';
        $primaryKey = 'id';
        $joinQuery = "FROM templates t LEFT JOIN category c ON t.categoryId=c.id";

        $columns = [
            ['db' => 't.id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'c.name', 'dt' => 'category', 'field' => 'category', 'as' => 'category'],
            ['db' => 't.name', 'dt' => 'name', 'formatter' => function ($d, $row) {
                $row['body'] = preg_replace('/(<.*?>)|(&.*?;)/', '', $row['body']);
                return $d . '<br><br>' . StringHelpers::shortText($row['body'], 500);
            }, 'field' => 'name'],
            ['db' => 't.body', 'dt' => 'body', 'field' => 'body'],
            ['db' => 't.prior', 'dt' => 'prior', 'formatter' => function ($d, $row) {
                return Templates::getPrior($d);
            }, 'field' => 'prior'],
            ['db' => 't.created_at', 'dt' => 'created_at', 'field' => 'created_at'],
            ['db' => 't.id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.template.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
        ];

        header('Content-Type: application/json');

        echo json_encode(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns, $joinQuery));
    }

    /**
     * @param $request
     * @param $response
     */
    public function getCategory($request, $response)
    {
        $table = 'category';
        $primaryKey = 'id';
        $joinQuery = "FROM category c LEFT JOIN subscriptions s ON c.id=s.categoryId ";
        $groupBy = 'c.id';

        $columns = [
            ['db' => 'c.name', 'dt' => 'name', 'field' => 'name'],
            ['db' => 'count(s.categoryId)', 'dt' => 'subcount', 'field' => 'subcount', 'as' => 'subcount'],
            ['db' => 'c.id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.category.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
        ];

        header('Content-Type: application/json');

        echo json_encode(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns, $joinQuery, null, $groupBy));
    }

    /**
     * @param $request
     * @param $response
     */
    public function getSubscribers($request, $response)
    {
        $table = 'subscribers';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 'checkbox', 'formatter' => function ($d, $row) {
                return '<input type="checkbox" title="Отметить/Снять отметку" value="' . $d . '" name="activate[]">';
            }, 'field' => 'checkbox', 'as' => 'checkbox'],
            ['db' => 'name', 'dt' => 'name', 'field' => 'name'],
            ['db' => 'active', 'dt' => 'subStatus', 'formatter' => function ($d, $row) {
                return $d;
            }, 'field' => 'subStatus'],
            ['db' => 'email', 'dt' => 'email', 'field' => 'email'],
            ['db' => 'ip', 'dt' => 'ip', 'field' => 'ip'],
            ['db' => 'active', 'dt' => 'active', 'formatter' => function ($d, $row) {
                return $d == 1 ? 'да' : 'нет';
            },  'field' => 'active'],
            ['db' => 'id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.subscribers.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
            ['db' => 'created_at', 'dt' => 'created_at', 'field' => 'created_at']
        ];

        header('Content-Type: application/json');

        echo json_encode(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }

    /**
     * @param $request
     * @param $response
     */
    public function getSettings($request, $response)
    {
        $table = 'settings';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'name', 'dt' => 'name', 'field' => 'name'],
            ['db' => 'description', 'dt' => 'description', 'field' => 'description'],
            ['db' => 'value', 'dt' => 'value', 'field' => 'value'],
            ['db' => 'id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.settings.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
        ];

        header('Content-Type: application/json');

        echo json_encode(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }

    /**
     * @param $request
     * @param $response
     */
    public function getUsers($request, $response)
    {
        $table = 'users';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'name', 'dt' => 'name', 'field' => 'name'],
            ['db' => 'login', 'dt' => 'login', 'field' => 'login'],
            ['db' => 'description', 'dt' => 'description', 'field' => 'description'],
            ['db' => 'role', 'dt' => 'role', 'field' => 'role'],
            ['db' => 'created_at', 'dt' => 'created_at', 'field' => 'created_at'],
            ['db' => 'id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.users.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = $this->auth->user()->id != $d ? '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>' : '';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
        ];

        header('Content-Type: application/json');

        echo json_encode(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }
}