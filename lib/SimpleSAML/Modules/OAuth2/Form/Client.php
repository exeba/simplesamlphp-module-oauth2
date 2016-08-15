<?php

/**
 * Editor for OAuth2 Client Registry
 *
 * @author Andreas Åkre Solberg <andreas@uninett.no>, UNINETT AS.
 * @package simpleSAMLphp
 */
namespace SimpleSAML\Modules\OAuth2\Form;


class Client
{
	protected function getStandardField($request, &$entry, $key, $multiline = false) {
		if (array_key_exists('field_' . $key, $request)) {
			$entry[$key] = $multiline ? explode( "\n", $request['field_' . $key] ) : $request['field_' . $key] ;
		} else {
			if (isset($entry[$key])) unset($entry[$key]);
		}
	}

	public function formToMeta($request, $entry = array(), $override = NULL) {
		$this->getStandardField($request, $entry, 'name');
		$this->getStandardField($request, $entry, 'description');
		$this->getStandardField($request, $entry, 'redirect_uri');
		$this->getStandardField($request, $entry, 'id');
		$this->getStandardField($request, $entry, 'secret');

		if ($override) {
			foreach($override AS $key => $value) {
				$entry[$key] = $value;
			}
		}
		
		return $entry;
	}

	protected function requireStandardField($request, $key) {
		if (!array_key_exists('field_' . $key, $request))
			throw new \Exception('Required field [' . $key . '] was missing.');
		if (empty($request['field_' . $key]))
			throw new \Exception('Required field [' . $key . '] was empty.');
	}

	public function checkForm($request) {
		$this->requireStandardField($request, 'name');
		$this->requireStandardField($request, 'description');
	}
	

	protected function header($name) {
		return '<tr ><td>&nbsp;</td><td class="header">' . $name . '</td></tr>';
		
	}
	
	protected function readonlyDateField($metadata, $key, $name) {
		$value = '<span style="color: #aaa">Not set</a>';
		if (array_key_exists($key, $metadata))
			$value = date('j. F Y, G:i', $metadata[$key]);
		return '<tr>
			<td class="name">' . $name . '</td>
			<td class="data">' . $value . '</td></tr>';

	}
	
	protected function readonlyField($metadata, $key, $name) {
		$value = '';
		if (array_key_exists($key, $metadata))
			$value = $metadata[$key];
		return '<tr>
			<td class="name">' . $name . '</td>
			<td class="data">' . htmlspecialchars($value) . '</td></tr>';

	}
	
	protected function hiddenField($key, $value) {
		return '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '" />';
	}
	
	protected function flattenLanguageField(&$metadata, $key) {
		if (array_key_exists($key, $metadata)) {
			if (is_array($metadata[$key])) {
				if (isset($metadata[$key]['en'])) {
					$metadata[$key] = $metadata[$key]['en'];
				} else {
					unset($metadata[$key]);
				}
			}
		}
	}

	protected function arrayField($metadata, $key, $name) {
		$value = '';
		if (array_key_exists($key, $metadata)) {
			$value = htmlspecialchars(implode("\n", $metadata[$key]));
		}

		return '<tr><td class="name">' . $name . '</td><td class="data">
		<textarea name="field_' . $key . '" rows="5" cols="50">' . $value . '</textarea></td></tr>';

	}


	protected function standardField($metadata, $key, $name, $textarea = FALSE) {
		$value = '';
		if (array_key_exists($key, $metadata)) {
			$value = htmlspecialchars($metadata[$key]);
		}
		
		if ($textarea) {
			return '<tr><td class="name">' . $name . '</td><td class="data">
			<textarea name="field_' . $key . '" rows="5" cols="50">' . $value . '</textarea></td></tr>';
			
		} else {
			return '<tr><td class="name">' . $name . '</td><td class="data">
			<input type="text" size="60" name="field_' . $key . '" value="' . $value . '" /></td></tr>';
			
		}
	}

	public function metaToForm($metadata) {
		
		return '<form action="registry.edit.php" method="post">' .		
			'<div id="tabdiv">' .
			'<ul>' .
			'<li><a href="#basic">Name and descrition</a></li>' . 
			'</ul>' .
			'<div id="basic"><table class="formtable">' .
				$this->standardField($metadata, 'name', 'Name of client') .
				$this->standardField($metadata, 'description', 'Description of client', TRUE) .
				$this->standardField($metadata, 'redirect_uri', 'Static/enforcing callback-url') .

				$this->hiddenField('field_client_id', $metadata['id']) .
				$this->hiddenField('field_client_id', $metadata['secret']) .
			'</table></div>' .
			'</div>' .
			'<input type="submit" name="submit" value="Save" style="margin-top: 5px" />' .
		'</form>';
	}
}