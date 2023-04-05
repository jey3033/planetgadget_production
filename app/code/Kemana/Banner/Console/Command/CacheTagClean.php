<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Console\Command;

use Kemana\Banner\Model\CleanCacheTags;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CacheTagClean
 * @package Kemana\Banner\Console\Command
 */
class CacheTagClean extends Command
{
    /**
     * @var CleanCacheTags
     */
    protected $cacheClean;

    /**
     * CacheTagClean constructor.
     * @param CleanCacheTags $cacheClean
     */
    public function __construct(
        CleanCacheTags $cacheClean
    ) {
        $this->cacheClean = $cacheClean;
        parent::__construct(null);
    }

    /**
     * Function configure
     */
    protected function configure()
    {
        $this->setName('tag-cache:clean')
            ->setDescription('Cache Tag Clean');
        parent::configure();
    }

    /**
     * Function execute
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Cleaning Cache Tags');
        $this->cacheClean->execute();
        $output->writeln("<info>Finished</info>");
    }
}
