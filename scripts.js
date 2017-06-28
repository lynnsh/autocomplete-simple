window.onload = init;

/* Works with Ajax to populate autocomplete request
 * using information from the server side
 */
function init() {
    onSubmit();
    $("#city").keyup(function(e){
        var key = $('#city').val(); 
        $.ajax({
            url:"ajax.php",
            data: "city="+key,
            type: "GET",
            dataType: "json",
            success: function(json) {  
                $("#city").autocomplete({ 
                    source: json,
                    select: function(event, ui) {
                        onSelect(ui);
                    }
                });               
            } 
        });    
    });
}

/* If the user selected the city, it will make it visible. */
function onSubmit() {
    var value = document.forms["search"]["city"].value.trim();
    if(value !== "")
        $("#list").css('display', 'block');
    else
        $("#list").css('display', 'none');
}

/* Displays the user's choice when a city is selected
 * from the autocomplete menu.
 * Sends to the server the selected city.
 */
function onSelect(ui) {
    var selected = ui.item.value;
    $("#list").css('display', 'block');
    $("#list").html("<h3 id='choice'>Your choice:</h3><p>" 
                    + selected + "</p>");
    $.ajax({
        url:"ajax.php",
        data: "selected="+selected,
        type: "GET",
        dataType: "json"
    });
}

