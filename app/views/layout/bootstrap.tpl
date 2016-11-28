<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ADMIN CENTER</title>

<link href="/css/bootstrap.min.css" rel="stylesheet">
<!--<link href="/css/datepicker3.css" rel="stylesheet">-->
<link href="/css/styles.css" rel="stylesheet">


<!--[if lt IE 9]>
<script src="/js/html5shiv.js"></script>
<script src="/js/respond.min.js"></script>
<![endif]-->

<style>
.col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
    float: left;
}

.navbar-inverse {
    background-color: white;
    height:60px;
    border-bottom:1px solid #d4d9dd;
}
table td.center, table th.center{
    text-align:center;
}
.navbar-header a.navbar-brand , .navbar-header a.navbar-brand:hover, .navbar-header a.navbar-brand:focus {
    color: #333333;
    font-weight:bold;
    font-size:18px;
}

.navbar {
    min-height: 60px;
    margin-bottom: 0;
}

body {
    background: #f1f4f7;
    padding-top: 60px;
    color: #5f6468;
}

@media screen and (min-width: 768px) {
    .sidebar {
        top:60px;
    }
}
.sidebar {
    background-color:transparent;
}
.nav>li>a {
    xbackground: gray;
}
.navbar-brand {
    line-height: 30px;
}
a, a:hover, a:focus {
    color: #333333;
}
a {
    color: #333333;
    text-decoration: none;
}

.sidebar ul.nav ul.children li a {
    height: 40px;
    background: #f9fafb;
    color: #333333!important;
}

.xsidebar ul.nav {
    width:90%;
    margin:10px 0 10px 10px;
}
.sidebar ul.nav {
    width: initial;
    margin: 10px 10px 10px 10px;
}
.sidebar ul.nav li {
    background:white;
    border-bottom:1px solid #d4d9dd;
    border-left:1px solid #d4d9dd;
    border-right:1px solid #d4d9dd;
}


.sidebar ul.nav ul.children {
    xpadding:4px;
    background:transparent;
}


.sidebar ul.nav ul.children>li:last-child {
    height:41px;
    xborder-bottom-style: solid;
    border-bottom-width: 1px;
    border-bottom-color: #d4d9dd;
    border-bottom-right-radius: 4px;
    border-bottom-left-radius: 4px;
}


.sidebar ul.nav li.parent ul li a {
    border:0;
}

.sidebar ul.nav ul.children li {
    border:0;
}

.sidebar ul.nav>li:first-child {
    border-top-style: solid;
    border-top-width: 1px;
    border-top-color: #d4d9dd;
    border-top-right-radius: 4px;
    border-top-left-radius: 4px;
}

.sidebar ul.nav>li:last-child {
    border-bottom-style: solid;
    border-bottom-width: 1px;
    border-bottom-color: #d4d9dd;
    border-bottom-right-radius: 4px;
    border-bottom-left-radius: 4px;
}



.sidebar ul.nav ul.children li.active a {
    color: #fff;
    background-color: #e9ecf2;
}
.nav-tabs li a, .nav-tabs li a:hover, .nav-tabs li.active a, .nav-tabs li.active a:hover {
    border: 1px;
}
.nav-tabs {
    background: #f9f9f9;
    border-bottom: 1px solid #ddd;
}
.nav-tabs li.active a, .nav-tabs li.active a:hover {
    xbackground: #e9ecf2;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
}
.nav-tabs li a:hover {
    background: #e9ecf2;
}

label {
    font-weight:normal;
}
.help-block {
    margin-bottom: 0;
}
.material-switch > input[type="checkbox"] {
    display: none;
}

.material-switch > label {
    cursor: pointer;
    height: 0px;
    position: relative;
    width: 40px;
}

.material-switch > label::before {
    background: rgb(0, 0, 0);
    box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
    border-radius: 8px;
    content: '';
    height: 16px;
    margin-top: -8px;
    position:absolute;
    opacity: 0.3;
    transition: all 0.4s ease-in-out;
    width: 40px;
}
.material-switch > label::after {
    background: rgb(255, 255, 255);
    border-radius: 16px;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
    content: '';
    height: 24px;
    left: -4px;
    margin-top: -8px;
    position: absolute;
    top: -4px;
    transition: all 0.3s ease-in-out;
    width: 24px;
}
.material-switch > input[type="checkbox"]:checked + label::before {
    background: inherit;
    opacity: 0.5;
}
.material-switch > input[type="checkbox"]:checked + label::after {
    background: inherit;
    left: 20px;
}

h1, .h1, h2, .h2, h3, .h3 {
    margin-top: 0px;
    margin-bottom: 0px;
}

.navbar-inverse .navbar-toggle,
.navbar-inverse .navbar-toggle:hover,
.navbar-inverse .navbar-toggle:focus {
    background-color: #333;
}

.navbar-toggle {
    margin-top:13px;
}

.row {
    margin:0;
}
.container-fluid {
    margin-right:10px;
    margin-left:10px;
    padding:0;
}
.panel {
    margin:10px;
}
.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
    xposition: relative;
    min-height: 1px;
    padding: 0;
    xpadding-right: 10px;
    xpadding-left: 10px;
}

.breadcrumb {
    padding:10px;
}
.panel-body {
    padding:20px;
}
</style>
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Admin <span>CENTER</span></a>
                <!--
                <ul class="user-menu">
                    <li class="dropdown pull-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> User <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Profile</a></li>
                            <li><a href="#">Settings</a></li>
                            <li><a href="#">Logout</a></li>
                        </ul>
                    </li>
                </ul>
                -->
            </div>

        </div><!-- /.container-fluid -->
    </nav>

    <div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">

        <ul class="nav menu">
            {@row = menu}
                <li class="{?row.children}parent {/}{?row.checked}active {/}"><a {?row.url}href="{=row.url}"{/}>⚃ {=row.name}</a>
                {?row.children}
                <ul class="children">
                {@child = row.children}
                    <li class="{?child.checked}active {/}"><a {?child.url}href="{=child.url}"{/}>{=child.name}</a></li>
                {/}
                </ul>
                {/}

                </li>
            {/}
        </ul>

    </div><!--/.sidebar-->

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li class="active">home</li>
                {@row = menu}
                    {?row.checked}<li>{=row.name}</li>{/}
                    {@child = row.children}
                        {?child.checked}<li>{=child.name}</li>{/}
                    {/}
                {/}
            </ol>
        </div><!--/.row-->

<!--

        <div class="row">
            <div class="col-lg-12"> &nbsp;
                <h1 class="page-header">{=title}</h1>
            </div>
        </div>
-->

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="canvas-wrapper">
                            {# contents}
                        </div>
                    </div>
                </div>
            </div>

        </div><!--/.row-->

    </div>  <!--/.main-->

    <script src="/js/jquery-1.11.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <script>
//        $('#calendar').datepicker({});

        !function ($) {
            $(document).on("click","ul.nav li.parent > a > span.icon", function(){
                $(this).find('em:first').toggleClass("glyphicon-minus");
            });
            $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
        }(window.jQuery);

        $(window).on('resize', function () {
          if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
        })
        $(window).on('resize', function () {
          if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
        })
    </script>
</body>

</html>
