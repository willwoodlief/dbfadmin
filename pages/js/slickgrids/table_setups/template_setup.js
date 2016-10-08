$(function() {
    $('#set-template-for-upload').change(function() {
        var val = $(this).val();
        parent.set_template_for_upload(val);
    });
});

function ThisFrameSlickgridSetup() {

    this.rem_row_id = 0;

  this.get_delete_message = function(id,stuffToDelete) {
      return 'do you want to delete the template ' + stuffToDelete.name + ' ?';
  }  ;

  this.setup_delete_object = function(my_object){
      my_object.action = 'delete';
  };

    this.setup_add_object = function(my_object){
        my_object.action = 'new';
    };

    this.setup_edit_object = function(my_object){
        my_object.action = 'edit';
        my_object.table_name = 'column';
    };

    this.get_ajax_url = function() {
        return 'ajax_templates.php';
    };

    this.post_process_data = function(field,all_column_data,server_response,cell_data) {
        return false;
        //if we are going to do something , we can change the data or the column options
        //and force a refresh of the row by returning true

    };

    this.row_click = function(data,row,cell) {
        //get the table name
        if (cell != 0) {return;}
        var this_template_id = data.id;
        if (this.rem_row_id == this_template_id) {return;}
        this.rem_row_id = this_template_id;
        parent.template_changed(this.rem_row_id);
    };

    this.get_options_for_cell_select = function(column,item) {

        return false;

    };

  this.get_columns = function(myFormatterObject,myValidatorObject,mySorterObject) {
      return [

          {
              id: "name",
              name: 'Template Name',
              field: "name",
              width: 250,
              minWidth: 40,
              editor: Slick.Editors.Text,
              validator: myValidatorObject.remoteCallValidator,
              formatter: null,
              sortable: true
          },  //
          {
              id: "notes",
              name: 'Notes',
              field: "notes",
              width: 750,
              minWidth: 100,
              editor: Slick.Editors.LongText,
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