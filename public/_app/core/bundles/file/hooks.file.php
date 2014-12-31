<?php
class Hooks_file extends Hooks
{

	public function file__thumbnail()
	{
		if ( ! $path = Request::get('path')) {
			exit('No path specified');
		}

		$template = "{{ transform src='{ path }' width='125' height='125' action='smart' }}";

		exit(Parse::template($template, compact('path')));
	}

}