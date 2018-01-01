<?php

class document_link
{
    public
        $url,
        $name,
        $selected;

    /**
     *
     * @param string $name
     * @param string|url $url
     * @param bool $selected
     */
    function __construct($name, $url, $selected = false)
    {
        $this->name = $name;
        $this->url = (string)$url;
        $this->selected = $selected;
    }
}
