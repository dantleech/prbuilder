<?php

namespace PrBuilder\Console\Command\Pr;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Console\Input\InputArgument;
use PrBuilder\Console\Command\BaseCommand;

class ServeCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('pr:serve');
        $this->setDescription('Start inbuilt webserver');
        $this->addArgument('address', InputArgument::OPTIONAL, 'Address to server', 'localhost:8090');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $documentRoot = __DIR__ . '/../../../web';

        $output->writeln(sprintf("Server running on <info>http://%s</info>\n", $input->getArgument('address')));
        $output->writeln('Quit the server with CONTROL-C.');

        $builder = $this->createPhpProcessBuilder($input, $output);
        $builder->setWorkingDirectory($documentRoot);
        $builder->setTimeout(null);
        $process = $builder->getProcess();

        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $callback = function ($type, $buffer) use ($output) {
                $output->write($buffer);
            };
        } else {
            $callback = null;
            $process->disableOutput();
        }

        $process->run($callback);

        if (!$process->isSuccessful()) {
            $output->writeln('<error>Built-in server terminated unexpectedly</error>');

            if (OutputInterface::VERBOSITY_VERBOSE > $output->getVerbosity()) {
                $output->writeln('<error>Run the command again with -v option for more details</error>');
            }
        }

        return $process->getExitCode();
    }

    private function createPhpProcessBuilder(InputInterface $input, OutputInterface $output)
    {
        return new ProcessBuilder(array(PHP_BINARY, '-S', $input->getArgument('address') ));
    }
}
