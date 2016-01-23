<?php

class Downloader
{
	private $urls = [];
	private $config = [];
	private $audio_only = false;
	private $errors = [];
	private $download_path = "";

	public function __construct($post=null, $audio_only=null)
	{
		$this->config = require dirname(__DIR__).'/config/config.php';
		$this->download_path = dirname(__DIR__).'/'.$this->config["outputFolder"];
		$this->audio_only = $audio_only;
		$this->urls = explode(",", $post);

		

		if(!$this->check_requirements($audio_only))
		{
			return;
		}



		foreach ($this->urls as $url)
		{
			if(!$this->is_valid_url($url))
			{
				$this->errors[] = "\"".$url."\" is not a valid url !";
			}
		}

		if(isset($this->errors) && count($this->errors) > 0)
		{
			$_SESSION['errors'] = $this->errors;
			return;
		}

		if($this->config["max_dl"] == 0)
		{
			$this->do_download();
		}
		elseif($this->config["max_dl"] > 0)
		{
			if($this->background_jobs() >= 0 && $this->background_jobs() < $this->config["max_dl"])
			{
				
				$this->do_download();
			}
			else
			{
				$this->errors[] = "Simultaneous downloads limit reached !";
			}
		}

		if(isset($this->errors) && count($this->errors) > 0)
		{
			$_SESSION['errors'] = $this->errors;
			return;
		}
	}

	public static function background_jobs()
	{
		return shell_exec("ps aux | grep -v grep | grep -v \"youtube-dl -U\" | grep youtube-dl | wc -l");
	}

	public static function max_background_jobs()
	{
		$config = require dirname(__DIR__).'/config/config.php';
		return $config["max_dl"];
	}

	public static function get_current_background_jobs()
	{
		exec("ps -A -o user,pid,etime,cmd | grep -v grep | grep -v \"youtube-dl -U\" | grep youtube-dl", $output);

		$bjs = [];

		if(count($output) > 0)
		{
			foreach($output as $line)
			{
				$line = explode(' ', preg_replace ("/ +/", " ", $line), 4);
				$bjs[] = array(
					'user' => $line[0],
					'pid' => $line[1],
					'time' => $line[2],
					'cmd' => $line[3]
					);
			}

			return $bjs;
		}
		else
		{
			return null;
		}
	}

	public static function kill_them_all()
	{
		exec("ps -A -o pid,cmd | grep -v grep | grep youtube-dl | awk '{print $1}'", $output);

		if(count($output) <= 0)
			return;

		foreach($output as $p)
		{
			shell_exec("kill ".$p);
		}

		$config = require dirname(__DIR__).'/config/config.php';
		$folder = dirname(__DIR__).'/'.$config["outputFolder"].'/';

		foreach(glob($folder.'*.part') as $file)
		{
			unlink($file);
		}
	}

	private function check_requirements($audio_only)
	{
		if($this->is_youtubedl_installed() != 0)
		{
			$this->errors[] = "Youtube-dl is not installed, see <a>https://rg3.github.io/youtube-dl/download.html</a> !";
		}

		$this->check_outuput_folder();

		if($audio_only)
		{
			if($this->is_extracter_installed() != 0)
			{
				$this->errors[] = "Install an audio extracter (ex: avconv) !";
			}
		}

		if(isset($this->errors) && count($this->errors) > 0)
		{
			$_SESSION['errors'] = $this->errors;
			return false;
		}

		return true;
	}

	private function is_youtubedl_installed()
	{
		exec("which youtube-dl", $out, $r);
		return $r;
	}

	private function is_extracter_installed()
	{
		exec("which ".$this->config["extracter"], $out, $r);
		return $r;
	}

	private function is_valid_url($url)
	{
		return filter_var($url, FILTER_VALIDATE_URL);
	}

	private function check_outuput_folder()
	{
		if(!is_dir($this->download_path))
		{
			//Folder doesn't exist
			if(!mkdir($this->download_path, 0775))
			{
				$this->errors[] = "Output folder doesn't exist and creation failed !";
			}
		}
		else
		{
			//Exists but can I write ?
			if(!is_writable($this->download_path))
			{
				$this->errors[] = "Output folder isn't writable !";
			}
		}
	}
	private function get_youtube_id($url)
	{
		$cmd = "youtube-dl --no-warnings --get-filename ".$url;
		$result=shell_exec($cmd);
$result=str_replace(" ","_", $result);
		return str_replace("\n", "", $result);
	}
	public function get_status_files()
	{
		$folder=$this->download_path.'/';
		$files=array();
		foreach(glob($folder.'*.json') as $file) {
				$file=str_replace($folder, "",$file);
 				 $files[]=$file;
			}
		
		return $files;
	}
	public function get_status_file($id)
	{
		$folder=$this->download_path.'/';
		$id=str_replace('.json',"",$id);
		$id=str_replace('status-',"",$id);
		$status_file="status-".$id.".json";
		$readfile=file_get_contents($folder.$status_file);
		return $readfile;
	}
	public function get_status($id)
	{
		 $status_file=$this->get_status_file($id);
		
		$pattern="/\[download\](.*)of(.*)at(.*)eta(.*)/i";
		preg_match_all($pattern, $status_file, $matches);
		unset($matches[0]);
		$matches['1']=str_replace(" ","",$matches['1']);
		$matches['2']=str_replace(" ","",$matches['2']);
		$matches['3']=str_replace(" ","",$matches['3']);
		$matches['4']=str_replace(" ","",$matches['4']);		
		$status=array();
		$status['percentage']=array_pop($matches[1]);
		$status['size']=array_pop($matches[2]);
		$status['speed']=array_pop($matches[3]);
		$status['eta']=array_pop($matches[4]);
		return $status;
	}
	private function do_download()
	{
		$cmd = "youtube-dl";
		$cmd .= " -o ".$this->config["outputFolder"]."/";
		$cmd .= escapeshellarg("%(title)s-%(uploader)s.%(ext)s");

		if($this->audio_only)
		{
			$cmd .= " -x ";
		}
        $cmd .=" -f best";
		foreach($this->urls as $url)
		{
			$cmd .= " ".$url;
			$id=$this->get_youtube_id($url);
		
        }

		$cmd .= " --restrict-filenames"; // --restrict-filenames is for specials chars
		$cmd .= " --newline > '".$this->download_path."/status-".$id.".json' & echo $!";
        
		shell_exec($cmd);
	}
}

?>
