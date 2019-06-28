function expansionCheck(elementId) {
    var isHidden = $(elementId).is(":hidden");
    if (isHidden === true) {
        window.localStorage.setItem(elementId, "yes");
    } else {
        window.localStorage.setItem(elementId, "no");
    }
}

function checkOnLoading(elementId) {
    isExpanded = window.localStorage.getItem(elementId);
    if (isExpanded === "yes") {
        $(elementId).collapse("show");
    }
}

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

function archiveTask(button) {
    if (confirm('Really archive this task?')) {
        sendRequest('tasks', button.form, function (form) {
            $('#tasks').DataTable().row(form.closest('tr')).remove().draw();
        });
        $(button).tooltip.hide();
    }
}

function deleteTask(button) {
    if (confirm('Really delete this task?')) {
        sendRequest('tasks', button.form, function (form) {
            $('#tasks').DataTable().row(form.closest('tr')).remove().draw();
        });
        $(button).tooltip.hide();
    }
}

function sendRequest(section, form, onSuccess) {
    var elements = {};
    var formData = form.elements;

    for (var i = 0; i < formData.length; i++) {
        elements[formData[i].name] = formData[i].value;
    }

    $.ajax({
        url: 'ajax/' + section + '.php',
        type: 'post',
        dataType: 'json',
        contentType: 'application/json',
        data: JSON.stringify(elements),
        success: function (result) {
            if (result.messages.length === 0) {
                if (result.status === 0) {
                    $.toast({
                        title: 'Success!',
                        type: 'success',
                        delay: 2000
                    });
                    if (onSuccess !== undefined) {
                        onSuccess(form);
                    }
                } else {
                    $.toast({
                        title: 'Unknown Failure!',
                        type: 'error',
                        delay: 2000
                    });
                }
            } else {
                var type, title;
                if (result.status === 0) {
                    type = 'success';
                    title = 'Success!';
                    if (onSuccess !== undefined) {
                        onSuccess(form);
                    }
                } else {
                    type = 'error';
                    title = 'Error!';
                }
                var messages = "";
                for (var i = 0; i < result.messages.length; i++) {
                    messages += result.messages[i].message + "<br>";
                }
                $.toast({
                    title: title,
                    content: messages,
                    type: type,
                    delay: 5000
                });
            }
        }
    });
}