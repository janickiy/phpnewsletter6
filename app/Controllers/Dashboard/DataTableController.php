<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Helper\{StringHelpers, Ssp};
use App\Models\{Attach, Templates};

class DataTableController extends Controller
{
    /**
     * @param $request
     * @param $response
     */
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

    /**
     * @return array
     */
    private function getDetails()
    {
        return ['user' => getenv('DB_USERNAME'), 'pass' => getenv('DB_PASSWORD'), 'db' => getenv('DB_DATABASE'), 'host' => getenv('DB_HOST'), 'charset' => getenv('DB_CHARSET')];
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function getTemplates($request, $response)
    {
        $table = 'templates';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'name', 'dt' => 'name', 'formatter' => function ($d, $row) {
                $row['body'] = preg_replace('/(<.*?>)|(&.*?;)/', '', $row['body']);
                return $d . '<br><br>' . StringHelpers::shortText($row['body'], 500);
            }, 'field' => 'name'],
            ['db' => 'body', 'dt' => 'body', 'field' => 'body'],
            ['db' => 'prior', 'dt' => 'prior', 'formatter' => function ($d, $row) {
                return Templates::getPrior($d);
            }, 'field' => 'prior'],
            ['db' => 'id', 'dt' => 'attachment', 'formatter' => function ($d, $row) {
                return Attach::where('templateId', $d)->count() > 0 ? StringHelpers::trans('str.yes') : StringHelpers::trans('str.no');
            }, 'field' => 'attachment', 'as' => 'attachment'],
            ['db' => 'created_at', 'dt' => 'created_at', 'field' => 'created_at'],
            ['db' => 'id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.template.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
        ];

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function getCategory($request, $response)
    {
        $table = 'category';
        $primaryKey = 'id';
        $joinQuery = "FROM category c LEFT JOIN subscriptions s ON c.id=s.categoryId ";
        $groupBy = 'c.id';

        $columns = [
            ['db' => 'c.id', 'dt' => 'id', 'field' => 'id'],
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

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns, $joinQuery, null, $groupBy));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function getSubscribers($request, $response)
    {
        $table = 'subscribers';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 'checkbox', 'formatter' => function ($d, $row) {
                return '<input type="checkbox" title="Отметить/Снять отметку" value="' . $d . '" name="activate[]">';
            }, 'field' => 'checkbox', 'as' => 'checkbox'],
            ['db' => 'id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'name', 'dt' => 'name', 'field' => 'name'],
            ['db' => 'active', 'dt' => 'subStatus', 'formatter' => function ($d, $row) {
                return $d;
            }, 'field' => 'subStatus'],
            ['db' => 'email', 'dt' => 'email', 'field' => 'email'],
            ['db' => 'ip', 'dt' => 'ip', 'field' => 'ip'],
            ['db' => 'active', 'dt' => 'active', 'formatter' => function ($d, $row) {
                return $d == 1 ? 'да' : 'нет';
            }, 'field' => 'active'],
            ['db' => 'id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.subscribers.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
            ['db' => 'created_at', 'dt' => 'created_at', 'field' => 'created_at']
        ];

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
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

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));

    }

    /**
     * @param $request
     * @param $response
     * @return mixed
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

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function getSmtp($request, $response)
    {
        $table = 'smtp';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 'checkbox', 'formatter' => function ($d, $row) {
                return '<input type="checkbox" value="' . $d . '" name="activate[]">';
            }, 'field' => 'checkbox', 'as' => 'checkbox'],
            ['db' => 'id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'host', 'dt' => 'host', 'field' => 'host'],
            ['db' => 'email', 'dt' => 'email', 'field' => 'email'],
            ['db' => 'username', 'dt' => 'username', 'field' => 'username'],
            ['db' => 'port', 'dt' => 'port', 'field' => 'port'],
            ['db' => 'authentication', 'dt' => 'authentication', 'field' => 'authentication'],
            ['db' => 'secure', 'dt' => 'secure', 'field' => 'secure'],
            ['db' => 'timeout', 'dt' => 'timeout', 'field' => 'timeout'],
            ['db' => 'active', 'dt' => 'activeStatus', 'formatter' => function ($d, $row) {
                return $d;
            }, 'field' => 'activeStatus'],
            ['db' => 'active', 'dt' => 'active', 'formatter' => function ($d, $row) {
                return $d == 1 ? 'да' : 'нет';
            }, 'field' => 'active'],
            ['db' => 'id', 'dt' => 'action', 'formatter' => function ($d, $row) {
                $editBtn = '<a title="Редактировать" class="btn btn-xs btn-primary"  href="' . $this->router->pathFor('admin.smtp.edit', ['id' => $d]) . '"><span  class="fa fa-edit"></span></a> &nbsp;';
                $deleteBtn = '<a class="btn btn-xs btn-danger deleteRow" id="' . $d . '"><span class="fa fa-remove"></span></a>';
                return $editBtn . $deleteBtn;
            },
                'field' => 'action', 'as' => 'action'
            ],
            ['db' => 'created_at', 'dt' => 'created_at', 'field' => 'created_at']
        ];

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function getLog($request, $response)
    {
        $table = 'schedule';
        $primaryKey = 'id';
        $joinQuery = "FROM schedule s INNER JOIN ready_sent r ON s.id=r.scheduleId";
        $groupBy = 's.id';

        $columns = [
            ['db' => 's.id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 's.date', 'dt' => 'date', 'field' => 'date'],
            ['db' => 'count(r.id)', 'dt' => 'count', 'formatter' => function ($d, $row) {
                return '<a href="' . $this->router->pathFor('admin.log.info', ['id' => $d]) . '">' . $row['count'] . '</a>';
            }, 'field' => 'count', 'as' => 'count'],
            ['db' => 'sum(r.success=1)', 'dt' => 'sent', 'field' => 'sent', 'as' => 'sent'],
            ['db' => 'sum(r.readMail=1)', 'dt' => 'read_mail', 'field' => 'read_mail', 'as' => 'read_mail'],
            ['db' => 'r.id', 'dt' => 'unsent', 'formatter' => function ($d, $row) {
                return $row['count'] - $row['sent'];
            }, 'field' => 'unsent', 'as' => 'unsent'],
            ['db' => 'r.id', 'dt' => 'report', 'formatter' => function ($d, $row) {
                return '<a href="' . $this->router->pathFor('admin.log.report', ['id' => $d]) . '">скачать</a>';
            },
                'field' => 'report', 'as' => 'report'
            ],
        ];

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns, $joinQuery, null, $groupBy));
    }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
    public function getInfoLog($request, $response, $id)
    {
        $table = 'ready_sent';
        $primaryKey = 'id';

        $columns = [
            ['db' => 'id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'success', 'dt' => 'success', 'field' => 'success'],
            ['db' => 'template', 'dt' => 'template', 'field' => 'template'],
            ['db' => 'email', 'dt' => 'email', 'field' => 'email'],
            ['db' => 'created_at', 'dt' => 'created_at', 'field' => 'created_at'],
            ['db' => 'success', 'dt' => 'status', 'formatter' => function ($d, $row) {
                return $row['success'] == 1 ? 'отправлено' : 'не отправлено';
            }, 'field' => 'status', 'as' => 'status'],
            ['db' => 'readMail', 'dt' => 'readMail', 'formatter' => function ($d, $row) {
                return $row['success'] == 1 ? 'прочитано' : 'не прочитано';
            }, 'field' => 'readMail'],
            ['db' => 'errorMsg', 'dt' => 'errorMsg', 'field' => 'errorMsg'],
        ];

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function getRedirectLog($request, $response)
    {
        $table = 'redirect_log';
        $primaryKey = 'id';
        $groupBy = 'url';
        $extraSelect = 'count(email) as num';

        $columns = [
            ['db' => 'id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'url', 'dt' => 'url', 'field' => 'url'],
            //   ['db' => 'count(email)', 'dt' => 'email', 'field' => 'email', 'as' => 'count'],
            ['db' => 'email', 'dt' => 'num', 'formatter' => function ($d, $row) {
                return '<a href="' . $this->router->pathFor('admin.redirect_log.info', ['url' => $row['url']]) . '">' . $row['num'] . '</a>';
            }, 'field' => 'num'],
            ['db' => 'id', 'dt' => 'report', 'formatter' => function ($d, $row) {
                return '<a href="' . $this->router->pathFor('admin.redirect_log.report', ['url' => $row['url']]) . '">скачать</a>';
            },
                'field' => 'report', 'as' => 'report'
            ],
        ];

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns, null, null, $groupBy, null, $extraSelect));
    }

    /**
     * @param $request
     * @param $response
     * @param $url
     * @return mixed
     */
    public function getInfoRedirectLog($request, $response, $parametr)
    {
        $table = 'redirect_log';
        $primaryKey = 'id';
        $where = "url='" . $parametr['url'] . "'";

        $columns = [
            ['db' => 'id', 'dt' => 'id', 'field' => 'id'],
            ['db' => 'email', 'dt' => 'email', 'field' => 'email'],
            ['db' => 'created_at', 'dt' => 'created_at', 'field' => 'created_at'],
        ];

        return $response->withJson(Ssp::simple($request->getParams(), $this->getDetails(), $table, $primaryKey, $columns, null, $where));
    }
}