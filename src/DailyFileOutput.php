<?php
namespace Wangruyi\PhpCrond;

class DailyFileOutput extends FileOutput
{

    public function __construct($fileName, $dateFormat='Ymd')
    {
        parent::__construct($fileName, $dateFormat);
    }

    public function getFilePath()
    {
        $datetime = date($this->dateFormat);
        $fileInfo = pathinfo($this->path);
        $formatName = $fileInfo['dirname'];
        $formatName .= \DIRECTORY_SEPARATOR . $fileInfo['filename'];
        $formatName .= '-' . $datetime;

        if (!empty($fileInfo['extension'])) {
            $formatName .= '.' . $fileInfo['extension'];
        }

        return $formatName;
    }
}
