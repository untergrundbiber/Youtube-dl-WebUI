<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Youtube-dl WebUI</title>
		<link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
		<link rel="stylesheet" href="css/flowplayer.functional.css">
		<link rel="stylesheet" href="css/font-awesome.min.css">
	</head>
	<body>
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
					<li><a href="./list.php?type=m">List of songs</a></li>
					<?php if ($session->is_logged_in()): ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<?php if (Downloader::background_jobs() > 0) { echo "<b>"; } ?>
							Background downloads : <?php echo Downloader::background_jobs()." / ".Downloader::max_background_jobs();
                            if (Downloader::background_jobs() > 0) { echo "</b>"; } ?>
                            <span class="caret"></span></a>
                            
						<ul class="dropdown-menu" role="menu">
							<?php if (Downloader::get_current_background_jobs() != null): ?>
                                <?php foreach (Downloader::get_current_background_jobs() as $key): ?>
                                    <?php if (strpos($key['cmd'], '-x') !== false): ?>
                                        <li><a href=\"#\"><i class=\"fa fa-music\"></i> Elapsed time : "<?php echo $key['time']; ?>"</a></li>
                                    <?php else: ?>
                                        <li><a href=\"#\"><i class=\"fa fa-video-camera\"></i> Elapsed time : "<?php echo $key['time']; ?>"</a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <li class=\"divider\"></li>
                                <li><a href=\"./index.php?kill=all\">Kill all downloads</a></li>
                            <?php else: ?>
                                <li><a>No jobs !</a></li>
                            <?php endif; ?>
						</ul>
					</li>
					<?php endif; ?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php if ($session->is_logged_in()): ?>
                        <li><a href="./logout.php">Logout</a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
