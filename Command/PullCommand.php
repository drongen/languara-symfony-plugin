<?php

namespace Languara\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Languara\SyncBundle\Library;

/**
 * Hello World command for demo purposes.
 *
 * You could also extend from Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
 * to get access to the container via $this->getContainer().
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class PullCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('languara:pull')
            ->setDescription('Pull your content from the Languara server and adds it to the local lang directories.')
            ->setHelp(<<<EOF
<info>php %command.full_name%</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lib_languara = new Library\LanguaraWrapper($this->getContainer()->get('kernel')->getRootDir());
        
        try
        {
            $lib_languara->download_and_process();
        }
        catch(\Exception $e)
        {
           $output->writeln(($e->getMessage()));
           return;
        }
        
        $output->writeln('Content pulled from the server successuflly!');
    }
}