<?php
namespace extend;

class Excel
{
    private $excel;

    public function __construct($config = [])
    {}

    public function output()
    {
        ob_end_clean();

        $buffer = \PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');

        header("Pragma: public");
        header("Expires: 0");
        header('Cache-Control: max-age=0');
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="' . $this->filename . '"');
        header("Content-Transfer-Encoding:binary");

        $buffer->save('php://output');

        exit();
    }

    /**
     * 导出为EXCEL
     *
     * @param array $list
     * @param array $options
     * @return void
     */
    public function export($list = [], $options = [])
    {
        global $_W;
        static $excel;
        static $sheet;
        static $beginAscll = 65;
        static $prefix = '';
        static $prefixIndex = 0;
        static $stage = 0;
        static $rowIndex = 1;
        static $cellIndex = 1;
        static $drawing = null;

        if($excel == null) {
            if(!isset($options['headers'])) {
                throw new \Exception('Export failed of options invalid.');
            }

            $this->excel = new \PHPExcel();
            $excel = $this->excel;

            $options['title'] = !isset($options['title']) ? 'DEFAULT' : $options['title'];
    
            $sheet = $excel->setActiveSheetIndex(0);
            // $sheet = $excel->getActiveSheet();
            $sheet->setTitle($options['title']);
            $sheet->getRowDimension($rowIndex)->setRowHeight(32);

            foreach($options['headers'] as $header) {
                if ($stage > 25 || $stage > 51) {
                    $prefix = chr($beginAscll + $prefixIndex); //AA AB AC AE
                    $prefixIndex += 1;
                    $stage = 0;
                }

                $id = $prefix . chr($stage + $beginAscll); // A B C D E
                $rowId = $id . $rowIndex;

                $sheet->setCellValue($id . $rowIndex, $header['title']);
                $sheet->getColumnDimension($id)->setWidth($header['width']);

                if (!isset($header['type'])) {
                    $header['type'] = 'text';
                }

                if ($header['type'] === 'id') {
                    $sheet->getStyle($rowId)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                } else {
                    $sheet->getStyle($rowId)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                }

                $sheet->getStyle($rowId)->getFill()->applyFromArray(array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'rgb' => 'F0E7DB'
                    )
                ));
                $sheet->getStyle($rowId)->applyFromArray(array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                ));

                $stage += 1;
            }

            $this->filename = $options['title'] . '_' . date('ymdHis') . '.xls';
        }

        foreach($list as $i => $row) {
            $rowIndex += 1;
            $prefix = '';
            $prefixIndex = 0;
            $stage = 0;
            $rowImage = false;

            foreach($options['headers'] as $mapper) {
                $key = $mapper['field'];
                $image = false;
                $drawing = null;

                if ($stage === 26 || $stage === 52) {
                    $prefix = chr($beginAscll + $prefixIndex); //AA AB AC AE
                    $prefixIndex += 1;
                    $stage = 0;
                }

                $id = $prefix . chr($stage + $beginAscll);  // A B C D E
                $rowId = $id . $rowIndex;

                if (!isset($mapper['type'])) {
                    $mapper['type'] = 'text';
                }

                if ($mapper['type'] != 'id') {
                    $sheet->getStyle($rowId)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                } else {
                    $sheet->getStyle($rowId)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }

                if ($mapper['type'] == 'id') {
                    $value = $rowIndex - 1;
                } elseif($mapper['type'] == 'money') {
                    $value = "¥".number_format(floatval($row[$key]) / 100, 2, '.', '');
                } elseif($mapper['type'] == 'numeric') {
                    $value = "\t".$row[$key];
                } elseif($mapper['type'] == 'date') {
                    $value = $row[$key];
                    if (is_numeric($value)) {
                        $value = date('Y-m-d H:i', $value);
                    }
                    $value = "\t".$value;
                } elseif($mapper['type'] == 'qrcode') {
                    $image = true;
                    $value = m('qrcode')->createQrcode($row[$key]);
                    $values = [$value];
                } elseif($mapper['type'] == 'link') {
                    $sheet->getCell($rowId)->getHyperlink()->setUrl($value);
                    $value = strip_tags($row[$key]);
                } elseif($mapper['type'] == 'text') {
                    $value = strip_tags($row[$key]);
                } elseif($mapper['type'] == 'image') {
                    $values = explode(",", $row[$key]);
                    $image = true;
                } else {
                    $value = $row[$key];
                }

                if ($image) {
                    foreach($values as $image) {
                        if (!empty($image)) {
                            $localimage = str_replace($_W['siteroot'], IA_ROOT . '/', $image);

                            if (file_exists($localimage)) {
                                $drawing = new \PHPExcel_Worksheet_Drawing();
                                $drawing->setPath($localimage);
                                $drawing->setWidth(50);
                                $drawing->setHeight(50);
                                $drawing->setCoordinates($rowId);
                                $drawing->setWorksheet($sheet);
                            } else {
                                $sheet->getCell($rowId)->getHyperlink()->setUrl($image);
                                $value = $image;
                            }
                        }

                        break;
                    }

                    if ($drawing != null) {
                        $stage += 1;
                        $rowImage = true;
                        continue;
                    }
                }

                $sheet->setCellValue($rowId, $value);
                $value = "";

                $stage += 1;
            }

            $sheet->getRowDimension($rowIndex)->setRowHeight($rowImage ? 50 : 26);
        }

        if (empty($list)) {
            return $this->output();
        }

        return $this;
    }
}