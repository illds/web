let x, y, r;
const dataType = 'html';

function chooseR(element) {
    r = element.value;
    [...document.getElementsByClassName("r-button")].forEach(function (btn) {
        btn.style.transform = "";
    });
    element.style.transform = "scale(1.1)";
}

function submit() {
    $('#errors').empty();
    y = document.querySelector("select[id=number]").value;
    if (validateX() & validateR()) {
        $.get("main.php", {
            'x': x,
            'y': y,
            'r': r,
            'timezone': new Date().getTimezoneOffset(),
            'wholeTable': false,
            'dataType': dataType
        }).done(function (data) {
            if (dataType === 'html') {
                $('#result-table tr:first').after(data);
            }
        });
    }
}

function generateRowFromElem(elem) {
    let newRow = elem.isHit ? '<tr class="hit-yes">' : '<tr class="hit-no">';
    newRow += '<td>' + elem.x + '</td>';
    newRow += '<td>' + elem.y + '</td>';
    newRow += '<td>' + elem.r + '</td>';
    newRow += '<td>' + elem.currentTime + '</td>';
    newRow += '<td>' + elem.execTime + '</td>';
    newRow += '<td>' + (elem.isHit ? 'Да' : 'Нет') + '</td>';

    return newRow;
}

function validateX() {
    x = document.querySelector("input[id=x-input]").value.replace(",", ".");
    if (x == undefined) {
        showError("Ошибка: Введите X");
        return false;
    }
    if (!isNumeric(x)) {
        showError("Ошибка: X не является числом");
        return false;
    }
    if (!((x > -5) && (x < 3))) {
        showError("Ошибка: X не входит в область допустимых значений");
        return false;
    }
    return true;
}

function validateR() {
    if (r)
        return true;

    showError("Ошибка: Выберите R");
    return false;
}

function showError(message) {
    $('#errors').append(`<li>${message}</li>`);
}

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}



