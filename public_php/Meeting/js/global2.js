$('input#name-submit').on('click',function(){
    var name = $('input#name').val();

    if($.trim(name) != ''){
        $.post('../schedulechecker', {
                name: name
            },
            function(data){
                $('div#name-data').text(data);

            }
        );
    }

})
