<?php
function clean($elem) {
	if (!is_array($elem)) {
		$elem = trim(htmlentities($elem,ENT_QUOTES,"UTF-8"));
	} else {
		foreach ($elem as $key => $value) {
			$elem[$key] = clean($value);
		}
	}
	return $elem;
}

function includeIntoVar($file) {
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return false;
}

function redirectLocal($url = 'index.php') {
	redirect('http://pisi.era.ee/pbb/' . $url);
}

function redirect($url) {
	header('Location: ' . $url);
	ob_flush();
}