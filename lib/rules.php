<?php

class Rules
{

    /*
     * errors happen because more than one rule matches
     * or because there are things that do not match at all
     * so each error has type (conflict, not found),key, conflicts (array)
     *
     */
    private $errors = [];
    private $errorStats = [];
    protected $db = null;
    protected $rules = []; //the column table for that template

    protected $allFlags = [] ; //indexed by id
    protected $blocks = []; //each block has name,priority and nodes
    protected $activeFlags = []; //indexed by flag name contains index in the all flags table for the flag table for that flagname
    protected $batch = null;

    public function __construct(Batch $batch)
    {
        try {
            $this->db = DB::getInstance();
            $this->batch = $batch;

            //get rules
            /** @noinspection PhpUndefinedFieldInspection */
           $rulesQ = $this->db->query("
              SELECT c.name_regex_alias,c.name_table, c.name_column, c.is_ignored,
              c.id , c.flag_to_raise,c.flag_needed_a,c.flag_needed_b
              from wx_template_columns c
              where c.wx_template_id = ?
              order by c.rank,c.name_regex_alias",
                    [$batch->getTemplate()->id]);

            $rulesO = $rulesQ->results();

            $this->rules = json_decode(json_encode($rulesO),false);

            //get flags
            $flagsQ = $this->db->query('select * from wx_template_flags where 1 ORDER BY name,priority ASC',[]);
            $flagsO = $flagsQ->results();
            $flags = json_decode(json_encode($flagsO),false);
            for($i=0; $i< sizeof($flags); $i++) {
                $this->allFlags[$flags[$i]->id] = $flags[$i];
            }

            for($i=0; $i< sizeof($this->rules); $i++) {
                $this->rules->flag_to_raise = $this->allFlags[$this->rules->flag_to_raise];
                $this->rules->flag_needed_a = $this->allFlags[$this->rules->flag_needed_a];
                $this->rules->flag_needed_b = $this->allFlags[$this->rules->flag_needed_b];
            }


        } catch (Exception $e) {
            $this->batch->addError( $e->getMessage());
        }
    }

    public function __destruct() {

    }

    public function passErrors() {
        if ($this->hasErrors()) {
            #add to batch and then add to the template errors
            for($i =0; $i < sizeof($this->errors); $i++) {
                $this->batch->addError( $this->errorToString( $this->errors[$i]) );
            }

            for($i =0; $i < sizeof($this->errors); $i++) {
                $err = $this->errors[$i];
                $check = $this->db->insert('wx_template_errors',[
                    'wx_batch_id' => $this->batch->getID(),
                    'type_error'=>$err['type'],
                    'dbf_key' => $err['key'],
                    'message'=> implode(',',$err['conflicts'])
                ]);

                if (!$check) {
                    $this->batch->addError( 'Could not create new  wx_template_error '. $this->db->error_info() );
                    break;
                }
            }

        }
    }

    public function addRow($key,$value) {
        //go through all the rules and see what matches,
            // and if matches if
            //   see if ignored
            //   see if needs an active flag found in $tis->activeFlags

        //if there is no matches it is an error of missing
        // if there is more than one result is an error to too many

        //if this has a flag to add, then pop off the active flags of equal to or lesser priority

        // start a new flag block which to add this and any other match that has only this priority levvel
    }

    public function getBlocks() { return $this->blocks;}
    public function hasErrors() { return !empty($this->errors);}
    public function getErrors() { return $this->errors;}
    public function getErrorStats() { return $this->errorStats;}


    protected function addError( $errorType,$key,$conflicts=[]){
        // type (conflict, not found),key, conflicts
        if (empty($this->errorStats[$key])) {
            $error = ['type' => $errorType, 'key' => $key, 'conflicts' => $conflicts];
            array_push($this->errors, $error);
            $this->errorStats[$key] = 1;
        } else {
            $this->errorStats[$key] ++;
        }
    }
    protected function errorToString($err) {
        $conflicts = implode(',',$err['conflicts']);
        return $err['type'] . ': ' . $err['key'] . ' ' . $conflicts;
    }
}