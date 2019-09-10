<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\ReadySent;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use App\Helper\StringHelpers;

class LogController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function index($request,$response)
   {
       $title =  "Журнал рассылки";

       return $this->view->render($response,'dashboard/log/index.twig',compact('title'));
   }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
   public function clear($request,$response)
   {
       ReadySent::truncate();

       $this->flash->addMessage('success', 'Журнал очищен');

       return $response->withRedirect($this->router->pathFor('admin.log.index'));
   }

   public function download($request,$response, $parametr)
   {
       $ext = 'xlsx';
       $filename = 'emailexport' . date("d_m_Y") . '.xlsx';
       $oSpreadsheet_Out = new Spreadsheet();


       $oSpreadsheet_Out->getProperties()->setCreator('Alexander Yanitsky')
           ->setLastModifiedBy('PHP Newsletter')
           ->setTitle(StringHelpers::trans('str.mailing_report'))
           ->setSubject('Office 2007 XLSX Document')
           ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
           ->setKeywords('office 2007 openxml php')
           ->setCategory('Log export file')
       ;

       // Add some data
       $oSpreadsheet_Out->setActiveSheetIndex(0)
           ->setCellValue('A1', StringHelpers::trans('str.total') . ": \n" . StringHelpers::trans('str.sent') . ": %\n" . StringHelpers::trans('str.spent_time') . ":\n" .  StringHelpers::trans('str.read') . ":")

       ->setCellValue('A2', StringHelpers::trans('str.email'))
       ->setCellValue('B2', StringHelpers::trans('str.time'))
       ->setCellValue('C2', StringHelpers::trans('str.status'))
       ->setCellValue('A2',StringHelpers::trans('str.mailer'))
       ->setCellValue('B2',StringHelpers::trans('str.email'))
       ->setCellValue('C2',StringHelpers::trans('str.time'))
       ->setCellValue('D2',StringHelpers::trans('str.status'))
       ->setCellValue('E2',StringHelpers::trans('str.read'))
       ->setCellValue('F2',StringHelpers::trans('str.error'))
       ->mergeCells('A1:F1')
       ;

       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(['wrapText' => TRUE]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getFill()->applyFromArray(['setRGB' => 'E3DA62']);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EE7171']]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A1')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EEEEEE']]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EE7171']]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EE7171']]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('C2')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EE7171']]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('D2')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EE7171']]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('E2')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EE7171']]);
       $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('F2')->getFill()->applyFromArray([ 'fillType' => Fill::FILL_SOLID, 'startColor' => [ 'rgb' => 'EE7171']]);

       /*

       $aSheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       $aSheet->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       $aSheet->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       $aSheet->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       $aSheet->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
       $aSheet->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

*/




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
       $title = "Журнал рассылки";
       $id = $parametr['id'];

       return $this->view->render($response,'dashboard/log/info.twig',compact('title','id'));
   }
	
}