<?php
include "db.php";
if (!isset($_SESSION['uid']) || !isset($_SESSION['nama_divisi'])) {
  header('Location: index.php');
  exit;
}


?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Jekyll v4.1.1">
  <title>Inventori - Yoshinoya</title>

  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="css/bootstrap.css">
  <link href="css/dataTables.bootstrap4.min.css" />
  <link href="css/responsive.bootstrap4.min.css" />
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Quicksand" />
  <link href="css/select2.min.css" rel="stylesheet" />
  <link href="css/jquery-ui.css" rel="stylesheet">
  <link rel="stylesheet" href="css/sweetalert2.min.css">
  
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.dataTables.min.css">
</head>

<style>
  #loader {
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
    margin-left: 250px;
    margin-top: 250px;
  }

  @-webkit-keyframes spin {
    0% {
      -webkit-transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
    }
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }


  td[colspan] {
    text-align: left;
  }

  .modal-backdrop {
    width: 100% !important;
    height: 100% !important;
  }

  /* No extra space between cells */
  table {
    border-collapse: collapse;
  }

  th,
  td {
    border: 1px solid gray;
    margin: 0;
    padding: 3px 10px;
  }


  /* table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2} */

  .code a:link {
    color: #000;
  }

  /* unvisited link  */
  .code a:visited {
    color: #000;
  }

  /* visited link    */
  .code a:hover {
    color: #000;
  }

  /* mouse over link */
  .code a:active {
    color: #000;
  }

  /* selected link   */


  .ui-datepicker {
    font-size: 12pt !important;
  }

  .ui-widget {
    font-family: Trebuchet MS, Tahoma, Verdana, Arial, sans-serif;
    font-size: .9em;
  }

  body {
    font-family: Quicksand;
    zoom: 80%;
  }

  .bg-warning {
    background-color: #fd880c !important;
  }

  .btn-warning {
    color: #fff;
    background-color: #fd880c;
    border-color: #fd880c;
  }


  .navbar-light .navbar-nav .nav-link {
    color: #fff;
  }

  .navbar-light .navbar-brand {
    color: #fff;
  }

  .navbar-light .navbar-nav .active>.nav-link,
  .navbar-light .navbar-nav .nav-link.active,
  .navbar-light .navbar-nav .nav-link.show,
  .navbar-light .navbar-nav .show>.nav-link {
    color: #4a4a4b;
  }

  .navbar-light .navbar-brand:focus,
  .navbar-light .navbar-brand:hover {
    color: #4a4a4b;
  }


  .dataTables_filter {
    float: right;
  }

  .dataTables_paginate {
    float: right;
  }

  .form-label-group {
    position: relative;
    margin-bottom: 1rem;
  }


  .form-label-group>input,
  .form-label-group>label {
    height: 3.125rem;
    padding: .75rem;
  }

  .form-label-group>label {
    position: absolute;
    top: 0;
    left: 0;
    display: block;
    width: 100%;
    margin-bottom: 0;
    /* Override default `<label>` margin */
    line-height: 1.5;
    color: #495057;
    pointer-events: none;
    cursor: text;
    /* Match the input under the label */
    border: 1px solid transparent;
    border-radius: .25rem;
    transition: all .1s ease-in-out;
  }

  .form-label-group input::-webkit-input-placeholder {
    color: transparent;
  }

  .form-label-group input:-ms-input-placeholder {
    color: transparent;
  }

  .form-label-group input::-ms-input-placeholder {
    color: transparent;
  }

  .form-label-group input::-moz-placeholder {
    color: transparent;
  }

  .form-label-group input::placeholder {
    color: transparent;
  }

  .form-label-group input:not(:placeholder-shown) {
    padding-top: 1.25rem;
    padding-bottom: .25rem;
  }

  .form-label-group input:not(:placeholder-shown)~label {
    padding-top: .25rem;
    padding-bottom: .25rem;
    font-size: 12px;
    color: #777;
  }

  /* Fallback for Edge
-------------------------------------------------- */
  @supports (-ms-ime-align: auto) {
    .form-label-group>label {
      display: none;
    }

    .form-label-group input::-ms-input-placeholder {
      color: #777;
    }
  }

  /* Fallback for IE
-------------------------------------------------- */
  @media all and (-ms-high-contrast: none),
  (-ms-high-contrast: active) {
    .form-label-group>label {
      display: none;
    }

    .form-label-group input:-ms-input-placeholder {
      color: #777;
    }
  }


  .reversed {
    padding: 3rem 0 0 0;
  }

  .reversed .control-label {
    margin: -4.5rem 0 0 0;
    float: left;
  }

  input.is-valid~label {
    color: green;
  }

  input.is-invalid~label {
    color: red;
  }

  .topnav a:hover {
    border-bottom: 3px solid red;
  }

  .container1 {
    width: 90%;
    margin-left: 5%;
  }
</style>
</head>
<!--    oncontextmenu="return false" -->

<body>