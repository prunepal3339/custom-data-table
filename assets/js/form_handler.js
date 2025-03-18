jQuery(document).ready(function($) {
    $( "#" + ajaxObject.formId ).on('submit', function(event) {
        event.preventDefault();
        let formData = $(this).serializeArray();
        
        $.ajax({
            type: 'POST',
            url: ajaxObject.adminUrl,
            data: {
                formData: objectify(formData),
                action: ajaxObject.actionParam,
            },
        }).done(function (data) {
                console.log("Success: " + data);
        }).fail(function(){
            console.log('I am error please!');
        });
    });
});
function objectify(formData) {
    let result = {};
    for (let {name, value} of formData) {
        result[name] = value; 
    }
    return result;
}