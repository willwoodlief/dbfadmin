<?php
// written by will !
require_once 'dbf/dbf_class.php';
require_once 'batch.php';
require_once 'rules.php';

class Blocks
{

    private $errors = [];
    protected $dbf = null;
    protected $blocks = [];
    protected $batch = null;

    public function __construct(Batch $batch)
    {
        try {
            $this->batch = $batch;
            $this->dbf = new dbf_class($batch->getFilepath());
            $type_file = $this->get_type_of_file();
            if ($type_file == 'property') {
                $this->blocks = $this->doPropertyBlocks();
            } elseif ($type_file == 'list') {
                $this->blocks = $this->doListBlocks();
            } else {
                throw new Exception('Cannot identify type of file, return type was ' . $type_file);
            }

        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    public function __destruct() {

    }

    public function passErrors()  {
        if ($this->hasErrors()) {
            for($i =0; $i < sizeof($this->errors); $i++) {
                $this->batch->addError($this->errors[$i]);
            }
        }
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }


    public function getBlocks()
    {
        if ($this->hasErrors()) {
            throw new Exception('Blocks have errors, need to check them first');
        }
        return $this->blocks;
    }

    //returns either property or list
    public function get_type_of_file()
    {
        if ($this->hasErrors()) {
            throw new Exception('Blocks have errors, need to check them first');
        }
        if ($this->dbf->dbf_num_field == 4) {
            return 'property';
        }
        return 'list';
    }


    protected function addError($err)
    {
        array_push($this->errors, $err);
    }

    protected function doListBlocks()
    {
        $num_rec = $this->dbf->dbf_num_rec;
        for ($i = 0; $i < $num_rec; $i++) {
            $row = $this->dbf->getRow($i);
            for($m = 0; $m < sizeof($this->dbf->dbf_names);$m++) {
                $name = $this->dbf->dbf_names[$m]['name'];
                /** @noinspection PhpUnusedLocalVariableInspection */
                //todo type cast for at least date
                $type = $this->dbf->dbf_names[$m]['type'];
                $value = $row[$m];
                $this->batch->getRules()->addRow($name,$value);
            }
        }
        $this->batch->getRules()->finishUpBlocks();
        return $this->batch->getRules()->getWorkingBlocks();
    }

    protected function doPropertyBlocks()
    {

        $num_rec = $this->dbf->dbf_num_rec;
        for ($i = 0; $i < $num_rec; $i++) {
            $row = $this->dbf->getRow($i);
            if (empty($row[1])) {
                continue;
            } else {
                $w_unkwn = (empty($row[2])) ? $row[3] : $row[2];
                $value = ForceUTF8\Encoding::toUTF8($w_unkwn);
                $name = ForceUTF8\Encoding::toUTF8($row[1]);
                $this->batch->getRules()->addRow($name,$value);
            }
        }
        $this->batch->getRules()->finishUpBlocks();
        return $this->batch->getRules()->getWorkingBlocks();


    }
}


