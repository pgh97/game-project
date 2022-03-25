<?php

namespace LOGTOOL\Util;

class CheckFileUtil
{
    private array $files;
    private array $dirs;

    public function addFiles($ff){
        $this->files[] = $ff;
    }

    public function addDirs($dir){
        $this->dirs[] = $dir;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getDirs(): array
    {
        return $this->dirs;
    }

    /**
     * @param array $dirs
     */
    public function setDirs(array $dirs): void
    {
        $this->dirs = $dirs;
    }

    //오늘 날짜
    public function getCurrentDay(){
        date_default_timezone_set('Asia/Seoul');
        return date("Ymd");
    }

    //어제 날짜
    public function getYesterday(){
        date_default_timezone_set('Asia/Seoul');
        return date("Ymd", strtotime("-1 day"));
    }

    //어제 날짜인 디렉토리와 log 파일만 가져옴
    public function listFolderFiles($dir){
        $ffs = scandir($dir);

        unset($ffs[array_search('.', $ffs, true)]);
        unset($ffs[array_search('..', $ffs, true)]);

        // prevent empty ordered elements
        if (count($ffs) < 1)
            return;

        foreach($ffs as $ff){
            if(is_dir($dir.'/'.$ff)){
                $this->addDirs($ff."_".$this->getYesterday());
                $this->listFolderFiles($dir.'/'.$ff);
            }else{
                if(strpos($ff, "current") == false ){
                    $this->addFiles($dir.'/'.$ff);
                }
            }
        }
    }
}