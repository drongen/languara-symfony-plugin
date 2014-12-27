<?php

namespace Languara\SyncBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Languara\SyncBundle\Library;

class PushCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('languara:push')
            ->setDescription('Pushes your content from the local language directories to the Languara server.')
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
            $lib_languara->upload_local_translations();
        }
        catch(\Exception $e)
        {
            $output->writeln('<error>'. ($e->getMessage()) .'</error>');
            return;
        }
        
        $output->writeln('Content pushed to the server successfully!');
    }
}
