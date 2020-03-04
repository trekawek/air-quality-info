<?php

namespace {
    function jsLink($path, ...$attributes) {
        if (!file_exists($path)) {
            throw new Exception("Can't find file $path");
        }
        $hash = md5(file_get_contents($path));
        $attributes = implode(' ', $attributes);
        $result = "<script $attributes src=\"/$path?hash=$hash\"></script>\n";
        return $result;
    }

    function cssLink($path) {
        if (!file_exists($path)) {
            throw new Exception("Can't find file $path");
        }
        $hash = md5(file_get_contents($path));
        $result = "<link rel=\"stylesheet\" href=\"/$path?hash=$hash\"/>\n";
        return $result;
    }
}

?>