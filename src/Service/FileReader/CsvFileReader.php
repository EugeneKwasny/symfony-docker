<?php

namespace App\Service\FileReader;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Model\BookData;

class CsvFileReader implements FileReaderInterface
{   
    /**
     * @return BookData[]
     */
    public function read(UploadedFile $file): array
    {
        $books  = [];

        if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
            $counter= 0;
            while (($data = fgetcsv($handle, null, ';')) !== FALSE) {
                if($counter === 0){
                    $counter++;
                    continue;
                }

                $books[] = new BookData($data[0], $data[1], $data[2]);

                $counter++;
            }
            fclose($handle);

        }

        return $books;
    }
}