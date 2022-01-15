<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Command\CSV;
use App\Command\XmlRead;
use App\Command\ErrorLogs;

/**
 * Class MyCommand
 *
 * @package App\Command
 */
class MyCommand extends Command
{
    protected $filePath;
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this -> setName('importXML')
            -> setDescription('XML path.')
            -> setHelp('Enter XML path.')
            -> addArgument('importXML', InputArgument::REQUIRED, 'XML PATH REQUIRED..');
        
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorLogs = new ErrorLogs();
        
        try{

            $this->filePath = $input -> getArgument('importXML');

            $xml_Obj = new XmlRead();
            
            $validate = $xml_Obj->validateXM($this->filePath);// Set Import XML Path

            if($validate['status'] == '1'){

                $output->writeln('Error: '.$validate['message']);

                $errorLogs->errorLog('Error: '.$validate['message']);

                return Command::FAILURE;
            }


            $output->writeln('Start Import XML file....');

            $xmlData = $xml_Obj->getXMLDataAsArray($this->filePath);// Read XML file

            $output->writeln('XML file read successfully....');

            if ($xmlData['status'] === '0') {

                /***************** CSV File Generate Code *******************/

                $csv = new CSV();

                $output->writeln('Start creating CSV file.');

                $csvFileName = $csv->exportCSV($xmlData['body'],$xmlData['header']);

                $output->writeln($csvFileName .' CSV file created successfully!');

                /***************** END CSV File Generate Code *******************/
            }

            if($xmlData['status'] === '1'){

                $output->writeln('Error: '.$xmlData['message']);
                $errorLogs->errorLog('Error: '.$xmlData['message']);
                
            }
            $output->writeln('EXIT....');

        } catch (Exception $e) {
            $errorLogs->errorLog($e->getMessage());
        }
        
        return Command::SUCCESS;
    }

}
