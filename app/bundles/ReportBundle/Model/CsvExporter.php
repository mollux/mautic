<?php

namespace Mautic\ReportBundle\Model;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Templating\Helper\FormatterHelper;
use Mautic\ReportBundle\Crate\ReportDataResult;

/**
 * Class CsvExporter.
 */
class CsvExporter
{
    public function __construct(protected FormatterHelper $formatterHelper, private CoreParametersHelper $coreParametersHelper)
    {
    }

    /**
     * @param resource $handle
     * @param int      $page
     */
    public function export(ReportDataResult $reportDataResult, $handle, $page = 1)
    {
        if (1 === $page) {
            $this->putHeader($reportDataResult, $handle);
        }

        foreach ($reportDataResult->getData() as $data) {
            $row = [];
            foreach ($data as $k => $v) {
                $type       = $reportDataResult->getType($k);
                $typeString = 'string' !== $type;
                $row[]      = $typeString ? $this->formatterHelper->_($v, $type, true) : $v;
            }
            $this->putRow($handle, $row);
        }
    }

    /**
     * @param resource $handle
     */
    private function putHeader(ReportDataResult $reportDataResult, $handle)
    {
        $this->putRow($handle, $reportDataResult->getHeaders());
    }

    /**
     * @param resource $handle
     */
    private function putRow($handle, array $row)
    {
        if ($this->coreParametersHelper->get('csv_always_enclose')) {
            fputs($handle, '"'.implode('","', $row).'"'."\n");
        } else {
            fputcsv($handle, $row);
        }
    }
}
