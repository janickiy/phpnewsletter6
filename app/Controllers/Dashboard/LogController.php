<?php

namespace App\Controllers\Dashboard;

use App\Controllers\Controller;
use App\Models\ReadySent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Helper\StringHelpers;

class LogController extends Controller
{
    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function index($request, $response)
    {
        $title = "Журнал рассылки";

        return $this->view->render($response, 'dashboard/log/index.twig', compact('title'));
    }

    /**
     * @param $request
     * @param $response
     * @return mixed
     */
    public function clear($request, $response)
    {
        ReadySent::truncate();

        $this->flash->addMessage('success', 'Журнал очищен');

        return $response->withRedirect($this->router->pathFor('admin.log.index'));
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
        $filename = 'log' . date("d_m_Y") . '.xlsx';
        $oSpreadsheet_Out = new Spreadsheet();

        $readySent = ReadySent::where('scheduleId', $parametr['id'])->get();

        if (!$readySent) return $this->view->render($response, 'errors/404.twig');

        $totalfaild = ReadySent::where('scheduleId', $parametr['id'])->where('success', 0)->count();
        $readmail = ReadySent::where('scheduleId', $parametr['id'])->where('readMail', 1)->count();
        $totaltime = ReadySent::selectRaw('*,sec_to_time(UNIX_TIMESTAMP(max(created_at)) - UNIX_TIMESTAMP(min(created_at))) as totaltime')->where('scheduleId', $parametr['id'])->first();
        $total = $readySent->count();

        if ($total > 0) {
            $success = $total - $totalfaild;
            $count = 100 * $success / $total;
        } else {
            $count = 0;
            $total = 0;
        }

        $oSpreadsheet_Out->getProperties()->setCreator('Alexander Yanitsky')
            ->setLastModifiedBy('PHP Newsletter')
            ->setTitle(StringHelpers::trans('str.log'))
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Log file');

        // Add some data
        $oSpreadsheet_Out->setActiveSheetIndex(0)
            ->setCellValue('A1', StringHelpers::trans('str.total') . ": $total\n" . StringHelpers::trans('str.sent') . ": " . $count . "%\n" . StringHelpers::trans('str.spent_time') . ": $totaltime->totaltime\n" . StringHelpers::trans('str.read') . ": " . $readmail)
            ->setCellValue('A2', StringHelpers::trans('str.email'))
            ->setCellValue('B2', StringHelpers::trans('str.time'))
            ->setCellValue('C2', StringHelpers::trans('str.status'))
            ->setCellValue('A2', StringHelpers::trans('str.mailer'))
            ->setCellValue('B2', StringHelpers::trans('str.email'))
            ->setCellValue('C2', StringHelpers::trans('str.time'))
            ->setCellValue('D2', StringHelpers::trans('str.status'))
            ->setCellValue('E2', StringHelpers::trans('str.read'))
            ->setCellValue('F2', StringHelpers::trans('str.error'))
            ->mergeCells('A1:F1');

        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->applyFromArray(['wrapText' => TRUE]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getFill()->applyFromArray(['setRGB' => 'E3DA62']);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A1')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEEEEE']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('C2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('D2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('E2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('F2')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EE7171']]);

        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('A2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('B2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('C2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('D2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('E2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('F2')->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);

        $i = 2;

        foreach ($readySent as $row) {
            $i++;

            $oSpreadsheet_Out->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $row->template)
                ->setCellValue('B' . $i, $row->email)
                ->setCellValue('C' . $i, $row->created_at)
                ->setCellValue('D' . $i, $row->success === 1 ? StringHelpers::trans('str.send_status_yes') : StringHelpers::trans('str.send_status_no')
                ->setCellValue('E' . $i, $row->readMail === 1 ? StringHelpers::trans('str.yes') : StringHelpers::trans('str.no')))
                ->setCellValue('F' . $i, $row->errorMsg);

            $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('D' . $i)->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
            $oSpreadsheet_Out->setActiveSheetIndex(0)->getStyle('E' . $i)->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_CENTER]);
        }

        $oSpreadsheet_Out->setActiveSheetIndex(0)->getRowDimension(1)->setRowHeight(70);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $oSpreadsheet_Out->getActiveSheet()->getColumnDimension('F')->setWidth(35);

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
    public function info($request, $response, $parametr)
    {
        $title = "Журнал рассылки";
        $id = $parametr['id'];

        return $this->view->render($response, 'dashboard/log/info.twig', compact('title', 'id'));
    }
}