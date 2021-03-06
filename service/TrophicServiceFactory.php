<?php

require_once 'TrophicServiceREST.php';
require_once 'TrophicServiceMock.php';

class UnknownTrophicServiceTypeException extends Exception {}

class TrophicServiceFactory 
{
    public static function createServiceOfType($type) 
    {
        $trophicService;
        switch ($type) {
            case 'REST':
                $trophicService = new TrophicServiceREST;
                break;

            case 'mock':
                $trophicService = new TrophicServiceMock;
                break;

            default: 
                throw new UnknownTrophicServiceTypeException('type [' . $type . '] not supported');
                break;
                
        }
        return $trophicService;
    }
}

?>
