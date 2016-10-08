<?php

class Batch
{

    private $errors = [];
    protected $db = null;
    protected $batch_id = false; //the column table for that template
    protected  $template = false;
    protected  $rules = null;
    protected $blocks = null;
    protected $filepath = null;
    protected $filename = null;

    public function __construct( $template,$filepath,$org_filename,User $user)
    {
        try {
            $this->db = DB::getInstance();
            $check = $this->db->insert('wx_batch',[
                            'app_user_id' => $user->data()->id,
                            'wx_template_id'=>$template->id,
                            'original_filename' => $org_filename,
                            'created_at_ts'=>time()
            ]);

            if (!$check) {
                throw new Exception('Could not create new batch ', $this->db->error_info());
            }
            $this->batch_id = $this->db->lastId();
            $this->template = $template;
            $this->filepath = $filepath;
            $this->filename = $org_filename;

            $this->rules = new Rules($this);
            $this->blocks = new Blocks($this);
            $this->blocks->passErrors(); //adds the errors to this class
            $this->rules->passErrors();

        } catch (Exception $e) {
            $this->addError($e->getMessage());
            throw $e;
        }
    }

    public function __destruct() {



        if ($this->hasErrors()) {
            $error_message = implode(';',$this->errors);
            $bError = 1;
        } else {
            $error_message = null;
            $bError = 0;
        }
        if ($this->batch_id) {
            $update_hash = ['finished_upload_at_ts'=>time(),'is_error'=>$bError,'error_message'=>$error_message];
            $this->db->update('wx_batch',$this->batch_id,$update_hash);
        }
    }


    public function getID() { return $this->batch_id;}
    public function hasErrors() { return !empty($this->errors);}
    public function getErrors() { return $this->errors;}
    public function addError($err) {array_push($this->errors,$err);}
    public function getTemplate() { return $this->template;}
    public function getBlocks() { return $this->blocks;}
    public function getRules() { return $this->rules;}
    public function getFilepath() { return $this->filepath;}
    public function getFilename() { return $this->filename;}
}