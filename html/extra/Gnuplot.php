<?php
class Gnuplot{
	
	var $output_pipe;
	var $input_pipe;
	var $process;

	function __construct(){
		$descriptorspec = array(
		 0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
		 1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
		);

		//$this->process = proc_open("/usr/bin/gnuplot", $descriptorspec, $pipes);
		$this->input_pipe  = popen("/usr/bin/gnuplot", "w");//$pipes[0];
		$this->output_pipe = $pipes[1];

		$this->exec('set term png');
	}

	function __destruct(){
		$this->close();
	}

	function exec($command){
		fwrite($this->input_pipe, $command . '\n'); 
	}
	
	function result(){
		flush($this->input_pipe);
		//stream_get_contents($this->output_pipe);
	}

	function close(){
			flush($this->input_pipe);
			fclose($this->input_pipe);
			fclose($this->output_pipe);
			proc_close($this->process);
	}

}
