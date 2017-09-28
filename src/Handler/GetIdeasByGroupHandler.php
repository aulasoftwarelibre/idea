<?php
/**
 * Created by PhpStorm.
 * User: omarsotillo
 * Date: 28/09/17
 * Time: 11:24
 */

namespace App\Handler;

use App\Command\GetIdeasByGroupQuery;
use App\Repository\IdeaRepository;
use Pagerfanta\Pagerfanta;

class GetIdeasByGroupHandler
{
    /**
     * @var IdeaRepository
     */
    private $repository;

    /**
     * GetIdeaPaginatorHandler constructor.
     *
     * @param IdeaRepository $repository
     */
    public function __construct(IdeaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(GetIdeasByGroupQuery $query): Pagerfanta
    {
        $page = $query->getPage();

        $user = $query->getUser();
        $group = $user->getGroup();

        return $this->repository->findByGroup($group, $page);
    }
}