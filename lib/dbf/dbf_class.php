<?php
require_once 'file_byte_reader.php';

/************************************************************
Below was updated by Will Woodlief to read from the file, and not fill up memory with the entire file
 *  these changes use a helper class the does random access to file in birary mode
 *  all I did was to substitute reading from an in memory array to reading from the class

  DBF reader Class v0.04  by Faro K Rasyid (Orca)
orca75_at_dotgeek_dot_org
v0.05 by Nicholas Vrtis
vrtis_at_vrtisworks_dot_com
1) changed to not read in complete file at creation.
2) added function to read individual rows
3) added support for Memo fields in dbt files.
4) See: http://www.clicketyclick.dk/databases/xbase/format/dbf.html#DBF_STRUCT
   for some additional information on XBase structure...
5) NOTE: the whole file (and the memo file) is read in at once.  So this could
   take a lot of memory for large files.

Input		: name of the DBF( dBase III plus) file
Output	:	- dbf_num_rec, the number of records
			- dbf_num_field, the number of fields
			- dbf_names, array of field information ('name', 'len', 'type')

Usage	example:
$file= "your_file.dbf";//WARNING !!! CASE SENSITIVE APPLIED !!!!!
$dbf = new dbf_class($file);
$num_rec=$dbf->dbf_num_rec;
$num_field=$dbf->dbf_num_field;

for($i=0; $i<$num_rec; $i++){
    $row = $dbf->getRow($i);
	for($j=0; $j<$num_field; $j++){
		echo $row[$j].' ');
	}
	echo('<br>');
}

Thanks to :
- Willy
- Miryadi

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU  Lesser General Public License for more details.
  
**************************************************************/ 
class dbf_class {
		
    var $dbf_num_rec;           //Number of records in the file
    var $dbf_num_field;         //Number of columns in each row
    var $dbf_names = array();   //Information on each column ['name'],['len'],['type']
    //These are private....
    private  $_rowsize;           //Length of each row
    private $_hdrsize;           //Length of the header information (offset to 1st record)
    private $_memos;             //The raw memo file (if there is one).

    private $_reader;  //the file byte reader added by will

    function dbf_class($filename) {

        $tail='.dbf';  # it used to read the filename, but now this is a temp file without an extension
        $this->_reader = new FileByteReader($filename); //throws exception if cannot open the file and get a size from it
        //Make sure that we indeed have a dbf file...
        $filesize = $this->_reader->getFileSize();
        $first_32_bytes = $this->_reader->getBytes(0,32);
        $last_byte = $this->_reader->getBytes($filesize - 1,1);
        if(!(ord($first_32_bytes[0]) == 3 || ord($first_32_bytes[0]) == 131) && ord($last_byte[0]) != 26) {
            throw new Exception("Not a valid DBF file !!!");
        }
        // 3= file without DBT memo file; 131 ($83)= file with a DBT.
        $arrHeaderHex = array();
        for($i=0; $i<32; $i++){
            $arrHeaderHex[$i] = str_pad(dechex(ord($first_32_bytes[$i]) ), 2, "0", STR_PAD_LEFT);
        }
        //Initial information
        $line = 32;//Header Size
        //Number of records
        $this->dbf_num_rec=  hexdec($arrHeaderHex[7].$arrHeaderHex[6].$arrHeaderHex[5].$arrHeaderHex[4]);
        $this->_hdrsize= hexdec($arrHeaderHex[9].$arrHeaderHex[8]);//Header Size+Field Descriptor
        //Number of fields
        $this->_rowsize = hexdec($arrHeaderHex[11].$arrHeaderHex[10]);
		$this->dbf_num_field = floor(($this->_hdrsize - $line ) / $line ) ;//Number of Fields
				
        //Field properties retrieval looping
        //get the bytes in the field propeties
        $property_bytes = $this->_reader->getBytes(32,$this->dbf_num_field* $line);
        for($j=0; $j<$this->dbf_num_field; $j++){
            $name = '';
            $beg = $j*$line;

            for($k=$beg; $k<$beg+11; $k++){

                if(ord($property_bytes[$k])!=0){
                    $name .= $property_bytes[$k];
                }

            }
            $this->dbf_names[$j]['name']= $name;//Name of the Field
            $this->dbf_names[$j]['len']= ord($property_bytes[$beg+16]);//Length of the field
            $this->dbf_names[$j]['type']= $property_bytes[$beg+11];
        }
        if (ord($first_32_bytes[0])==131) { //See if this has a memo file with it...
            //Read the File
            $tail=substr($tail,-1,1);   //Get the last character...
            if ($tail=='F'){            //See if upper or lower case
                $tail='T';              //Keep the case the same
            } else {
                $tail='t';
            }
            $memoname = substr($filename,0,strlen($filename)-1).$tail;
            $handle = fopen($memoname, "r");
            if (!$handle) { throw new Exception("Cannot read DBT file, there was a memo attached buts it not here with the filename expected"); }
            $filesize = filesize($memoname);
            $this->_memos = fread ($handle, $filesize);
            fclose ($handle);
        }
    }
    
    function getRow($recnum) {
        if ($recnum > $this->dbf_num_rec) {
            throw new Exception("Asked for a row that does not exist in the dbf file, asked for $recnum but the number of rows is $this->dbf_num_rec");
        }
        $memoeot = chr(26).chr(26);
        $rawrow = $this->_reader->getBytes($recnum*$this->_rowsize+$this->_hdrsize,$this->_rowsize);
        $rowrecs = array();
        $beg=1;
        if (ord($rawrow[0])==42) {
            return false;   //Record is deleted...
        }
        for ($i=0; $i<$this->dbf_num_field; $i++) {
            $col=trim(substr($rawrow,$beg,$this->dbf_names[$i]['len']));
            if ($this->dbf_names[$i]['type']!='M') {
                $rowrecs[]=$col;
            } else {
                $memobeg=$col*512;  //Find start of the memo block (0=header so it works)
                $memoend=strpos($this->_memos,$memoeot,$memobeg);   //Find the end of the memo
                $rowrecs[]=substr($this->_memos,$memobeg,$memoend-$memobeg);
            }
            $beg+=$this->dbf_names[$i]['len'];
        }
        return $rowrecs;
    }
    
    function getRowAssoc($recnum) {
        $memoeot = chr(26).chr(26);
        $rawrow = $this->_reader->getBytes($recnum*$this->_rowsize+$this->_hdrsize,$this->_rowsize);
        $rowrecs = array();
        $beg=1;
        if (ord($rawrow[0])==42) {
            return false;   //Record is deleted...
        }
        for ($i=0; $i<$this->dbf_num_field; $i++) {
            $col=trim(substr($rawrow,$beg,$this->dbf_names[$i]['len']));
            if ($this->dbf_names[$i]['type']!='M') {
                $rowrecs[$this->dbf_names[$i]['name']]=$col;
            } else {
                $memobeg=$col*512;  //Find start of the memo block (0=header so it works)
                $memoend=strpos($this->_memos,$memoeot,$memobeg);   //Find the end of the memo
                $rowrecs[$this->dbf_names[$i]['name']]=substr($this->_memos,$memobeg,$memoend-$memobeg);
            }
            $beg+=$this->dbf_names[$i]['len'];
        }
        return $rowrecs;
    }
}//End of Class






