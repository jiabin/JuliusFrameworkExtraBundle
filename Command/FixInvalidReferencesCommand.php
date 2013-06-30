<?php

/*
 * This file is part of the JuliusFrameworkExtraBundle package.
 *
 * (c) Eymen Gunay <eymen@egunay.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Julius\FrameworkExtraBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;

class FixInvalidReferencesCommand extends ContainerAwareCommand
{
    /**
     * @var array
     */
    private $cache = array();

    /**
     * @var \Closure
     */
    private $printStatusCallback;

    /**
     * @var Doctrine\Common\Persistence\ManagerRegistry
     */
    private $registry;

    /**
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:mongodb:fix:invalid-references')
            ->setDescription('Fixes invalid references in given documents')
            ->addArgument('document', InputArgument::REQUIRED, 'Document name')
            ->addArgument('reference', InputArgument::REQUIRED, 'Reference name')
        ;
    }

    /**
     * @see Symfony\Bundle\FrameworkBundle\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->registry = $this->getContainer()->get('doctrine_mongodb');

        $this->printStatusCallback = function() {};
        register_tick_function(array($this, 'printStatus'));
    }

    /**
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $documentName = $input->getArgument('document');
        $results = $this->registry->getManager()->getRepository($documentName)->findAll();

        $numProcessed = 0;

        if (!$numTotal = $cursor->count()) {
            $output->writeln(sprintf('There are no "%s" documents to examine.', $collection->getName()));
            return;
        }

        $this->printStatusCallback = function() use ($output, &$numProcessed, $numTotal) {
            $output->write(sprintf("Processed: <info>%d</info> / Complete: <info>%d%%</info>\r", $numProcessed, round(100 * ($numProcessed / $numTotal))));
        };

        declare(ticks=2500) {
            foreach ($cursor as $document) {
                if (!isset($document[$referenceName]['$id'])) {
                    $output->writeln(sprintf('<error>"%s" document "%s" is missing a Foo reference</error>', $collection->getName(), $document['_id']));
                } elseif (!$this->isFooValid($document[$referenceName]['$id'])) {
                    $output->writeln(sprintf('<error>"%s" document "%s" references a nonexistent Foo "%s"</error>', $collection->getName(), $document['_id'], $document[$referenceName]['$id']));
                }

                ++$numProcessed;
            }
        }

        $this->printStatusCallback = function() {};
        $output->write(str_repeat(' ', 28 + ($numProcessed > 0 ? ceil(log10($numProcessed)) : 0)) . "\r");
        $output->writeln(sprintf('Examined <info>%d</info> "%s" documents.', $numProcessed, $collection->getName()));

        $size = memory_get_peak_usage(true);
        $unit = array('b', 'k', 'm', 'g', 't', 'p');
        $output->writeln(sprintf("Peak Memory Usage: <comment>%s</comment>", round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).$unit[$i]));
    }

    /**
     * Determines whether a Foo exists with this ID
     *
     * @param \MongoId $fooId
     * @return boolean
     */
    private function isFooValid(\MongoId $fooId)
    {
        if (!isset($this->cache[(string) $fooId])) {
            $this->cache[(string) $fooId] = (boolean) $this->fooCollection->count(array('_id' => $fooId));
        }

        return $this->cache[(string) $fooId];
    }

    /**
     * Get the MongoCollection for the given class
     *
     * @param string $class
     * @return \MongoCollection
     * @throws \RuntimeException if the class has no DocumentManager
     */
    private function getMongoCollectionForClass($class)
    {
        if (!$dm = $this->registry->getManagerForClass($class)) {
            throw new \RuntimeException(sprintf('There is no DocumentManager for class "%s"', $class));
        }

        return $dm->getDocumentCollection($class)->getMongoCollection();
    }

    /**
     * Invokes the print status callback
     *
     * Since unregister_tick_function() does not support anonymous functions, it
     * is easier to register one method (this) and invoke a dynamic callback.
     */
    public function printStatus()
    {
        call_user_func($this->printStatusCallback);
    }
}