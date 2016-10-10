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
    private $defaultMainFlag = null; //the flag for active main
    private $remWorkingActiveFlag = null;
    protected $activeMain = null;
    protected $db = null;
    protected $rules = []; //the column table for that template

    protected $allFlags = [] ; //indexed by id
    protected $activeFlags = []; //indexed by flag name contains {flag,block}
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
              where c.wx_template_id = ? AND c.name_regex_alias is not null
              order by c.rank,c.name_regex_alias",
                    [$batch->getTemplate()->id]);

            $rulesO = $rulesQ->results();

            $this->rules = json_decode(json_encode($rulesO),false);

            //get flags
            $flagsQ = $this->db->query('select * from wx_template_flags where 1 ORDER BY name,priority ASC',[]);
            $flagsO = $flagsQ->results();
            $flags = json_decode(json_encode($flagsO),false);
            $this->defaultMainFlag = new stdClass();
            $this->defaultMainFlag->name = 'default main';
            $this->defaultMainFlag->priority = 0;
            $this->defaultMainFlag->id = 0;
            $this->defaultMainFlag->notes = 'created dynamically by rulesclass';
            array_unshift($flags,$this->defaultMainFlag);


            for($i=0; $i< sizeof($flags); $i++) {
                $this->allFlags[$flags[$i]->id] = $flags[$i];
            }

            for($i=0; $i< sizeof($this->rules); $i++) {
                if (isset( $this->rules[$i]->flag_to_raise)) {
                    $this->rules[$i]->flag_to_raise = $this->allFlags[intval($this->rules[$i]->flag_to_raise)];
                } else {
                    $this->rules[$i]->flag_to_raise = $this->defaultMainFlag; //assume null in db means default flag
                }

                if (isset($this->rules[$i]->flag_needed_a)) {
                    $this->rules[$i]->flag_needed_a = $this->allFlags[intval($this->rules[$i]->flag_needed_a)];
                }

                if (isset($this->rules[$i]->flag_needed_b)) {
                    $this->rules[$i]->flag_needed_b = $this->allFlags[intval($this->rules[$i]->flag_needed_b)];
                }

            }

            //start the active flags with the 0 priority default main
            $this->activeMain = $this->addActiveFlag($this->defaultMainFlag,null);



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
        // any poppped off active flags add their blocks to the parent block
        //for the new block add as parent the active block that is nearest but higher to the priority level


        $matches = $this->getMatches($key);
        if (sizeof($matches) == 0 ) {
            //add error
            $this->addError('not_found',$key);
        } elseif (sizeof($matches) > 1 ) {
            //add in conflicts

            //make array of ids in the matches array
            $conflicts = [];
            for($j=0;$j < sizeof($matches); $j++) {
                $id = $matches[$j]->id;
                array_push($conflicts,$id);
            }
            $this->addError('conflict',$key,$conflicts);
        } else {
            //is just one , see if this result is to be thrown out
            $rule = $matches[0];
            if ($rule->is_ignored != 0) {
                return; //done and nothing else needs to be done, ignored rules do not add to priority stack or have blocks
            }

            $this->remWorkingActiveFlag = $this->bumpAndProcessRaisedFlag($rule->flag_to_raise);
            $node = array('value'=> $value,'table'=>$rule->name_table,'column'=>$rule->name_column);
            $this->remWorkingActiveFlag->block['items'][$key] = $node;



        }
    }

    //pops off any active flags and forces all other blocks back to main block
    public function finishUpBlocks() {
        $this->remWorkingActiveFlag = $this->bumpAndProcessRaisedFlag($this->defaultMainFlag);
    }

    public function getWorkingBlocks() {
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->remWorkingActiveFlag->block;
    }

    private function bumpAndProcessRaisedFlag($flag_to_raise) {
        if (isset($flag_to_raise)) {
            $this->bumpFlags($flag_to_raise);
        } //end adding in an active flag

        //now add the key value pair to the items part of the working active flag block
        // the working active flag is the one with the highest priority
        $workingActiveFlag = $this->getWorkingActiveFlag(); //working active is returning the deleted parking

        return $workingActiveFlag;

    }

    //returns the current parent flag
    private function bumpFlags($flag_to_raise) {
        $event = $flag_to_raise;
        $parent_active_flag = $this->activeMain;
        //see if priority is lower or equal to any of the active rules
        $takeOut = [];
        for($k=0; $k < sizeof($this->activeFlags);$k++) {
            $flag = $this->activeFlags[$k]->flag;
            if ( ($event->priority <= $flag->priority) &&
                 ($flag->priority > 0) &&
                ( $event->id != $flag->id )
            ) { //basically make the initial 0  priority unbumpable
                //take out of active flags, and put block, if not empty into parent's children

                array_push($takeOut,$k);
            } else {
                if ($flag->priority < $event->priority) {
                    if (isset($parent_active_flag)) {
                        /** @noinspection PhpUndefinedFieldInspection */
                        if ($flag->priority > $parent_active_flag->flag->priority) {
                            $parent_active_flag = $this->activeFlags[$k];
                        }
                    } else {
                        $parent_active_flag = $this->activeFlags[$k];
                    }
                }
            }
        }

        for($k =0; $k < sizeof($takeOut);$k++) {
            $oldNode = array_splice($this->activeFlags,$takeOut[$k],1);
            $activeFlag = $oldNode[0];
            $block = $activeFlag->block;
            $flag = $activeFlag->flag;
            $name = $flag->db_hint_for_needed;
            if ($activeFlag->parent) {
                $node = ['name'=>$name,'block'=>$block];
                array_push($activeFlag->parent->block['children'],$node);
            }
        }

        //if the parent active flag is 0 then we want to give the original 0 flag instead
        if ($parent_active_flag->flag->priority == 0) {
            $parent_active_flag = $this->activeMain;
        }

        //add in new active flag
        $newActiveFlag = $this->addActiveFlag($event,$parent_active_flag);
        return $newActiveFlag;
    }

    private function getWorkingActiveFlag() {
        $workingActiveFlag = null;
        $hi = -100;
        for($k=0; $k < sizeof($this->activeFlags);$k++) {
            $flag = $this->activeFlags[$k]->flag;
            if ($flag->priority > $hi) {
                $hi = $flag->priority;
                $workingActiveFlag = $this->activeFlags[$k];
            }
        }
        return $workingActiveFlag;
    }

    private function addActiveFlag($flag,$parent) {
        //only add if active flag is not there for that flag
        for($k=0; $k< sizeof($this->activeFlags);$k++) {
            $test = $this->activeFlags[$k];
            if ($test->flag->id == $flag->id) {
                return $test;
            }
        }
        $activeFlag = new stdClass();
        $activeFlag->flag = $flag;
        $activeFlag->block = ['items'=>[],'children'=>[]];
        $activeFlag->parent = $parent;
        array_push($this->activeFlags,$activeFlag);
        return $activeFlag;
    }

    private function getActiveFlagIDs() {
        $ret = [];
        for($k=0; $k < sizeof($this->activeFlags);$k++) {
            $flagID = $this->activeFlags[$k]->flag->id;
            array_push($ret,$flagID);
        }
        return $ret;
    }


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

    private function getMatches($key) {
        //go through each rule in the rules array and see what matches return as array of rules
        //get Active Flag ids and put in array for easy access, they change all the time do don't store them
        $activeFlagIDs = $this->getActiveFlagIDs();
        $matches = [];
        for($k=0;$k<sizeof($this->rules); $k++) {
            $rule = $this->rules[$k];
            $pattern = preg_quote($rule->name_regex_alias, '/');
            if (preg_match('/'.$pattern.'/', $key))     {
                //possible match, check for needed context
                if (isset($rule->flag_needed_a)) {
                    //if not in activeflagids then next
                    if (! in_array($rule->flag_needed_a->id,$activeFlagIDs)){
                        continue;
                    }
                }
                if (isset($rule->flag_needed_b)) {
                    //if not in activeflagids then next
                    if (! in_array($rule->flag_needed_b->id,$activeFlagIDs)){
                        continue;
                    }
                }
                //if we got here, its a match, so add it
                array_push($matches,$rule);
            }
        } //end loop for all matches
        return $matches;

    }
}