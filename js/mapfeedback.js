function addFeedbackSuccess(form, data) {
    addElementClass('add_feedback_form', 'hidden');
    paginator.updateResults(data);
    var messageid = 'message';
    if (data.fieldnames && data.fieldnames.message) {
        messageid = data.fieldnames.message;
    }
    $('add_feedback_form_' + messageid).value = '';
    rewriteCancelButtons();
}

function rewriteCancelButtons() {
    if ($('add_feedback_form')) {
        var buttons = getElementsByTagAndClassName('input', 'cancel', 'add_feedback_form');
        // hashed field names on anon forms mean we don't know the exact id of this button
        var idprefix = 'cancel_add_feedback_form_';
        forEach(buttons, function(button) {
            if (getNodeAttribute(button, 'id').substring(0, idprefix.length) == idprefix) {
                disconnectAll(button);
                connect(button, 'onclick', function (e) {
                    e.stop();
                    addElementClass('add_feedback_form', 'hidden');
                    return false;
                });
            }
        });
    }
}

addLoadEvent(function () {
    if ($('add_feedback_form')) {
        if ($('add_feedback_link')) {

            var isIE6 = document.all && !window.opera &&
                (!document.documentElement || typeof(document.documentElement.style.maxHeight) == "undefined");

            connect('add_feedback_link', 'onclick', function(e) {
                e.stop();
                removeElementClass('add_feedback_form', 'js-hidden');
                removeElementClass('add_feedback_form', 'hidden');

                if (isIE6) {
                    disconnectAll('add_feedback_form', 'onsubmit');
                }

                return false;
            });
        }
    }

    rewriteCancelButtons();
});
