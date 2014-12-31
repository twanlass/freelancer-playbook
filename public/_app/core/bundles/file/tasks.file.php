<?php

use Symfony\Component\Finder\Finder as Finder;

class Tasks_file extends Tasks
{

	public function generateModal($config, $destination)
	{
		$vars = array(
			'server_files' => $this->getServerFiles($config, $destination),
			'destination'  => $destination
		);
		$template = File::get($this->getAddonLocation() . 'views/modal.html');

		return Parse::template($template, $vars);
	}


	private function getServerFiles($config, $destination)
	{
		$path = Path::assemble(BASE_PATH, $destination);

		$finder = new Finder();

		// Set folder location
		$finder->in($path);

		// Limit by depth
		$finder->depth('<' . array_get($config, 'depth', '1'));

		// Limit by file extension
		foreach (array_get($config, array('allowed', 'types'), array()) as $ext) {
			$finder->name("/\.{$ext}/i");
		}

		// Fetch matches
		$matches = $finder->files()->followLinks();

		// Build array
		$files = array();
		foreach ($matches as $file) {
			$filename = Path::trimSubdirectory(Path::toAsset($file->getPathname(), false));
			$display_name = ltrim(str_replace($path, '', $file->getPathname()), '/');

			$value = (Config::get('prepend_site_root_to_uploads', false)) 
			         ? '{{ _site_root }}' . ltrim($filename, '/')
			         : $filename;

			$files[] = compact('value', 'display_name');
		}

		return $files;
	}

}