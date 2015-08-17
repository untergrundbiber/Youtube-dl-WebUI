
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script>
function show_video(i) {
  var preview = $("#preview"+i)
  var video = document.getElementById("preview"+i); 

  if (preview.is(":visible")){
    preview.hide();
    video.pause()
  } else {
    preview.show();
    video.play();
  }
  return false;
}

</script>
<?php
	require_once 'class/Session.php';
	require_once 'class/Downloader.php';
	require_once 'class/FileHandler.php';

	$session = Session::getInstance();
	$file = new FileHandler;

	if(!$session->is_logged_in())
	{
		header("Location: login.php");
	}

	if(isset($_GET['type']) && !empty($_GET['type']))
	{
		$t = $_GET['type'];
		if($t === 'v')
		{
			$type = "videos";
			$files = $file->listVideos();
		}
		elseif($t === 'm')
		{
			$type = "musics";
			$files = $file->listMusics();
		}
	}

	if($session->is_logged_in() && isset($_GET["delete"]))
	{
		$file->delete($_GET["delete"], $t);
		header("Location: list.php?type=".$t);
	}

	require 'views/header.php';
?>
		<div class="container">
		<?php
			if(!empty($files))
			{
		?>
			<h2>List of available <?php echo $type ?> :</h2>
			<table class="table table-striped table-hover ">
				<thead>
					<tr>
						<th style="min-width:700px; height:35px">Title</th>
						<th style="min-width:80px">Size</th>
						<th style="min-width:80px">Preview</th>
						<th style="min-width:110px">Download link</th>
						<th style="min-width:110px">Delete link</th>
					</tr>
				</thead>
				<tbody>
			<?php
				$i = 0;
				$totalSize = 0;

				foreach($files as $f)
				{
          $video_url = $file->get_downloads_folder().'/'.$f["name"];
					echo "<tr>";
					echo "<td><a href=\"javascript:void(0)\" onclick=\"show_video(".$i.");\" >".$f["name"]."</a>";
          echo "<br/><video hidden id=\"preview".$i."\" class=\"video-js vjs-default-skin\" controls preload=\"none\"  width=\"320\" height=\"240\"  data-setup='{}'>
  <source src=\"".$video_url."\" type='video/mp4'>
  <p class=\"vjs-no-js\">
    To view this video please enable JavaScript, and consider upgrading to a web browser
    that <a href=\"http://videojs.com/html5-video-support/\" target=\"_blank\">supports HTML5 video</a>
  </p>
</video>";
          echo "</td>";
					echo "<td>".$f["size"]."</td>";
          echo "<td><a href=\"javascript:void(0)\" onclick=\"show_video(".$i.");\" class=\"btn btn-danger btn-sm\">Preview</a>";
          echo "<td><a href=\"".$video_url."\" download class=\"btn btn-danger btn-sm\">Download</a></td>";
					echo "<td><a href=\"./list.php?delete=$i&type=$t\" class=\"btn btn-danger btn-sm\">Delete</a></td>";
					echo "</tr>";
					$i++;
				}
			?>
				</tbody>
			</table>
			<br/>
			<br/>
		<?php
			}
			else
			{
				if(isset($t) && ($t === 'v' || $t === 'm'))
				{
					echo "<br><div class=\"alert alert-warning\" role=\"alert\">No $type !</div>";
				}
				else
				{
					echo "<br><div class=\"alert alert-warning\" role=\"alert\">No such type !</div>";
				}
			}
		?>
			<br/>
		</div><!-- End container -->
<?php
	require 'views/footer.php';
?>
