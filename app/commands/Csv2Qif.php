<?php

namespace App\Commands;

use DateTime;
use StephenHarris\QIF\Transaction;
use StephenHarris\QIF\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Csv2Qif extends Command
{
    protected static $defaultName = 'convert:csvtoqif';

    protected function configure(): void
    {
        $this->setDescription('convert a Starling Bank CSV to QIF');
        $this->addArgument('inputfile', InputArgument::REQUIRED, 'CSV file to convert');
        $this->addArgument('outputfile', InputArgument::REQUIRED, 'QIF file to write');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inFile = $input->getArgument('inputfile');
        $outFile = $input->getArgument('outputfile');

        $inFh = fopen($inFile, 'r');
        $outFh = fopen($outFile, 'w');
        $qif = new Writer();

        // Get header row, and ignore
        $inRow = fgetcsv($inFh);

        while ($inRow = fgetcsv($inFh)) {
            $transaction = new Transaction(Transaction::BANK);
            $date = DateTime::createFromFormat('d/m/Y', $inRow[0]);
            ray($inRow[0]);
            ray($date);
            $transaction->setDate($date)
                ->setAmount($inRow[4])
                ->setDescription(
                    $inRow[1] . '(' . strtolower($inRow[3]) . ')/' . $inRow[2]
                );
            $qif->addTransaction($transaction);
        }

        fputs($outFh, $qif);

        fclose($inFh);
        fclose($outFh);

        return Command::SUCCESS;
    }
}