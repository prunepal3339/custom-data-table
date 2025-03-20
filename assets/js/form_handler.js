jQuery(document).ready(function($) {
    const showFlashMessage = (message, type) => {
        // remove existing flash message
        $(".flash-message").remove();
        if ($("#flash-container").length === 0) {
            $("body").prepend('<div id="flash-container"></div>');
        }
        $("#flash-container").css({
            "position": "fixed",
            "top": "0",
            "left": "0",
            "width": "100%",
            "text-align": "center",
            "z-index": "1000"
        });
        // Create new flash message
        let flashMessage = $("<div></div>", {
            class: "flash-message " + type,
            html: message
        }).css({
            "position": "absolute",
            "top": "10px",
            "right": "10px",
            "padding": "10px 20px",
            "color": "white",
            "border-radius": "5px",
            "z-index": "1000",
            "font-size": "16px",
            "box-shadow": "0px 4px 6px rgba(0, 0, 0, 0.1)"
        });

        if (type == 'success') {
            flashMessage.css({
                "background-color": "green"
            });
        } else {
            flashMessage.css({
                "background-color": "red"
            });
        }

        $("#flash-container").append(flashMessage);
        
        flashMessage.fadeIn();
        setTimeout(function(){
            flashMessage.fadeOut(function(){
                $(this).remove();
            });
        }, 3000);
    };
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
        }).done(function (response) {
            if (response.success) {
                document.getElementById(ajaxObject.formId).reset();
                showFlashMessage(response.data.message, 'success');
            }
            else
                showFlashMessage(response.data.message || response.data, 'failure');
        }).fail(function(){
            alert('Failed to submit form!');
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