<?php
namespace Wangruyi\PhpCrond;

class FileOutput
{
    protected $path;
    protected $dateFormat;

    public function __construct($fileName, $dateFormat='')
    {
        $this->path = $fileName;
        $this->dateFormat = $dateFormat;
    }

    public function getFilePath()
    {
        return $this->parsePath($this->path);
    }

    public function parsePath(string $path): string
    {
        return preg_replace_callback(
            '/\{([^}]+)\}/',
            function ($match) {
                return date($match[1]);
            },
            $path
        );
    }

}
