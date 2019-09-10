<?php

namespace App\Controllers\Dashboard;

use App\Models\{Category, Charset, Subscribers, Subscriptions};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use App\Helper\StringHelpers;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            'email' => ['rules' => v::email()->notEmpty(), 'messages' => ['email' => 'Адрес электронной почты указан не верно', 'notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
        }

        if (Subscribers::where('email', 'like', $request->getParam('email'))->first()) {
            $_SESSION['errors'] = ['email' => ['unique' => 'Адрес электронной почты уже есть в базе данных! Укажите другой']];

            return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
        }

        $id = Subscribers::create(array_merge($request->getParsedBody(), ['active' => 1, 'token' => StringHelpers::token()]))->id;

        if ($request->getParam('categoryId') && $id) {

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

        $subscriptions = [];

        foreach ($subscriber->categories as $row) {
            $subscriptions[] = $row->id;
        }

        return $this->view->render($response, 'dashboard/subscribers/create_edit.twig', compact('subscriber', 'title', 'category', 'subscriptions'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function update($request, $response)
    {
        $validation = $this->validator->validate($request, [
            'email' => ['rules' => v::email()->notEmpty(), 'messages' => ['email' => 'Адрес электронной почты указан неверно', 'notEmpty' => 'Это поле обязательно для заполнения']],
        ]);

        if (!$validation->isValid()) {
            $_SESSION['errors'] = $validation->getErrors();

            return $response->withRedirect($this->router->pathFor('admin.subscribers.create'));
        }

        $data['name'] = $request->getParam('name');
        $data['email'] = $request->getParam('email');

        Subscribers::where('id', $request->getParam('id'))->update($data);

        $this->flash->addMessage('success', 'Данные успешно обновлены');

        return $response->withRedirect($this->router->pathFor('admin.subscribers.index'));
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

    /**
     * @param $request
     * @param $response
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
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

        $ext = pathinfo($f->getClientFilename(), PATHINFO_EXTENSION);

        switch ($ext) {
            case 'csv':
            case 'xls':
            case 'xlsx':

                $result = $this->importFromExcel($f, $request->getParam('categoryId'), $request->getParam('charset'));

                break;

            default:

                $result = $this->importFromText($f, $request->getParam('categoryId'), $request->getParam('charset'));
        }

        if ($result === false) {
            $this->flash->addMessage('error', 'Ошибка импорта! Невозможно прочитать файл');

            return $response->withRedirect($this->router->pathFor('admin.subscribers.import'));
        }

        $this->flash->addMessage('success', 'Импорт завершен. Всего импортировано: ' . $result);

        return $response->withRedirect($this->router->pathFor('admin.subscribers.index'));
    }

    /**
     * @param $f
     * @param array|null $categoryId
     * @param null $charset
     * @return bool|int
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function importFromExcel($f, array $categoryId = null, $charset = null)
    {
        $ext = pathinfo($f->getClientFilename(), PATHINFO_EXTENSION);

        if ($ext == 'csv') {
            $reader = new Csv();

            if ($ext == 'csv' && $charset) {
                $reader->setInputEncoding($charset);
            }

        } elseif ($ext == 'xlsx') {
            $reader = new Xlsx();
        } else {
            $reader = new Xls();
        }

        $count = 0;

        $spreadsheet = $reader->load($f->file);

        if (!$spreadsheet) return false;

        $allDataInSheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        foreach ($allDataInSheet as $dataInSheet) {
            $email = trim($dataInSheet['A']);
            $name = trim($dataInSheet['B']);

            if (StringHelpers::isEmail($email)) {
                $subscribers = Subscribers::where('email', 'like', $email)->first();

                if ($subscribers && $categoryId) {
                    Subscriptions::where('subscriberId', $subscribers->id)->delete();

                    foreach ($categoryId as $category) {
                        if (is_numeric($category)) {
                            $data = [
                                'subscriberId' => $subscribers->id,
                                'categoryId' => $category,
                            ];

                            Subscriptions::create($data);
                        }
                    }
                } else {
                    $subscribersData = [
                        'name' => $name,
                        'email' => $email,
                        'active' => 1,
                        'token' => StringHelpers::token()
                    ];

                    $insertId = Subscribers::create($subscribersData)->id;

                    if ($categoryId) {
                        foreach ($categoryId as $category) {
                            if (is_numeric($category)) {
                                $data = [
                                    'subscriberId' => $insertId,
                                    'categoryId' => $category,
                                ];
                            }
                        }

                        Subscriptions::create($data);
                    }

                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function export($request, $response)
    {
        $title = "Экспорт";
        $category = Category::get();

        return $this->view->render($response, 'dashboard/subscribers/export.twig', compact('title','category'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportSubscribers($request, $response)
    {
        $request->getParam('export_type');
        $subscribers = $this->getSubscribersList($request->getParam('categoryId'));

        if ($request->getParam('export_type') == 'text') {
            $ext = 'txt';
            $filename = 'emailexport' . date("d_m_Y") . '.txt';

            if ($subscribers) {
                $contents = '';
                foreach ($subscribers as $subscriber) {
                    $contents .= "" . $subscriber->email . " " . $subscriber->name . "\r\n";
                }
            }
        } elseif ($request->getParam('export_type') == 'excel') {

            $ext = 'xlsx';
            $filename = 'emailexport' . date("d_m_Y") . '.xlsx';
            $oSpreadsheet_Out = new Spreadsheet();

            $oSpreadsheet_Out->getProperties()->setCreator('Alexander Yanitsky')
                ->setLastModifiedBy('PHP Newsletter')
                ->setTitle('Office 2007 XLSX Document')
                ->setSubject('Office 2007 XLSX Document')
                ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Email export file')
            ;

            // Add some data
            $oSpreadsheet_Out->setActiveSheetIndex(0)
                ->setCellValue('A1', 'User email')
                ->setCellValue('B2', 'Name')
            ;

            $i = 0;

            foreach ($subscribers as $subscriber) {
                $i++;

                $oSpreadsheet_Out->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $subscriber->email)
                    ->setCellValue('B'.$i, $subscriber->name)
                ;
            }

            $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('B')->setWidth(30);

            $oWriter = IOFactory::createWriter($oSpreadsheet_Out, 'Xlsx');
            ob_start();
            $oWriter->save('php://output');
            $contents = ob_get_contents();
            ob_end_clean();
        }

        if ($request->getParam('compress') == 'zip'){

            $fout = fopen("php://output", "wb");

            if ($fout !== false){
                fwrite($fout, "\x1F\x8B\x08\x08".pack("V", '')."\0\xFF", 10);

                $oname = str_replace("\0", "", $filename);
                fwrite($fout, $oname."\0", 1+strlen($oname));

                $fltr = stream_filter_append($fout, "zlib.deflate", STREAM_FILTER_WRITE, -1);
                $hctx = hash_init("crc32b");

                if (!ini_get("safe_mode")) set_time_limit(0);

                hash_update($hctx, $contents);
                $fsize = strlen($contents);

                fwrite($fout, $contents, $fsize);

                stream_filter_remove($fltr);

                $crc = hash_final($hctx, TRUE);

                fwrite($fout, $crc[3] . $crc[2] . $crc[1] . $crc[0], 4);
                fwrite($fout, pack("V", $fsize), 4);

                fclose($fout);

                return $response->withHeader('Content-Type', 'application/zip')
                        ->withHeader('Content-Disposition', 'filename=emailexport_' . date("d_m_Y") . '.zip');

            }

        } else {
            return $response->write($contents)
                ->withHeader('Content-Disposition', 'attachment; filename=' . $filename)
                ->withHeader('Cache-Control', 'max-age=0')
                ->withHeader('Content-Type', StringHelpers::getMimeType($ext));
        }
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

    /**
     * @param $file
     * @param array|null $categoryId
     * @return bool|int
     */
    private function importFromText($f, array $categoryId = null, $charset = null)
    {
        if (!($fp = @fopen($f->file, "rb"))) {
            return false;
        } else {
            $buffer = fread($fp, filesize($f->file));
            fclose($fp);
            $tok = strtok($buffer, "\n");

            while ($tok) {
                $tok = strtok("\n");
                $strtmp[] = $tok;
            }

            $count = 0;

            foreach ($strtmp as $val) {
                $str = $val;

                if ($charset) {
                    $str = StringHelper::convertEncoding($str, 'utf-8', $charset);
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
                    $subscriber = Subscribers::where('email', 'like', $email)->first();

                    if ($subscriber) {
                        Subscriptions::where('subscriberId', $subscriber->id)->delete();

                        if ($categoryId) {
                            foreach ($categoryId as $id) {
                                if (is_numeric($id)) {
                                    Subscriptions::create(['subscriberId' => $subscriber->id, 'categoryId' => $id]);
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

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function status($request, $response)
    {
        $temp = [];

        foreach ($request->getParam('activate') as $id) {
            if (is_numeric($id)) {
                $temp[] = $id;
            }
        }

        switch ($request->getParam('action')) {
            case  0 :
            case  1 :

                Subscribers::whereIN('id', $temp)->update(['active' => $request->getParam('action')]);

                break;

            case 2 :

                Subscribers::whereIN('id', $temp)->delete();

                break;
        }

        $this->flash->addMessage('success', 'Действия были выполнены');

        return $response->withRedirect($this->router->pathFor('admin.subscribers.index'));
    }

    /**
     * @param array $categoryId
     * @return mixed
     */
    private function getSubscribersList($categoryId = [])
    {
        if ($categoryId) {
            $temp = [];
            foreach ($categoryId as $id) {
                if (is_numeric($id)) {
                    $temp[] = $id;
                }
            }

            $subscribers = Subscribers::select('subscribers.name','subscribers.email')
                ->leftJoin('subscriptions', function($join) {
                    $join->on('subscribers.id', '=', 'subscriptions.subscriberId');
                })
                ->where('subscribers.active','=',1)
                ->whereIn('subscriptions.categoryId',$temp)
                ->groupBy('subscribers.id')
                ->get();
        } else {
            $subscribers = Subscribers::select('name','email')
                ->where('active','=',1)
                ->get();
        }

        return $subscribers;
    }
}