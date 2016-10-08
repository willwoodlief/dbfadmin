<?php
$real =   realpath( dirname( __FILE__ ) );

//lib/zipClass.php


class GetOneFileFromZip {
    protected $extracted_file_path = null;
    protected $type_file = false;
    protected $extracted_file_name = '';
    public function __construct($filepath){
        $this->type_file = $this->is_file_rar_or_zip($filepath);
        if ($this->type_file == 2) {
            $this->extracted_file_path = $this->get_zip_file($this->type_file,
                                            $filepath,
                                            $this->extracted_file_name);
        }
    }

    public function __destruct() {
        if ($this->extracted_file_path) {unlink($this->extracted_file_path); }
    }

    public function getExtractedTempFilePath() { return $this->extracted_file_path;}
    public function getExtractedFileName() { return $this->extracted_file_name;}
    public function isZipFile() { return $this->type_file == 2;}

     //returns 1 for rar, 2 for zip  or false
     private function is_file_rar_or_zip($fileName) {

        $fh = @fopen($fileName, "r");
        if (!$fh) {
            throw new Exception("could not open file to check if its zip");
        }
        $blob = fgets($fh, 5);
        fclose($fh);
        if (strpos($blob, 'Rar') !== false) {
            return 1;
        } else
            if (strpos($blob, 'PK') !== false) {
                return 2;
            } else {
                return false;
            }
     }

    //returns temp file name which will need to be unlinked after using it
    private function get_zip_file($type,$zippedfileName,&$name) {

        if ($type!=2) {
            throw new Exception("wrong compressed type put in expected 2, type passed was [$type]");
        }

        $tmpfname = tempnam("/tmp", "zipExtract");
        $zip = new ZipArchive;
        $code = $zip->open($zippedfileName);
        if ( $code === true) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                if ($i>0) {break;}
                $filename = $zip->getNameIndex($i);
                $name = $filename;
               // $fileinfo = pathinfo($filename);
                copy("zip://".$zippedfileName."#".$filename, $tmpfname);
            }
            $zip->close();
        } else {
            throw new Exception("Could not open zip file. Code is " . $code);
        }

        return $tmpfname;
    }

}






