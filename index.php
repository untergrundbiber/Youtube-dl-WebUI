<?php
	require_once 'class/Session.php';
	require_once 'class/Downloader.php';
	require_once 'class/FileHandler.php';

	$session = Session::getInstance();
	$file = new FileHandler;

  $lastVideos = $file->lastVideos(3);

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
			<h1>Download</h1>
			<?php

				if(isset($_SESSION['errors']) && $_SESSION['errors'] > 0)
				{
					foreach ($_SESSION['errors'] as $e)
					{
						echo "<div class=\"alert alert-warning\" role=\"alert\">$e</div>";
					}
				}

			?>
			<form id="download-form" class="form-horizontal" action="index.php" method="post">					
				<div class="form-group">
					<div class="col-md-10">
						<input class="form-control" id="url" name="urls" placeholder="Link(s) separate with comma" type="text">
					</div>
					<div class="col-md-2">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="audio"> Audio Only
							</label>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary">Download</button>
			</form>
			<br>
			<div class="row">
				<div class="col-lg-6">
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">Info</h3></div>
						<div class="panel-body">
							<p>Free space : <?php echo $file->free_space(); ?></b></p>
							<p>Download folder : <?php echo $file->get_downloads_folder(); ?></p>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">Help</h3></div>
						<div class="panel-body">
							<p><b>How does it work ?</b></p>
							<p>Simply paste your video link in the field and click "Download"</p>
							<p><b>With which sites does it works ?</b></p>
							<p><a href="http://rg3.github.io/youtube-dl/supportedsites.html">Here</a> is the list of the supported sites</p>
							<p><b>How can I download the video on my computer ?</b></p>
							<p>Go to <a href="./list.php?type=v">List of videos</a>, choose one, right click on the link and do "Save target as ..." </p>
						</div>
					</div>
				</div>
			</div>
      <div class="row">
				<div class="col-lg-12">
					<div class="panel panel-info">
						<div class="panel-heading"><h3 class="panel-title">Last videos</h3></div>
						<div class="panel-body">
              <div class="container">
                <div class="row">
							    <?php
                    $i = 0;
                    foreach($lastVideos as $f)
				            {
                      echo "<div class=\"col-lg-4\">";
                      $video_url = $file->get_downloads_folder().'/'.$f["name"];
                      echo "<video id=\"preview".$i."\" class=\"video-js vjs-default-skin\" controls preload=\"none\"  width=\"320\" height=\"240\"  data-setup='{}'>
                        <source src=\"".$video_url."\" type='video/mp4'>
                        <p class=\"vjs-no-js\">
                          To view this video please enable JavaScript, and consider upgrading to a web browser
                          that <a href=\"http://videojs.com/html5-video-support/\" target=\"_blank\">supports HTML5 video</a>
                        </p>
                      </video><p>".$f["name"]."</p>";
                      
                      echo "</div>";
					            $i++;
				            }
                  ?>
						    </div>
						  </div>
						</div>
					</div>
				</div>        
			</div>
		</div>
<?php
	unset($_SESSION['errors']);
	require 'views/footer.php';
?>
