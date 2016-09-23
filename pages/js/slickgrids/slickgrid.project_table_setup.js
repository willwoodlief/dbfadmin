function SlickSetup(setup) {

    this.setup = setup;

    this.getColumnsForSlickgrid=function(myFormatterObject,myValidatorObject,mySorterObject) {
        return this.setup.get_columns(myFormatterObject,myValidatorObject,mySorterObject);
    };

    this.getGridRowHeight = function() {
        return 25;
    };

    this.getShowTopPanel = function() {
        return false;
    };

    this.getSearchKeywordColumn = function() {
        return 'none_used';
    };

    this.getSearchKeywordColumn2 = function() {
        return 'none_used_2';
    };

    this.goesGridUseContextMenu = function() {
        return false;
    };

    this.onContextMenuItemClicked = function(data, action) {
       // var id = data.id;

    };

    this.getContextData = function() {
        return table_data;
    };

    this.ContextWasClicked = function(data, row,cell) {
        if (!data) {
            return;
        }


    };

    this.confirm_deletion = function (id,stuffToDelete) {
        //do nothing here, but return true to say its okay to delete
        var delete_message = this.setup.get_delete_message(id,stuffToDelete);
        var r = confirm(delete_message);
        return r;
    };

    this.contextDataChanged = function(item, field, new_field_value) {
       // var d = item[field];

    };

    this.isCellEditable = function(row,cell,item) {
        if (item) {
            return true;
        }
        return false;
    };

    this.confirm_field_change = function(field,value,cell_data) {
        return true; //ok for all changes
    };

    this.get_options_for_cell_select = function(column,item){
      return this.setup.get_options_for_cell_select(column,item);
    };

//this is set up to be called by the rails controller as it sets up the data, coordinated with the xformatter,or activeFormatter
    this.delete_row_on_server = function(id, all_data, stuffToDelete, afterEffect, noEffect) {
        //do ajax call to delete and then if successful delete the row with afterEffect, or if error show that with noEffect
        var my_object = jQuery.extend(true, {}, {});
        my_object.id = id;
        my_object.param = stuffToDelete;
        this.setup.setup_delete_object(my_object);
        $.ajax({
            type: "POST",
            url: this.setup.get_ajax_url(),
            data: my_object,
            async: true,
            success: function(data) {
                if (data.valid) {
                    afterEffect(data.id, data.message);

                } else {
                    noEffect(data.id, 'there was an error deleting data', data.message);
                }

            }

        })
            .fail(function() {
                noEffect(id, 'error deleting',  ' Connection Error');
            })
        ;


    };

    // this cannot be async because we need a return from this function
    this.remote_call_change = function(field, cell_data, value,all_column_data) {

        //update the information
        var message = '';
        var myret = false;
        var my_object = jQuery.extend(true, {}, {});
        my_object.id = cell_data.id;
        my_object.param = cell_data;
        my_object.param[field] = value;
        my_object.changed_field = field;
        this.setup.setup_edit_object(my_object);
        var b_do_refresh = false;
        var setup = this.setup;
        $.ajax({
            type: "POST",
            url: this.setup.get_ajax_url(),
            data: my_object,
            async: false,
            success: function(data) {
                if (data.valid) {
                    myret = true;
                    if (data.set_is_active) {
                        cell_data.is_active= data.set_is_active;
                        b_do_refresh = cell_data;
                    }
                    var is_changed = setup.post_process_data(field,all_column_data,data,cell_data);
                    if (is_changed) {
                        b_do_refresh = cell_data;
                    }
                }
                if (data.message) {
                    message = data.message;
                }
            }
        })
            .fail(function() {
                message = 'Could not connect';
            })
        ;

        //return valid false to prevent data being updated in table
        return {valid: myret, msg: message,refresh:b_do_refresh};

    };

//more than one class can be returned, seperate by a space for each . but this is not implemented
    this.get_class_for_field = function(field, value, row_data) {
        return '';
    };

    this.do_field_toggle = function(id, field, row_data,callback) {



        // {valid: true, message: 'ok', title: '?##$#$?', data: row_data};
    };

    this.add_row_on_server = function(row_data, callback, fail_callback) {
        var my_object = jQuery.extend(true, {}, row_data);
        my_object.action = 'new';
        my_object.table_name = 'block';
        this.setup.setup_add_object(my_object);
        $.ajax({
            type: "POST",
            url: this.setup.get_ajax_url(),
            data: my_object,
            async: true,
            success: function(data) {
                if (data.valid) {
                    //add delete column in
                    for (var i = 0; i < data.data_array.length; i++) {
                        data.data_array[i].delete_row = true;
                    }
                    callback(data);
                } else {
                    fail_callback(data);
                }

            }

        })
            .fail(function() {
                fail_callback({message:'connection error',title:"Error"});
            })
        ;




    };

//types alert,information,error,warning,notification,success
    this.show_error_message = function(message, type) {
        $(".main-header").noty({
            text: message,
            type: type,
            dismissQueue: true,
            layout: 'top',
            theme: 'defaultTheme',
            timeout: 20000
        });
    };

}

