<?php
class FileByteReader {

    protected $file;
    protected  $file_stats;
    protected  $file_size;

    function __construct($fname) {
        if ( !file_exists($fname)) {
            throw new Exception("filename given not a valid file: ".$fname);
        }

        $this->file = fopen($fname, 'rb');
        $this->file_stats = fstat ( $this->file );
        $this->file_size = $this->file_stats['size'];
        if (empty($this->file_size)) {
            throw new Exception("file not opened and started,or file is empty");

        }
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    function getFileSize() {
        return $this->file_size;
    }

    function getBytes($offset,$length) {

        if ($length <= 0) {
            throw new Exception("Can only read positive length, length given was $length");
        }

        if ($offset < 0) {
            throw new Exception("Can only read from positive offset, offset given was $length");
        }

        $hoa = ftell($this->file);

        $check = fseek($this->file, $offset, SEEK_SET);
        if ($check < 0) {
            throw new Exception("cannot move position for reading in the file");
        }

        if ($this->is_past_end_of_file($length)) {
            throw new Exception("Tried to read past end of file");
        }

        $what = fread($this->file, $length);
        if (strlen($what) != $length) {
            $hoa = ftell($this->file);

            throw new Exception("could not read all of the requested data.
             Asked for $length and got $what, file position $hoa in total file size of $this->file_size");
        }
        return $what;
    }

    protected  function is_past_end_of_file($amount_to_move) {
        $hoa = ftell($this->file);
        if ($amount_to_move + $hoa > $this->file_size) {
            return true;
        }
        return false;
    }

}