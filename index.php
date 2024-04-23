<?php
ini_set('html_errors', false);

require_once 'libraries/sage/sage.phar';
Sage::$displayCalledFrom = false;
Sage::$returnOutput = true;

$env = file_exists('.env') ? parse_ini_file('.env') : null;

$prettyPrintDefault = $env['PRETTY_PRINT_DEFAULT'] ?? false;
Sage::$expandedByDefault = $env['PRETTY_PRINT_EXPANDED'] ?? false;

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['code'] = trim($_POST['code']);
    $code = trim($_POST['code']);
    $hasStartPHPTag = strtolower(substr($code, 0, 5)) == '<?php';
    $hasEndPHPTag = substr($code, -2) == '?>';
    $usePrettyPrint = filter_var($_POST['usePrettyPrint'], FILTER_VALIDATE_BOOLEAN);

    if ($hasStartPHPTag) {
        $code = substr($code, 5);
    }

    if ($hasEndPHPTag) {
        $code = substr($code, 0, strlen($code) - 2);
    }

    $codeArr = explode('/** */', $code);
    $result = '';

    foreach ($codeArr as $key => $value) {
        if ($usePrettyPrint) {
            $output = eval($value);
            $result .= sage($output);
        } else {
            $result .= '<pre>';
            $result .= print_r(eval($value), true);
            $result .= '</pre>';
        }
    }

    $_SESSION['output'] = $result;
    die($result);
}
?>
<!doctype html>
<html lang="en">
<link rel="icon" href="favicon.ico" type="image/x-icon" />

<head>
    <meta charset="utf-8">
    <title>PHP Fiddle</title>
    <meta name="description" content="PHP Fiddle">
    <link rel="stylesheet" href="assets/css/phpfiddle.css">
</head>

<body>
    <div id="container">
        <div id="header">
            <div id="header_upper">
                <div id="run">
                    <button type="button" id="run_btn" onclick="runCode();">▶</button>
                    &nbsp;or press ctrl + enter to run.&nbsp;
                </div>
                <div id="instructions">
                    Use <span
                            id="separator"
                            class="run_highlight"
                            onclick="insertSeparator();"
                            role="button"
                            tabIndex="0">
                        /** */
                    </span> to separate outputs,&nbsp;
                    and use <span class="run_highlight">return</span> to generate each output
                </div>
                <div id="execution-time">
                    Execution Time
                </div>
            </div>
            <div id="header_lower">
                <div id="snippets">
                    <select name="snippetSelect" id="snippetSelect" onchange="loadSnippet();">
                        <option value="" selected disabled hidden>Load a snippet</option>
                        <?php
                        $directories = [
                            './snippets',
                            './user_snippets',
                        ];
                        $regex = "/.*\.txt$/";

                        foreach ($directories as $directory) {
                            $files = [];
                            foreach (scandir($directory) as $file) {
                                if (preg_match($regex, $file)) {
                                    echo '<option value="' . $directory . '/' . $file . '">' .
                                        str_replace('.txt', '', $file) .
                                        '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                    <span class="snippet_option">Replace</span>
                    <input type="checkbox" id="snippetMode" name="snippetMode" role="toggle">
                    <span class="snippet_option">Append</span>
                </div>
                <div id="options">
                    <input
                        type="checkbox"
                        id="usePrettyPrint"
                        name="usePrettyPrint"
                        role="switch"
                        <?php echo $prettyPrintDefault ? 'checked' : ''; ?>>
                    <label for="usePrettyPrint">Prettify output</label>
                </div>
            </div>
        </div>
        <div id="code-container">
            <div id="code"><?php echo htmlentities(@$_SESSION['code'] ?? ""); ?></div>
            <div id="console"><?php echo htmlentities(@$_SESSION['output'] ?? ""); ?></div>
        </div>
    </div>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        type="text/javascript"
        charset="utf-8">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.9.6/ace.js"
        type="text/javascript"
        charset="utf-8">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.9.6/mode-php.min.js"
        type="text/javascript"
        charset="utf-8">
    </script>
    <script src="assets/js/phpfiddle.js" type="text/javascript"></script>
</body>

</html>
