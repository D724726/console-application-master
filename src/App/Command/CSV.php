<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Class Name: CSV
 *
 * @package App\Command
 */
class CSV extends Command
{
    // CSV File path
    private $csvFilePath    = "public";

    // CSV Name default empty
    private $csvName        = "";


    /**
     * Construct
     *  @param fileName string
     */
    public function __construct(string $fileName = null)
    {
        if ($fileName != NULL) {
           $this->csvName = $this->csvFilePath.'/sample_CSV_'.$fileName.'.csv';
        }else{
            $this->csvName = $this->csvFilePath.'/sample_CSV_'.time().'.csv';
        }
    }

    
    /**
     * @param $data  array - Data which store in CSV
     * @param $header  array -header of CSV
     *
     * @return string
     */
    public function exportCSV(array $data, array $header=array()):string
    {
        $csvfile = fopen($this->csvName, "w");

        // If header is set then add header
        if (!empty($header)) {
            fputcsv($csvfile, $header);
        }
        

        // Loop array which want to add in CSV
        foreach ($data as $line)
        {
            fputcsv($csvfile,$line,',');      
        }     
        
        fclose($csvfile);
        return $this->csvName; 
    }
}