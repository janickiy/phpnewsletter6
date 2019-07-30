<?php

namespace App\Controllers\Dashboard;

use App\Models\{Category, Schedule, Templates, ScheduleCategory};
use App\Controllers\Controller;
use Respect\Validation\Validator as v;
use Symfony\Component\Console\Helper\Helper;

class ScheduleController extends Controller
{
   public function index($request,$response)
   {
       $title = "Расписание рассылки";

       $schedule = Schedule::get();

       return $this->view->render($response,'dashboard/schedule/index.twig', compact('schedule','title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function create($request, $response)
   {
       $title = "Добавление рассылки";

       $templates = Templates::get();
       $category = Category::get();

       return $this->view->render($response,'dashboard/schedule/create_edit.twig', compact('title', 'templates', 'category'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function store($request, $response)
   {
       $validation = $this->validator->validate($request,[
           'templateId' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
           'categoryId'  => ['rules' => v::arrayType()->each(v::intVal()), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'arrayType' => 'Не верно указано категория подписчиков', 'intVal' => 'Не верно указано категория подписчиков']],
           'date' => ['rules' => v::date('Y-m-d H:i:s')->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'date' => 'Не верно указан формат датты']],
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.schedule.create'));
       }

       $id = Schedule::create($request->getParsedBody())->id;;

       if ($request->getParam('categoryId') && $id) {

           foreach ($request->getParam('categoryId') as $categoryId) {
               if (is_numeric($categoryId)) {
                   ScheduleCategory::create(['scheduleId' => $id, 'categoryId' => $categoryId]);
               }
           }
       }

       $this->flash->addMessage('success','Данные успешно добавлены');

       return $response->withRedirect($this->router->pathFor('admin.schedule.index'));
   }

    /**
     * @param $request
     * @param $response
     * @param $id
     * @return mixed
     */
   public function edit($request, $response, $id)
   {
       $title = "Редактирование рассылки";
       $schedule = Schedule::where('id', $id)->first();

       if (!$schedule) return $this->view->render($response, 'errors/404.twig');

       $templates = Templates::get();
       $category = Category::get();

       return $this->view->render($response, 'dashboard/schedule/create_edit.twig', compact('templates', 'category', 'schedule', 'title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function update($request, $response)
   {
       if (!is_numeric($request->getParam('id'))) return $this->view->render($response, 'errors/500.twig');

       $validation = $this->validator->validate($request, [
           'templateId' => ['rules' => v::numeric()->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения']],
           'categoryId'  => ['rules' => v::arrayType()->each(v::intVal()), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'arrayType' => 'Не верно указано категория подписчиков', 'intVal' => 'Не верно указано категория подписчиков']],
           'date' => ['rules' => v::date('Y-m-d H:i:s')->notEmpty(), 'messages' => ['notEmpty' => 'Это поле обязательно для заполнения', 'date' => 'Не верно указан формат датты']],
       ]);

       if (!$validation->isValid()) {
           $_SESSION['errors'] = $validation->getErrors();

           return $response->withRedirect($this->router->pathFor('admin.schedule.edit',['id' => $request->getParam('id')]));
       }

       $data['name'] = $request->getParam('name');

       Schedule::where('id', $request->getParam('id'))->update($data);
       ScheduleCategory::where('id', $request->getParam('id'))->delete();

       if ($request->getParam('categoryId')) {

           foreach ($request->getParam('categoryId') as $categoryId) {
               if (is_numeric($categoryId)) {
                   Subscriptions::create(['subscriberId' => $request->getParam('id'), 'categoryId' => $categoryId]);
               }
           }
       }

       $this->flash->addMessage('success', 'Данные успешно обновлены');

       return $response->withRedirect($this->router->pathFor('admin.schedule.index'));
   }

    /**
     * @param $request
     * @param $response
     * @param $id
     */
   public function destroy($request, $response, $id)
   {
       Schedule::where('id', $id)->delete();
   }
}