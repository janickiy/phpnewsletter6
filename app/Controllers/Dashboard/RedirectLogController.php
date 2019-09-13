<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\RedirectLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helper\StringHelpers;

class RedirectLogController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function index($request,$response)
   {
       $title =  "Статистика переходов по ссылкам";

       return $this->view->render($response,'dashboard/redirect_log/index.twig',compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function clear($request,$response)
   {
       RedirectLog::truncate();

       $this->flash->addMessage('success', 'Статистика очищина');

       return $response->withRedirect($this->router->pathFor('admin.redirect_log.index'));
   }

    /**
     * @param $request
     * @param $response
     * @param $parametr
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
   public function download($request, $response, $parametr)
   {
       $ext = 'xlsx';
       $filename = 'redirect_log_' . date("d_m_Y") . '.xlsx';
       $oSpreadsheet_Out = new Spreadsheet();

       $redirectLog = RedirectLog::where('url',$parametr['url'])->get();

       if (!$redirectLog) return $this->view->render($response, 'errors/404.twig');

       $oSpreadsheet_Out->getProperties()->setCreator('Alexander Yanitsky')
           ->setLastModifiedBy('PHP Newsletter')
           ->setTitle(StringHelpers::trans('str.redirect_log'))
           ->setSubject('Office 2007 XLSX Document')
           ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
           ->setKeywords('office 2007 openxml php')
           ->setCategory('Redirect Log file');

       $oSpreadsheet_Out->setActiveSheetIndex(0)
           ->setCellValue('A1', 'E-mail')
           ->setCellValue('B1', StringHelpers::trans('str.time'));

       $i = 0;

       foreach ($redirectLog as $row) {
           $i++;

           $oSpreadsheet_Out->setActiveSheetIndex(0)
               ->setCellValue('A' . $i, $row->email)
               ->setCellValue('B' . $i, $row->created_at);
       }

       $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('A')->setWidth(30);
       $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('B')->setWidth(25);

       $oWriter = IOFactory::createWriter($oSpreadsheet_Out, 'Xlsx');
       ob_start();
       $oWriter->save('php://output');
       $contents = ob_get_contents();
       ob_end_clean();

       return $response->write($contents)
           ->withHeader('Content-Disposition', 'attachment; filename=' . $filename)
           ->withHeader('Cache-Control', 'max-age=0')
           ->withHeader('Content-Type', StringHelpers::getMimeType($ext));
   }

    /**
     * @param $request
     * @param $response
     * @param $parametr
     * @return mixed
     */
    public function info($request,$response,$parametr)
    {
        $title =  "Статистика переходов по ссылкам";

        $url = $parametr['url'];

        return $this->view->render($response, 'dashboard/redirect_log/info.twig', compact('title', 'url'));
    }
}