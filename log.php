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
			<h1>Progress</h1>
			<?php

				if(isset($_SESSION['errors']) && $_SESSION['errors'] > 0)
				{
					foreach ($_SESSION['errors'] as $e)
					{
						echo "<div class=\"alert alert-warning\" role=\"alert\">$e</div>";
					}
				}

			?>
			
			<table class="table table-striped table-hover ">
				<thead>
					<tr>
						<th style="min-width:800px; height:35px">Title</th>
						<th style="min-width:80px">progress</th>
					</tr>
				</thead>
				<tbody>
			<?php
				$i = 0;
				$totalSize = 0;
				
				
				$files = $file->listLogFiles();

				$config = [];
				$config = require dirname(__DIR__).'/Youtube-dl-WebUI/config/config.php';
				foreach($files as $f)
				{
					echo "<tr>";
					echo "<td><a href='./log/" . $f["name"] . "'> " . $f["name"] . "</a></td>";	
					echo "<td>".$f["size"]."</td>";
					echo "</tr>";
					$i++;
				}
			?>
				</tbody>
			</table>
			<br/>
			<br/>
			<br/>

		</div>
<?php
	unset($_SESSION['errors']);
	require 'views/footer.php';
?>
