<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Serializer\Serializer; 
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\XmlFileLoader;
use XMLReader;

/**
 * Class Name: XmlRead
 *
 * @package App\Command
 */
class XmlRead extends Command
{

    /**
     * @param $filePath  String 
     *
     * @return array
     */
    public function validateXM(String $filePath) : array
    {
        if (file_exists($filePath)) {

            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            if (!in_array($ext, array('xml'))) {
                return array('status'=>'1', 'message' =>'Error to load XML File.');
            }else{
                return array('status'=>'0', 'message' =>'Valid XML file.');
            }
            
        } else {
            return array('status'=>'1', 'message' =>'XML File not found.');
        }
    }


    /**
     * @param $filePath  String 
     *
     * GET XML DATA BY FILEPATH
     * @return array
     */
    public function getXMLDataAsArray(String $filePath) : array
    {

        $decoder = new Serializer([new ObjectNormalizer()],[new XmlEncoder()]);

        $xmlData = $decoder->decode(file_get_contents($filePath),'xml');

        if ( isset($xmlData['row'])  && count($xmlData['row']) > 0) {

            $header =  array_keys($xmlData['row'][0]);

            $xmlData = $this->FilterXMLData($xmlData['row']);

            return array('status'=>'0', 'header' => $header,'body' => $xmlData);

        }else{
            return array('status'=>'1', 'message' =>'No data found in XML file.');
        }
        
    }


    /**
     * @param $xmlData  array Filter XML Data
     *
     * @return array
     */
    public function FilterXMLData(array $xmlData) : array
    {
        $finaData = [];
        foreach ($xmlData as $file) {
            $result = [];
            array_walk_recursive($file, function($item) use (&$result) {
                $result[] = $item;
            });
            $finaData[] = $result;
        }
        return $finaData;
    }
}
