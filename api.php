<?php
 require_once 'class/Session.php';
 require_once 'class/Downloader.php';

 $session = Session::getInstance();

 if($session->is_logged_in())
 {
    if(isset($_GET['f']) && !empty($_GET['f']))
    {
      $f = $_GET['f'];

      if($f === 'background_jobs')
      {

        $ret_val = array(
          'background_jobs' => Downloader::background_jobs(), 
          'max_background_jobs' => Downloader::max_background_jobs(),
          'get_current_background_jobs' => Downloader::get_current_background_jobs()
        );

        echo json_encode($ret_val);
      }
    }
 }

?>

