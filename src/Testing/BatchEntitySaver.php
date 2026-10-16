<?php

namespace App\Testing;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

class BatchEntitySaver
{
    protected int $batchSize = 30;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function save(
        ?SymfonyStyle $io,
        ?ProgressBar $progress,
        iterable $entities,
        string $entityFqcn,
        int $entitiesCount,
        int $count,
        bool $flush,
        callable $processor,
        ?callable $afterFlushAndClear = null,
    ): void {
        if ($io) {
            $t = microtime(true);
        }

        $em = $this->em;
        $i = 1;
        foreach ($entities as $entity) {
            if ($i > $count) {
                $em->remove($entity);
            } else {
                $processor($i, $entity);
            }

            if ($i % $this->batchSize == 0) {
                if ($flush) {
                    $em->flush();
                    $em->clear();
                    if ($afterFlushAndClear) {
                        $afterFlushAndClear();
                    }
                }
            }

            $progress?->advance();
            $i++;
        }

        if ($flush) {
            $em->flush();
        }

        for ($i = $entitiesCount + 1; $i <= $count; $i++) {
            $entity = new $entityFqcn;
            $processor($i, $entity);
            $em->persist($entity);
            if ($i % $this->batchSize == 0) {
                if ($flush) {
                    $em->flush();
                    $em->clear();
                    if ($afterFlushAndClear) {
                        $afterFlushAndClear();
                    }
                }
            }

            $progress?->advance();
        }

        if ($flush) {
            $em->flush();
            $em->clear();
            if ($afterFlushAndClear) {
                $afterFlushAndClear();
            }
        }

        $progress?->finish();

        $io?->info(round((microtime(true) - $t) * 1000) . ' ms');
        $io?->info(round(memory_get_peak_usage(true) / 1024 / 1024) . ' Mb');
    }
}
