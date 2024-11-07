<?php

namespace CMW\Manager\Uploads\Format;

enum ImagesFormat: string
{
    case JPEG = 'jpeg';
    case JPG = 'jpg';
    case PNG = 'png';
    case GIF = 'gif';
    case WEBP = 'webp';
    case ICO = 'ico';
    case SVG = 'svg';
}
