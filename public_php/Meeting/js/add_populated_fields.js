// var loopvar = {{ loopvar }};
$(document).ready(function() {
    var i = 1;


    $('#add').click(function () {

        i++;
        $('#dynamic_field').append('<tr id="row' + i + '"><td class="non-days"><input type="text" name="name[]" placeholder="Enter user Name" class="form-control name_list" /></td><td class="non-days"><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>');
    });
    if(loopvar >1){
        $('#dynamic_field').append('<tr id="row' + 2 + '"><td class="non-days"><input type="text" name="name[]" value= {{ userData[1][2] }} class="form-control name_list" /></td><td class="non-days"><button type="button" name="remove" id="' + 2 + '" class="btn btn-danger btn_remove">X</button></td></tr>');

    }
    if(loopvar >2){
        $('#dynamic_field').append('<tr id="row' + 3 + '"><td class="non-days"><input type="text" name="name[]" value= {{ userData[2][2] }} class="form-control name_list" /></td><td class="non-days"><button type="button" name="remove" id="' + 3 + '" class="btn btn-danger btn_remove">X</button></td></tr>');
    }
    if(loopvar >3){
        $('#dynamic_field').append('<tr id="row' + 4 + '"><td class="non-days"><input type="text" name="name[]" value= {{ userData[3][2] }} class="form-control name_list" /></td><td class="non-days"><button type="button" name="remove" id="' + 4 + '" class="btn btn-danger btn_remove">X</button></td></tr>');
    }
    if(loopvar >4){
        $('#dynamic_field').append('<tr id="row' + 5 + '"><td class="non-days"><input type="text" name="name[]" value= {{ userData[4][2] }} class="form-control name_list" /></td><td class="non-days"><button type="button" name="remove" id="' + 5 + '" class="btn btn-danger btn_remove">X</button></td></tr>');
    }
    //     for (i = 1; i < loopvar; i++){
    //             $('#dynamic_field').append('<tr id="row' + i + '"><td><input type="text" name="name[]" value= "' + userlist[i][2] + '" class="form-control name_list" /></td><td><button type="button" name="remove" id="' + i + '" class="btn btn-danger btn_remove">X</button></td></tr>');
    // }


    $('#remove').click(function(){
        i++;
        $('#dynamic_field').remove();
    });
    $(document).on('click', '.btn_remove', function(){
        var button_id = $(this).attr("id");
        $('#row'+button_id+'').remove();
    });

});

function GetDynamicTextBox(value)
{
    return '<input name = "DynamicTextBox"  class="dynamic"  type="text" value = "' + "mymonthname" + '" />' +
        '<input name = "DynamicTextBox1"  class="dynamic"  type="text" value = "' + "myYearname" + '" />' +
        '<input type="button" value="Remove" onclick = "RemoveTextBox(this)" />'
}

function AddTextBox()
{
    var div = document.createElement('DIV');
    div.innerHTML = GetDynamicTextBox("");
    document.getElementById("TextBoxContainer").appendChild(div);
}