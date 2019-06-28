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

function sendRequest(section, formId) {
    var elements = {};
    var formData = document.getElementById(formId).elements;

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
            alert(result.value);
        }
    });
}