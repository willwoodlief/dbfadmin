<?php
// written by will !
require_once 'dbf/dbf_class.php';

class Blocks {

    private  $errors = [];
    protected  $dbf = null;
    protected  $blocks = [];

    public function __construct($filepath) {
        try {
            $this->dbf = new dbf_class($filepath);
            $type_file = $this->get_type_of_file() ;
            if ($type_file == 'property' ) {
                $this->blocks = $this->doPropertyBlocks();
            } elseif ( $type_file == 'list') {
                $this->blocks = $this->doListBlocks();
            } else {
                throw new Exception('Cannot identify type of file, return type was '.$type_file );
            }
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    public function hasErrors() { return !empty($this->errors);}
    public function getErrors() { return $this->errors;}


    public function getBlocks() {
        if ($this->hasErrors()) {
            throw new Exception('Blocks have errors, need to check them first');
        }
        return $this->blocks;
    }

    //returns either property or list
    public function get_type_of_file() {
        if ($this->hasErrors()) {
            throw new Exception('Blocks have errors, need to check them first');
        }
        if ($this->dbf->dbf_num_field == 4) { return 'property'; }
        return 'list';
    }


    protected function addError($err) {array_push($this->errors,$err);}

    protected function doPropertyBlocks() {

        //we are going to go through each row and build up clusters of rows seperated out
        // by empty rows,
        $num_rec=$this->dbf->dbf_num_rec;
        $blocks = [];
        $this_block = [];
        $main_block = [];
        for($i=0; $i<$num_rec; $i++) {
            $row = $this->dbf->getRow($i);
            if (empty($row[1])) {
                if (sizeof($this_block) == 1) {
                    foreach ($this_block as $k=>$v) {
                        $main_block[$k] = $v;
                    }

                } else if(sizeof($this_block) == 0 ) {
                    continue;
                } else {
                    array_push($blocks,$this_block);
                }
                $this_block = [];
            } else {
                $w_unkwn = (empty($row[2])) ? $row[3] : $row[2];
                $w = ForceUTF8\Encoding::toUTF8($w_unkwn);
                $kk = ForceUTF8\Encoding::toUTF8($row[1]);
                $this_block[$kk] = $w;
            }




        }

        //now go through each of the $blocks, and put the children with the parents
        $node_list = [];

        $last_parent_index = -1;
        $last_type_block = false;
        for($i = 0; $i < sizeof($blocks); $i++) {
            $b = $blocks[$i];
            $type_block = determine_block_type($b,$last_type_block);
            $first_line = reset($b);
            foreach ($b as $k=>$v) {
                if (empty($k)) {
                    $first_line = $v;
                } else {
                    $first_line = $k;
                }
                break;
            }
            if (empty($first_line)) {
                throw new Exception("Empty for $i");
            }
            #has tile,siding,stone,paint,trim in the first line name, but not painting
            $words_for_children = ['tile','siding','stone','paint','trim'];
            $words_for_grownups = ['painting'];
            $b_is_child = false;
            foreach ($words_for_children as $word) {
                if (stripos($first_line, $word) !== false) {
                    $b_is_child = true;
                    break;
                }
            }
            if ($b_is_child) {
                foreach ($words_for_grownups as $word) {
                    if (stripos($first_line, $word) !== false) {
                        $b_is_child = false;
                        break;
                    }
                }
            } else {
                //if not a child , it might still be if it has the keys: brand, type, color
                $match_columns = ['brand'=>0,'type'=>0,'color'=>0];
                $count_matches = 0;
                foreach ($b as $k=>$v) {
                    foreach ($match_columns as $word=>$count) {
                        if ($count == 0) {
                            if (stripos($k, $word) !== false) {
                                $count_matches ++;
                                $match_columns[$word] += 1;
                            }
                        }

                    }
                }
                if ($count_matches == sizeof($match_columns)) { //matched all three columns
                    $b_is_child = true;
                }

            }



            if ($b_is_child) {
                if ($last_parent_index < 0) {
                    throw new Exception('Parent was null for the child');
                }
                $node = ['type'=>'xx','data'=>$b];
                $node_list[$last_parent_index]['children'][] = $node;
            } else {
                $node = ['type'=>'ii','data'=>$b,'children'=>[]];
                $node_list[] = $node;
                $last_parent_index = sizeof($node_list) -1;
            }



        }
        $ret = ['main'=>$main_block,'blocks'=>$node_list];
        return $ret;
    }


    protected function doListBlocks() {

    }
}