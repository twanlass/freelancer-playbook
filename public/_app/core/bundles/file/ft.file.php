<?php
class Fieldtype_file extends Fieldtype
{

	public function render()
	{
		// Let's make sure they set an upload destination
		if (array_get($this->field_config, 'destination', false) === false) {
			throw new Exception("You need to set a destination for your File field.");
		}

		// Normalize the destination
		$this->destination = trim(array_get($this->field_config, 'destination'), '/') . '/';

		// Allow a string or an array, but we want an array
		$has_data = ($this->field_data != '');
		$this->field_data = Helper::ensureArray($this->field_data);

		// Clean up {{ _site_root }} and lack of leading slash existence
		foreach ($this->field_data as $i => $file) {
			$this->field_data[$i] = URL::tidy('/' . str_replace('{{ _site_root }}', '', $file));
		}

		// Whether or not to allow the browse existing files functionality
		$allow_browse = array_get($this->field_config, 'browse', true);

		// Resizing config
		if ($resize = array_get($this->field_config, 'resize')) {
			$resize['resize'] = true;
			$resize = http_build_query($resize);
		}

		// If we're in a subdirectory, prepend it to all the filenames
		if (($site_root = Config::getSiteRoot()) != '/') {
			foreach ($this->field_data as $i => $file) {
				$this->field_data[$i] = URL::assemble($site_root, $file);
			}
		}

		// Send data to the view
		$vars = array(
			'field_id'     => $this->field_id,
			'field_name'   => $this->fieldname,
			'tabindex'     => $this->tabindex,
			'has_data'     => $has_data,
			'field_data'   => $this->field_data,
			'field_config' => $this->field_config,
			'destination'  => $this->destination,
			'allow_browse' => $allow_browse,
			'server_files' => ($allow_browse) ? json_encode($this->tasks->generateModal($this->field_config, $this->destination)) : null,
			'file_thumb'   => URL::assemble(Config::getSiteRoot(), Config::get('admin_path'), 'themes', Config::get('admin_theme'), '/img/file.png'),
			'resize'       => $resize
		);
		$template = File::get($this->getAddonLocation() . 'views/fieldtype.html');

		return Parse::template($template, $vars);
	}


	public function process()
	{
		$data = json_decode($this->field_data);

		// Normalize paths if we are running in a subdirectory
		if (($site_root = Config::getSiteRoot()) != '/') {
			foreach ($data as $i => $file) {
				$data[$i] = preg_replace('#^' . $site_root . '#', '/', $file);
			}
		}

		// Turn an array with one key into a string
		if (count($data) == 1){
			$data = $data[0];
		}
		// Turn an empty array into an empty string
		elseif (count($data) == 0) {
			$data = '';
		}

		return $data;
	}

}
