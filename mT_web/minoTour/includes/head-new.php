<head>
    <?php session_start(); ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
  <META HTTP-EQUIV="Expires" CONTENT="-1">
  <title>minoTour - real time minION analysis</title>
  <link rel="icon"
    type="image/png"
    href="images/minitour.png">


  <link rel="apple-touch-icon" sizes="57x57" href="images/minitour57.png" >
  <link rel="apple-touch-icon" sizes="72x72" href="images/minitour72.png" >
  <link rel="apple-touch-icon" sizes="114x114" href="images/minitour114.png" >
  <link rel="apple-touch-icon" sizes="144x144" href="images/minitour144.png" >
  <?php $whitelist = array('127.0.0.1', "::1"); ?>
  <?php if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){?>
      <link rel="mask-icon" href="images/minotour.svg" color="red">
  <?php
}else{
  ?>
      <link rel="mask-icon" href="images/minotour.svg" color="yellow">
  <?php
}
?>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
  <!-- DataTables -->
  <!--<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">-->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.12/cr-1.3.2/datatables.min.css"/>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="css/ion.rangeSlider.css">
  <link rel="stylesheet" href="css/ion.rangeSlider.skinModern.css">
  <link href="css/pnotify.custom.min.css" media="all" rel="stylesheet" type="text/css" />
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="dist/css/skins/skin-blue.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style type="text/css">
.ui-tabs .ui-tabs-hide {
     position: absolute;
     top: -10000px;
     display: block !important;
}
 </style>
 <script src="js/vue.min.js"></script>
 <script src="js/vue-filter.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.2.0/vue-resource.min.js"></script>
 <script src="js/reconnecting_websocket.min.js"></script>
</head>
<!--This is a test to see where this text appears - this site is in development.-->
