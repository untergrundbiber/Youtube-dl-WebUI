<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Youtube-dl WebUI</title>
		<link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	</head>
  <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
  <script type="text/javascript">
    function format_background_jobs(j) {
      return j.background_jobs + "/" + j.max_background_jobs;
    }

    function format_menu(j) {
      html = ""
	    if(j.get_current_background_jobs != null)
	    {
        jobs = j.get_current_background_jobs
        for (i = 0, len = jobs.length; i < len; i++)
		    {
			    if (jobs[i]["music"]) 
			    {
            //Music
            html += "<li><a href=\"#\"><i class=\"fa fa-music\"></i> Elapsed time : "  + jobs[i]["time"] + "</a></li>";				    
			    }
			    else
			    {
            html += "<li><a href=\"#\"><i class=\"fa fa-video-camera\"></i> Elapsed time : " + jobs[i]["time"] + "</a></li>";
			    }
		    }

		    html += "<li class=\"divider\"></li>";
		    html += "<li><a href=\"./index.php?kill=all\">Kill all downloads</a></li>";
	    }
	    else
	    {
		    html += "<li><a>No jobs !</a></li>";
	    }

      return html;
    }

    function update_background_jobs() 
    {  
      $.ajax({
        url : "api.php",
        data : "f=background_jobs",
        complete : function (xhr, res) 
        {   
          j = JSON.parse(xhr.responseText);

          s = format_background_jobs(j);
          $("#background_jobs").html(s);

          s = format_menu(j);
          $("#background_jobs_menu").html(s);

          if (j.background_jobs > 0)
             $("#jobs").attr ( { style : "font-weight: bold" } );
          else
             $("#jobs").attr ( { style : "font-weight: normal" } );
          
        } 
      });  
    }
  </script>
	<body onload="window.setInterval(update_background_jobs,5000);update_background_jobs()">
		<div class="navbar navbar-default">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="">Youtube-dl WebUI</a>
			</div>
			<div class="navbar-collapse collapse navbar-responsive-collapse">
				<ul class="nav navbar-nav">
					<li><a href="./">Download</a></li>
					<li><a href="./list.php?type=v">List of videos</a></li>
					<li><a href="./list.php?type=m">List of musics</a></li>
					<?php
						if($session->is_logged_in())
						{
					?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" id="jobs" aria-expanded="false" onclick="update_background_jobs();">
            Background downloads : <span id="background_jobs">...</span>
            <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu" id="background_jobs_menu"></ul>
					</li>
					<?php
						}
					?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php
						if($session->is_logged_in())
						{
							echo "<li><a href=\"./logout.php\">Logout</a></li>";
						}
					?>
				</ul>
			</div>
		</div>



