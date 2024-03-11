<?php
namespace Wangruyi\PhpCrond;

class DailyFileOutput
{
    const PATH_VARIABLE = '{DATE}';

    protected $fileName;
    protected $dateFormat;

    public function __construct($fileName, $dateFormat='Ymd')
    {
        $this->fileName = $fileName;
        $this->dateFormat = $dateFormat;
    }

    public function getFilePath()
    {
        $datetime = date($this->dateFormat);

        if ($this->hasPathVariables($this->fileName)){
            $formatName = str_replace(self::PATH_VARIABLE, $datetime, $this->fileName);
        }else {
            $fileInfo = pathinfo($this->fileName);
            $formatName = $fileInfo['dirname'];
            $formatName .= \DIRECTORY_SEPARATOR . $fileInfo['filename'];
            $formatName .= '-' . $datetime;

            if (!empty($fileInfo['extension'])) {
                $formatName .= '.' . $fileInfo['extension'];
            }
        }

        return $formatName;
    }

    private function hasPathVariables($fileName)
    {
        if (strpos($fileName, self::PATH_VARIABLE) === false) {
            return false;
        }

        return true;
    }
}