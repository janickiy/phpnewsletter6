<?php

namespace App\Controllers\Dashboard;

use App\Models\{Category, Charset, Subscribers, Subscriptions};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use App\Helper\StringHelpers;
use Janickiy\ConvertCharset\ConvertCharset;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

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

        return $this->view->render($response, 'dashboard/subscribers/import.twig', compact('title', 'charsets', 'category', 'maxUploadFileSize'));
    }


    public function importSubscribers($request, $response)
    {
        $f = $request->getUploadedFiles()['import'];

        if (v::exists()->validate($f->file) === false) {
            $_SESSION['errors'] = 'Файл для импорта не выбран!';

            return $response->withRedirect($this->router->pathFor('admin.subscribers.import'));
        }

        if (v::size(null, StringHelpers::maxUploadFileSize())->validate($f->file) === false) {
            $_SESSION['errors'] = 'Максимальный размер файла превышает ' . StringHelpers::maxUploadFileSize() . '!';

            return $response->withRedirect($this->router->pathFor('admin.subscribers.import'));
        }




    //    PhpOffice\PhpSpreadsheet\IOFactory

        $ext = pathinfo($f->getClientFilename(), PATHINFO_EXTENSION);


        if($ext == 'csv'){
            $reader = new Csv();
        } elseif($ext == 'xlsx') {
            $reader = new Xlsx();
        } else {
            $reader = new Xls();
        }

      //  $f->file = StringHelper::convertEncoding ($f->file, 'windows-1251', 'UTF-8');



        if($ext == 'csv') {
            $encoding = mb_detect_encoding(file_get_contents($f->file),
                // example of a manual detection order
                'ISO-8859-15,UTF-8,Windows-1251,ASCII');

            $reader->setInputEncoding( 'Windows-1251');

          // echo $encoding;
        //   exit;
        }

        $spreadsheet = $reader->load($f->file);

        $allDataInSheet = $spreadsheet->getActiveSheet()->toArray(true, true, true, true);


        // array Count
        $arrayCount = count($allDataInSheet);
        $flag = 0;
        $createArray = array('Email', 'Name');
        $makeArray = array('Email' => 'Email', 'Name' => 'Name');
        $SheetDataKey = array();

        foreach ($allDataInSheet as $dataInSheet) {

            foreach ($dataInSheet as $key => $value) {

            echo $key . ' - '. $value;
            }
        }


       exit;



        if ($ext == 'xls' || $ext == 'xlsx') {
            $result = $this->importFromExcel($f->file,$request->getParam('categoryId'));
        } else if($ext == 'csv') {
            $result = $this->importFromCsv($f->file,$request->getParam('categoryId'));
        } else {
            $result = $this->importFromText($f->file, $request->getParam('categoryId'));
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

    private function importFromCsv($file, array $categoryId = null)
    {
        $inputFileType = 'Csv';

        $reader = IOFactory::createReader($inputFileType);
        $inputFileName = array_shift($file);
       // $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' into WorkSheet #1 using IOFactory with a defined reader type of ' . $inputFileType);
        $spreadsheet = $reader->load($inputFileName);
        $spreadsheet->getActiveSheet()->setTitle(pathinfo($inputFileName, PATHINFO_BASENAME));

//            $reader->setSheetIndex($sheet + 1);
            $reader->loadIntoExisting($file, $spreadsheet);
            $spreadsheet->getActiveSheet()->setTitle(pathinfo($file, PATHINFO_BASENAME));

        $loadedSheetNames = $spreadsheet->getSheetNames();

            $spreadsheet->setActiveSheetIndexByName($file);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            var_dump($sheetData);



    }

    private function importFromExcel($file, array $categoryId = null)
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

                    $subscriber = Subscribers::where('email', 'like', $email);
                    $row = $subscriber->first();

                    if ($row) {
                        Subscriptions::where('subscriberId', $row->id)->delete();

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