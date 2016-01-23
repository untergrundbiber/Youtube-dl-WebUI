<?php
require_once 'class/Session.php';
require_once 'class/Downloader.php';
require_once 'class/FileHandler.php';

$session = Session::getInstance();
$file = new FileHandler;

require 'views/header.php';

if(!$session->is_logged_in())
{
header("Location: login.php");
}
else
{
if(isset($_GET['kill']) && !empty($_GET['kill']) && $_GET['kill'] === "all")
{
Downloader::kill_them_all();
}

if(isset($_POST['urls']) && !empty($_POST['urls']))
{
$audio_only = false;

if(isset($_POST['audio']) && !empty($_POST['audio']))
{
$audio_only = true;
}

$downloader = new Downloader($_POST['urls'], $audio_only);

if(!isset($_SESSION['errors']))
{
header("Location: index.php");
}
}
}
?>
<div class="container">
<div class="row">
<div class="col-lg-12">
<div class="panel panel-info">
<div class="panel-heading"><h3 class="panel-title">Status</h3></div>
<div class="panel-body">
<table class="table table-striped table-hover ">
<thead>
<?php 
$downloader = new Downloader();
$status_files=$downloader->get_status_files();
?>
        <tr>
<th style="min-width:400px; height:35px">Title</th>
<th style="min-width:80px">Size</th>
<th style="min-width:200px">Percentage</th>
<th style="min-width:80px">Speed</th>
<th style="min-width:110px">ETA</th>
</tr>
      <?php  
     
foreach($status_files as $status_file)
{
//$data=$downloader->get_status_file($status_file);    

$status=$downloader->get_status($status_file);

if(is_array($status))
    {   
        if($status['percentage'] == '100.0%')
        {
              $status['eta']='Complete';
              
        }
        $status_file=str_replace('.json',"",$status_file);
                                $status_file=str_replace('status-',"",$status_file);    
        echo '<tr>
                <td>'.$status_file.'</td>
                <td>'.$status['size'].'</td>
                <td><div class="progress">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="'.str_replace("%","",$status['percentage']).'" aria-valuemin="0" aria-valuemax="100" style="width:'.$status['percentage'].' ;">
                                                            <span class="sr-only">'.$status['percentage'].' Complete</span>
                                                        </div>
                                                </div>'.$status['percentage'].'</td>
                <td>'.$status['speed'].'</td>
                <td>'.$status['eta'].'</td>
              </tr>';
    }
}
//var_Dump($downloader->get_status('e-ORhEE9VVg'));

 ?>

</thead>
<tbody>
<?php


?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<?php
unset($_SESSION['errors']);
require 'views/footer.php';
?>

