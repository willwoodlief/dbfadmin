<!DOCTYPE HTML>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?=$us_url_root ?>favicon.ico" />

    <link rel="stylesheet" href="../users/js/plugins/SlickGrid/css/smoothness/jquery-ui-1.11.3.custom.css" type="text/css"/>
    <link href="../users/js/plugins/SlickGrid/slick.grid.css" rel="stylesheet" type="text/css">
    <link href="../users/js/plugins/SlickGrid/examples/examples.css" rel="stylesheet" type="text/css">
    <title>Grid</title>
    <style>
        .slick-headerrow-column {
            background: #87ceeb;
            text-overflow: clip;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .slick-headerrow-column input {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .slick-column-name,
        .slick-sort-indicator {
            display: inline-block;
            float: left;
            margin-bottom: 100px;
        }

        .bgb-button {
            background-color: rgba(149, 110, 177, 0.92); /* Green */
            border: none;
            color: white;
            padding: 5px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
        }

        .bgb-button:hover {
            box-shadow: inset 0 0 0 1px #5b5b5b;
            background: rgba(113, 84, 138, 0.92);
            text-decoration: underline;
        }

        .hidefocus:focus {
            outline: none;
        }
    </style>
    <!-- NOTE from include -->