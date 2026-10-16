<?php

namespace App\Controller;

use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Maxkain\EavBundle\Bridge\Doctrine\Query\EavQueryFactory;
use Maxkain\EavBundle\Options\EavOptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Stopwatch\Stopwatch;

class TestController extends AbstractController
{
    public function __construct(
        private EavQueryFactory $eavQueryFactory,
        private EavOptionsResolverInterface $eavOptionsResolver,
        private EntityManagerInterface $em,
        private Stopwatch $stopwatch
    ) {
    }

    #[Route('/', 'test')]
    public function test(Request $request): Response
    {
        $stopwatch = $this->stopwatch;
        $multiEnumFilter = [
            4 => [32, 33]
        ];

        $enumFilter = [
            4 => [33, 41],
            5 => [43, 51]
        ];
        $eavQueryFactory = $this->eavQueryFactory;
        $stopwatch->start('test');
        $multiEnumOptions = $this->eavOptionsResolver->resolve(MultiEnumEav::class);
        $enumOptions = $this->eavOptionsResolver->resolve(EnumEav::class);

        $qb = $this->em->getRepository(Product::class)->createQueryBuilder('e')->select('e.id, e.name');
        $eavQueryFactory
            ->addEavFilters($qb, 'e', $multiEnumOptions, $multiEnumFilter)
            ->addEavFilters($qb, 'e', $enumOptions, $enumFilter)
            ->addValueSelect($qb, 'e', 4, $multiEnumOptions, as: ':attributeName');

        $multiEnumAttribute4Name = $eavQueryFactory->getSelectAttributeName($multiEnumOptions, 4);
        $qb->addOrderBy($multiEnumAttribute4Name, \SortDirection::Ascending);
        $query = $qb->getQuery();
        $stopwatch->lap('test');
//        dd($qb->getDQL());

        $result = $query->getArrayResult();
        dump($stopwatch->stop('test')->getPeriods());
        dump(count($result));

        return $this->render('base.html.twig');
    }
}
