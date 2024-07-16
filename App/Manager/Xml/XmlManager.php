<?php

namespace CMW\Manager\Xml;


use CMW\Manager\Manager\AbstractManager;
use CMW\Utils\File;
use SimpleXMLElement;

class XmlManager extends AbstractManager
{
    /**
     * @param string $file
     * @return false|\SimpleXMLElement
     * @desc Read and return xml file.
     */
    public function read(string $file): false|SimpleXMLElement
    {
        if (!is_file($file)) {
            return false;
        }

        $data = File::read($file);

        if (!$data) {
            return false;
        }

        return simplexml_load_string($data);
    }
}