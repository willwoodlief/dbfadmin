function ThisFrameSlickgridSetup() {

  this.get_delete_message = function(id,stuffToDelete) {
      return 'do you want to delete the rule ' + stuffToDelete.name_regex_alias + ' ?';
  }  ;

  this.setup_delete_object = function(my_object){
      my_object.action = 'delete';
      my_object.table_name = 'block';
  };

    this.setup_add_object = function(my_object){
        my_object.action = 'new';
        my_object.table_name = 'block';
    };

    this.setup_edit_object = function(my_object){
        my_object.action = 'edit';
        my_object.table_name = 'block';
    };

    this.get_ajax_url = function() {
        return 'ajax_aliases.php';
    };

    this.post_process_data = function(field,column_data,server_response,cell_data) {
        return false;  //no changes so do not refresh row on the grid
    };

    this.get_options_for_cell_select = function(column,item) {
        return false; //we do not dynamically generate the drop down list for a cell
    };

  this.get_columns = function(myFormatterObject,myValidatorObject,mySorterObject) {
      return [
          {
              id: "name_regex_alias",
              name: 'Pattern for Name',
              field: "name_regex_alias",
              width: 250,
              minWidth: 40,
              editor: Slick.Editors.Text,
              validator: myValidatorObject.remoteCallValidator,
              formatter: null,
              sortable: true
          },  //
          {
              id: "name_table",
              name: 'Table Name',
              field: "name_table",
              width: 200,
              minWidth: 100,
              editor: myFormatterObject.SelectCellEditor,
              formatter: myFormatterObject.selectFormatter,
              validator: myValidatorObject.remoteCallValidator,
              options: options_for_table_names,
              //sorter: mySorterObject.SelectNameSorter,
              sortable: true


          },
          {
              id: "context_table",
              name: 'Context Table Name',
              field: "context_table",
              width: 200,
              minWidth: 100,
              editor: myFormatterObject.SelectCellEditor,
              formatter: myFormatterObject.selectFormatter,
              validator: myValidatorObject.remoteCallValidator,
              options: options_for_table_names,
              sortable: true
          },

          {
              id: "is_ignored",
              name: 'Is Ignored',
              field: "is_ignored",
              width: 100,
              minWidth: 100,
              formatter: myFormatterObject.MyCheckBoxFormatter,
              editor: myFormatterObject.MyCheckBoxEditor,
              validator: myValidatorObject.remoteCallValidator,
              sortable: true
          },


          {
              id: "delete_row",
              name: 'Delete',
              field: "delete_row",
              width: 60,
              minWidth: 40,
              cssClass: "cell-title",
              formatter: myFormatterObject.xFormatter
          }
      ];

  }

};