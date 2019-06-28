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
        sendRequest('tasks', button, function (element) {
            $('#tasks').DataTable().row(element.closest('tr')).remove().draw();
        });
    }
}

function setTaskPriority(button) {
    sendRequest('tasks', button);
    $(button).closest('td').attr('data-sort', button.form.priority.value);
    $('#tasks').DataTable().cells().invalidate().draw();
}

function taskUnassignAgent(button) {
    sendRequest('tasks', button);
    $(button).closest('tr').outerHTML = "";
}

function deleteTask(button, onSuccess) {
    if (confirm('Really delete this task?')) {
        sendRequest('tasks', button, function (element) {
            if (onSuccess !== undefined) {
                onSuccess();
            }
            $('#tasks').DataTable().row(element.closest('tr')).remove().draw();
        });
    }
}

function sendRequest(section, button, onSuccess) {
    // hide all tooltips after an onclick action to prevent tooltips floating around with no button
    $("[data-toggle='tooltip']").tooltip('hide');

    var elements = {};
    var formData = button.form.elements;

    for (var i = 0; i < formData.length; i++) {
        if (formData[i].name.length === 0) {
            continue;
        }
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
                        onSuccess(button);
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
                        onSuccess(button);
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