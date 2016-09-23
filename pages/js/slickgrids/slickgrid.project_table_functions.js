var grid_var = null;



function create_new_row() {
    grid_var.addNewRow({});
    return false;
}


$(function() {

    var this_frame_slickgrid = new ThisFrameSlickgridSetup();
    var opts = {
        slick_setup: new SlickSetup(this_frame_slickgrid),
        gridID : 'myGrid',
        pageID : false,
        search_id_array : [],
        context_menu_id : false,
        inline_panel_id : false

    };

    grid_var = new SlickGridBoilerplate(opts);


});