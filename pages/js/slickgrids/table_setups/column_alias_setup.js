function ThisFrameSlickgridSetup() {

  this.get_delete_message = function(id,stuffToDelete) {
      return 'do you want to delete the rule ' + stuffToDelete.name_regex_alias + ' ?';
  }  ;

  this.setup_delete_object = function(my_object){
      my_object.action = 'delete';
      my_object.table_name = 'column';
  };

    this.setup_add_object = function(my_object){
        my_object.action = 'new';
        my_object.table_name = 'column';
        my_object.template_id = template_id;
    };

    this.setup_edit_object = function(my_object){
        my_object.action = 'edit';
        my_object.table_name = 'column';
    };

    this.get_ajax_url = function() {
        return 'ajax_aliases.php';
    };

    this.row_click = function(data,row,cell) {

    };

    this.post_process_data = function(field,all_column_data,server_response,cell_data) {
        return false;
        //if we are going to do something , we can change the data or the column options
        //and force a refresh of the row by returning true

    };

    this.get_options_for_cell_select = function(column,item) {
        if (column.field != 'name_column') {
            return false;
        }
        //get the table name
        var table_name = item.name_table;
        if (!options_for_columns[table_name]) {
            console.log("could not find options for table " +table_name );
            return false;
        }
        return options_for_columns[table_name];
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
              width: 250,
              minWidth: 100,
              editor: myFormatterObject.SelectCellEditor,
              formatter: myFormatterObject.selectFormatter,
              validator: myValidatorObject.remoteCallValidator,
              options: options_for_table_names,
              //sorter: mySorterObject.SelectNameSorter,
              sortable: true


          },
          {
              id: "name_column",
              name: 'Column Name',
              field: "name_column",
              width: 250,
              minWidth: 100,
              editor: myFormatterObject.SelectCellEditor,
              formatter: myFormatterObject.selectFormatter,
              validator: myValidatorObject.remoteCallValidator,
              options: '',
              //sorter: mySorterObject.SelectNameSorter,
              sortable: true


          },

          {
              id: "flag_to_raise",
              name: 'Flag to Raise',
              field: "flag_to_raise",
              width: 100,
              minWidth: 20,
              editor: myFormatterObject.SelectCellEditor,
              formatter: myFormatterObject.selectFormatter,
              validator: myValidatorObject.remoteCallValidator,
              options: options_for_flags,
              //sorter: mySorterObject.SelectNameSorter,
              sortable: true


          },

          {
              id: "flag_needed_a",
              name: 'Context',
              field: "flag_needed_a",
              width: 100,
              minWidth: 20,
              editor: myFormatterObject.SelectCellEditor,
              formatter: myFormatterObject.selectFormatter,
              validator: myValidatorObject.remoteCallValidator,
              options: options_for_flags,
              //sorter: mySorterObject.SelectNameSorter,
              sortable: true


          },

          {
              id: "flag_needed_b",
              name: 'Other Context',
              field: "flag_needed_b",
              width: 100,
              minWidth: 20,
              editor: myFormatterObject.SelectCellEditor,
              formatter: myFormatterObject.selectFormatter,
              validator: myValidatorObject.remoteCallValidator,
              options: options_for_flags,
              //sorter: mySorterObject.SelectNameSorter,
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
              id: "rank",
              name: 'Rank',
              field: "rank",
              width: 50,
              minWidth: 50,
              editor: Slick.Editors.Text,
              validator: myValidatorObject.remoteCallValidator,
              formatter: null,
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

}