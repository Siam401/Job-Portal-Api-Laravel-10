<?php

namespace App\Services\CvParser;

use Smalot\PdfParser\Parser;

class Handler
{

    public function process(string $filePdf): array
    {
        $data = [];
        try {
            if (!$this->checkFileIsPdf($filePdf)) {
                return $data;
            }

            $parser = new Parser();
            $pdfText = $parser->parseFile($filePdf)->getText();

            $lines = array_values(array_filter(preg_split('/\n|\r\n?/', $pdfText)));

            if (str_contains(strtolower(trim($lines[0])), 'job title:') || str_contains(strtolower(trim($lines[1])), 'last updated:')) {
                unset($lines[0], $lines[1]);
            }
            $data = BdJobsCvParser::parse(array_values($lines));
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $data;
    }

    private function checkFileIsPdf(string $filePdf)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $fileExt = finfo_file($finfo, $filePdf);


        if ($fileExt !== 'application/pdf') {
            return false;
        }

        return true;
    }

}