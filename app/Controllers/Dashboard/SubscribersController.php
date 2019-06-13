<?php

namespace App\Controllers\Dashboard;

use App\Models\{Category, Charset, Subscribers, Subscriptions};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use App\Helper\StringHelpers;
use Janickiy\ConvertCharset\ConvertCharset;

class SubscribersController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function index($request,$response)
   {
       $title = "Подписчики";

       return $this->view->render($response,'dashboard/subscribers/index.twig', compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function create($request,$response)
   {
       $title = "Добавление подписчика";

       $category = Category::get();

       return $this->view->render($response,'dashboard/subscribers/create_edit.twig', compact('title', 'category'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function store($request,$response)
   {
       $validation = $this->validator->validate($request,[
           'email' => ['rules' => v::email()->notEmpty(),'messages' => ['email' => 'Адрес электроной почты указан не верно','notEmpty' => 'Это поле обязательно для заполнения']],
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
       }

       if (Subscribers::where('email','like',$request->getParam('email'))->first()) {
           $_SESSION['errors'] = ['email' => ['unique' => 'Адрес электроной почты уже есть в базе данных! Укажите другой']];

           return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
       }

       $id = Subscribers::create(array_merge($request->getParsedBody(),['active' => 1, 'token' => StringHelpers::token()]))->id;

       if ($request->getParam('categoryId')) {

          foreach ($request->getParam('categoryId') as $categoryId) {
              if (is_numeric($categoryId)) {
                  Subscriptions::create(['subscriberId' => $id, 'categoryId' => $categoryId]);
              }
          }
       }

       $this->flash->addMessage('success','Данные успешно добавлены');

       return $response->withRedirect($this->router->pathFor('admin.subscribers.index'));
   }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
   public function edit($request, $response, $id)
   {
       $title = "Редактирование подписчика";
       $subscriber = Subscribers::where('id', $id)->first();

       if (!$subscriber) return $this->view->render($response, 'errors/404.twig');

       $category = Category::get();

       return $this->view->render($response, 'dashboard/category/create_edit.twig', compact('subscriber', 'title', 'category'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function update($request, $response)
   {
       $validation = $this->validator->validate($request,[
           'email' => ['rules' => v::email()->notEmpty(),'messages' => ['email' => 'Адрес электроной почты указан не верно','notEmpty' => 'Это поле обязательно для заполнения']],
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
       }

       $data['name'] = $request->getParam('name');
       $data['email'] = $request->getParam('email');

       Subscribers::where('id', $request->getParam('id'))->update($data);

       $this->flash->addMessage('success', 'Данные успешно обновлены');

       return $response->withRedirect($this->router->pathFor('admin.category'));
   }

    /**
     * @param $request
     * @param $response
     * @param $id
     */
   public function destroy($request, $response, $id)
   {
       Subscribers::where('id', $id)->delete();
       Subscriptions::where('subscriberId', $id)->delete();
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function import($request, $response)
   {
       $title = "Импорт";
       $charsets = Charset::get();
       $category = Category::get();
       return $this->view->render($response,'dashboard/subscribers/import.twig', compact('title','charsets','category'));
   }

   public function importSubscribers($request, $response)
   {

   }

   public function export()
   {

   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function removeAll($request, $response)
   {
       Subscribers::truncate();
       Subscriptions::truncate();

       $this->flash->addMessage('success', 'Данные успешно удалены');

       return $response->withRedirect($this->router->pathFor('admin.subscribers.index'));
   }

    private function importFromText($id_cat)
    {


        if (!($fp = @fopen($_FILES['file']['tmp_name'], "rb"))) {
            return false;
        } else {
            $buffer = fread($fp, filesize($_FILES['file']['tmp_name']));
            fclose($fp);
            $tok = strtok($buffer, "\n");

            while ($tok) {
                $tok = strtok("\n");
                $strtmp[] = $tok;
            }

            $count = 0;

            foreach ($strtmp as $val) {
                $str = $val;

                if (!mb_check_encoding($str, 'utf-8') && core::getSetting('id_charset')) {
                    $sh = new ConvertCharset(core::getSetting('id_charset'), "utf-8");
                    $str = $sh->Convert($str);
                }

                preg_match('/([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/uis', $str, $out);

                $email = isset($out[0]) ? $out[0] : '';
                $name = str_replace($email, '', $str);
                $email = strtolower($email);
                $name = trim($name);

                if (strlen($name) > 250) {
                    $name = '';
                }

                if ($email) {
                    $query = "SELECT * FROM " . core::database()->getTableName('users') . " WHERE email='" . $email . "'";
                    $result = core::database()->querySQL($query);

                    if (core::database()->getRecordCount($result) > 0) {
                        $row = core::database()->getRow($result);

                        core::database()->delete(core::database()->getTableName('subscription'), "id_user=" . $row['id_user'], '');

                        if ($id_cat) {
                            foreach ($id_cat as $id) {
                                if (is_numeric($id)) {
                                    $fields = [
                                        'id_sub'  => 0,
                                        'id_user' => $row['id_user'],
                                        'id_cat'  => $id
                                    ];

                                    core::database()->insert($fields, core::database()->getTableName('subscription'));
                                }
                            }
                        }
                    } else {
                        $fields = [
                            'id_user' => 0,
                            'name'    => $name,
                            'email'   => $email,
                            'token'   => Pnl::getRandomCode(),
                            'time'    => date("Y-m-d H:i:s"),
                            'status'  =>  'active',
                            'time_send' => '0000-00-00 00:00:00'
                        ];

                        $insert_id = core::database()->insert($fields, core::database()->getTableName('users'));

                        if ($insert_id) $count++;

                        if ($id_cat) {
                            foreach ($id_cat as $id) {
                                if (is_numeric($id)) {
                                    $fields = [
                                        'id_sub'  => 0,
                                        'id_user' => $insert_id,
                                        'id_cat'  => $id,
                                    ];

                                    core::database()->insert($fields, core::database()->getTableName('subscription'));
                                }
                            }
                        }
                    }
                }
            }
        }

        return $count;
    }
}