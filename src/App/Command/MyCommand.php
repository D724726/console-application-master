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
            // Set Import XML Path

            $validate = $xml_Obj->validateXM($this->filePath);
            if($validate['status'] == '1'){

                $output->writeln('Error: '.$validate['message']);
                $ErrorLogs->errorLog('Error: '.$validate['message']);
                return Command::FAILURE;
            }


            $output->writeln('Start Import XML file....');

            // Read XML file
            $xmlData = $xml_Obj->getXMLDataAsArray($this->filePath);

            $output->writeln('XML file read successfully....');

            if ($xmlData['status'] === '0') {

                $output->writeln('Start creating CSV file.');

                $csv = new CSV();

                $csvFileName = $csv->exportCSV($xmlData['body'],$xmlData['header']);

                $output->writeln($csvFileName .' CSV file created successfully!');
            }

            if($xmlData['status'] === '1'){
                $output->writeln('Error: '.$xmlData['message']);
                $ErrorLogs->errorLog('Error: '.$xmlData['message']);
                
            }
            $output->writeln('EXIT....');

        } catch (Exception $e) {
            $ErrorLogs->errorLog($e->getMessage());
        }
        
        return Command::SUCCESS;
    }

}
