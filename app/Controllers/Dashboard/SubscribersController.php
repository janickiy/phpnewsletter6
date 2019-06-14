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
    public function index($request, $response)
    {
        $title = "Подписчики";

        return $this->view->render($response, 'dashboard/subscribers/index.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function create($request, $response)
    {
        $title = "Добавление подписчика";

        $category = Category::get();

        return $this->view->render($response, 'dashboard/subscribers/create_edit.twig', compact('title', 'category'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function store($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => ['rules' => v::email()->notEmpty(), 'messages' => ['email' => 'Адрес электроной почты указан не верно', 'notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
        }

        if (Subscribers::where('email', 'like', $request->getParam('email'))->first()) {
            $_SESSION['errors'] = ['email' => ['unique' => 'Адрес электроной почты уже есть в базе данных! Укажите другой']];

            return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
        }

        $id = Subscribers::create(array_merge($request->getParsedBody(), ['active' => 1, 'token' => StringHelpers::token()]))->id;

        if ($request->getParam('categoryId')) {

            foreach ($request->getParam('categoryId') as $categoryId) {
                if (is_numeric($categoryId)) {
                    Subscriptions::create(['subscriberId' => $id, 'categoryId' => $categoryId]);
                }
            }
        }

        $this->flash->addMessage('success', 'Данные успешно добавлены');

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
        $validation = $this->validator->validate($request, [
            'email' => ['rules' => v::email()->notEmpty(), 'messages' => ['email' => 'Адрес электроной почты указан не верно', 'notEmpty' => 'Это поле обязательно для заполнения']],
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
        $maxUploadFileSize = StringHelpers::maxUploadFileSize();

        return $this->view->render($response, 'dashboard/subscribers/import.twig', compact('title', 'charsets', 'category','maxUploadFileSize'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function importSubscribers($request, $response)
    {
        $f = $request->getUploadedFiles()['import'];

        $validation = $this->validator->validate($request, [
            'import' => ['rules' => v::file(), 'messages' => ['file' => 'Файл для импорта не выбран!']],
            'name' => v::stringType()->notEmpty()
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.subscribers.import'));
        }

        if (v::file()->size(null, StringHelpers::maxUploadFileSize())->validate($f->getClientFilename()) === false) {
            $_SESSION['errors'] = $validation->getErrors();




            return $response->withRedirect($this->router->pathFor('admin.subscribers.import'));
        }

        $ext = pathinfo($f->getClientFilename(), PATHINFO_EXTENSION);

        if ($ext == 'xls' || $ext == 'xlsx') {
           // $result = $this->importFromExcel($f->file,$request->getParam('categoryId'));
        } else {
            $result = $this->importFromText($f->file,$request->getParam('categoryId'));
        }

        if ($result === false) {
            $this->flash->addMessage('error', 'Ошибка импорта! Невозможно прочитать файл');

            return $response->withRedirect($this->router->pathFor('admin.subscribers.import'));
        }

        $this->flash->addMessage('success', 'Импорт завершен. Всего импортировано: ' . $result);

        return $response->withRedirect($this->router->pathFor('admin.subscribers.index'));
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

    private function importFromExcel()
    {

    }

    /**
     * @param $file
     * @param array|null $categoryId
     * @return bool|int
     */
    private function importFromText($file, array $categoryId = null)
    {
        if (!($fp = @fopen($file, "rb"))) {
            return false;
        } else {
            $buffer = fread($fp, filesize($file));
            fclose($fp);
            $tok = strtok($buffer, "\n");

            while ($tok) {
                $tok = strtok("\n");
                $strtmp[] = $tok;
            }

            $count = 0;

            foreach ($strtmp as $val) {
                $str = $val;

                if (!mb_check_encoding($str, 'utf-8')) {
                    $sh = new ConvertCharset("utf-8", "utf-8");
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

                    $subscriber = Subscribers::where('email','like',$email);
                    $row = $subscriber->first();

                    if ($row) {
                        Subscriptions::where('subscriberId',$row->id)->delete();

                        if ($categoryId) {
                            foreach ($categoryId as $id) {
                                if (is_numeric($id)) {
                                    Subscriptions::create(['subscriberId' => $row->id, 'categoryId' => $id]);
                                }
                            }
                        }
                    } else {
                        $data = [
                            'name' => $name,
                            'email' => $email,
                            'token' => StringHelpers::token(),
                            'active' => 1,
                        ];

                        $insertId = Subscribers::create($data)->id;

                        if ($insertId) $count++;

                        if ($categoryId) {
                            foreach ($categoryId as $id) {
                                if (is_numeric($id)) {
                                    Subscriptions::create(['subscriberId' => $insertId, 'categoryId' => $id]);
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