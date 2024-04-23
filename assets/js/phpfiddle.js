const editor = ace.edit("code");

editor.getSession().setMode({path:"ace/mode/php", inline:true});
editor.getSession().setOptions({tabSize: 4, useSoftTabs: false });
editor.focus();

const runCode = function() {
    const code = editor.getValue();
    const usePrettyPrint = document.getElementById("usePrettyPrint").checked;
    const startTime = performance.now();

    $.ajax({
        type: 'POST',
        data: {
            code: code,
            usePrettyPrint: usePrettyPrint
        },
        dataType: 'text',
        cache: false,
        success: function (result) {
            const endTime = performance.now();
            const executionTime = endTime - startTime;
            $('#execution-time').text('Execution Time: ' + executionTime.toFixed(2) + ' ms');
            $('#console').html(result);
        },
        error: function (error) {
            console.log (error.responseText);
            alert(error.responseText);
        }
    });
};

function loadSnippet() {
    
    let selectedSnippet = document.getElementById("snippetSelect").value;
    let snippetMode = document.getElementById("snippetMode").checked;

    fetch(selectedSnippet)
        .then(response => response.text())
        .then((data) => {
            if (snippetMode) {
                insertSeparator();
                editor.session.insert({
                    row: editor.session.getLength(),
                    column: 0
                }, data);
                editor.session.insert({
                    row: editor.session.getLength(),
                    column: 0
                }, "\n");
            } else {
                editor.setValue(data, 1);
            }

            editor.gotoLine(editor.session.getLength(), 0);
            editor.focus();
        })

    document.getElementById("snippetSelect").selectedIndex = 0;
};

function insertSeparator() {
    editor.session.insert({
        row: editor.session.getLength(),
        column: 0
    }, "\n\n/** */\n\n");

    editor.gotoLine(editor.session.getLength(), 0);
    editor.focus();
}

$(document).ready(function() {
    $('body').keydown(function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            runCode();
        }
    });
});
