<?php
declare(strict_types=1);

namespace App\Datatable;

use App\Entity\Interfaces\IdentifiableInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

abstract class AbstractDatatable implements DatatableInterface
{
    public const DEFAULT_ITEMS_PER_PAGE = 5;

    // ajax request are only used if the total is more than the threshold. Note that without ajax, search and
    // order-actions are performed client-side only, which greatly improves performance for small datasets
    // but this also means that the server-side search/order functionality will be ignored.
    // If your datatable depends on specific server-side order/search functionality, you should set the threshold to 0.
    public const AJAX_THRESHOLD = 500;

    protected const USE_VOTER_CHECK = true;

    protected const DELETE_ACTION = true;
    protected const EDIT_ACTION = true;
    protected const SHOW_ACTION = true;

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var DatatableHeader[]
     */
    protected $headers;
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    // Array to hold values in getter methods - helps to prevent executing scripts/queries multiple times
    protected $getterValues = [];

    public function __construct(EntityManagerInterface $em, RouterInterface $router, RequestStack $requestStack, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->em = $em;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function useAjax(): bool
    {
        return $this->getTotal() > static::AJAX_THRESHOLD;
    }

    public function getDatatableOptions(): DatatableOptions
    {
        return $this->datatableOptions
            ?? $this->datatableOptions = new DatatableOptions($this->requestStack->getCurrentRequest(), static::DEFAULT_ITEMS_PER_PAGE);
    }

    abstract protected function buildHeaders();

    protected function buildActionHeader()
    {
        $this
            ->addHeader(null, [
                'value' => 'actions',
                'text' => '',
                'sortable' => false,
                'width' => '200px',
                'align' => DatatableHeader::ALIGN_END,
                'search' => null,
            ])
        ;
    }

    /**
     * @param $fieldName
     * @param array $options Only options that match the properties of the DatatableHeader are allowed
     * @return static
     */
    protected function addHeader($fieldName, array $options = [])
    {
        $this->headers[] = new DatatableHeader($fieldName, $options);
        return $this;
    }

    /**
     * @return DatatableHeader[]
     */
    public function getHeaders(): array
    {
        if (empty($this->headers)) {
            $this->buildHeaders();
            $this->buildActionHeader();
        }
        return $this->headers;
    }

    public function getItems(): array
    {
        if ($this->getterValues['items'] ?? false) {
            return $this->getterValues['items'];
        }
        $qb = $this->getQueryBuilder();
        $options = $this->getDatatableOptions();
        if ($options->getItemsPerPage() !== -1) {
            $qb->setMaxResults($options->getItemsPerPage());
            if ($options->getPage() > 1) {
                $qb->setFirstResult($options->getPage());
            }
        }

        $items = $qb->getQuery()->getScalarResult();

        return $this->getterValues['items'] = $this->addActionItems($items);
    }

    public function getTotal(): int
    {
        return $this->getterValues['total'] ??
            $this->getterValues['total'] = (int) $this->getQueryBuilder()
                ->select('count('.$this->qbAlias().'.id)')
                ->getQuery()
                ->getSingleScalarResult()
            ;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->getRepo()->createQueryBuilder($this->getPrefix());
        // make sure id is always included in item-data
        if (is_subclass_of($this->getEntity(), IdentifiableInterface::class, true)) {
            $idName = call_user_func([$this->getEntity(), 'getIdName']);
            $qb->select($this->getPrefix().'.'.$idName . ' as id');
        } else {
            $qb->select($this->getPrefix().'.id');
        }
        $this->qbAddSelects($qb);
        $options = $this->getDatatableOptions();
        if ($search = $options->getSearch()) {
            $this->qbAddSearch($qb, $search);
        }
        $sortDesc = $options->getSortDesc();
        foreach ($options->getSortBy() as $index => $sortBy) {
            // FIXME: dql currently cannot be sorted -> will cause an error
            $qb->orderBy($this->qbAlias() . '.' . $sortBy, $sortDesc[$index] ? 'DESC' : 'ASC');
        }
        return $qb;
    }

    protected function qbAddSelects(QueryBuilder $qb)
    {
        foreach ($this->getHeaders() as $headerItem) {
            if ($dql = $headerItem->geDql()) {
                $qb->addSelect($dql. ' as '. $headerItem->getField());
            } elseif ($value = $headerItem->getField()) {
                $qb->addSelect($this->qbAlias() . '.' . $value);
            }
        }
    }

    protected function qbAddSearch(QueryBuilder $qb, string $search)
    {
        $whereCriteria = Criteria::create();
        foreach ($this->getHeaders() as $header) {
            $comparison = $header->getSearchComparison($this->qbAlias(), $search);
            if($comparison) {
                $whereCriteria->orWhere($comparison);
            }
        }
        if ($whereCriteria->getWhereExpression()) {
            $qb->addCriteria($whereCriteria);
        }
        return $qb;
    }

    protected function qbAlias(): string
    {
        return $this->getPrefix();
    }

    public function getAjaxUrl(): string
    {
        return $this->router->generate($this->getRoute('result'));
    }

    protected function getRoute(string $suffix): string
    {
        return $this->getPrefix().'_'.$suffix;
    }

    /**
     * @return ObjectRepository|EntityRepository
     */
    protected function getRepo(): ObjectRepository
    {
        return $this->em->getRepository($this->getEntity());
    }

    abstract protected function getEntity(): string;

    protected function getPrefix(): string
    {
        $shortClassName = (new \ReflectionClass($this->getEntity()))->getShortName();
        return Inflector::tableize($shortClassName);
    }

    public function jsonSerialize()
    {
        return [
            'headers' => $this->getHeaders(),
            'options' => $this->getDatatableOptions(),
            'itemsPerPage' => $this->getDatatableOptions()->getItemsPerPage(),
            'ajaxUrl' => $this->getAjaxUrl(),
            'useAjax' => $this->useAjax(),
            'total' => $this->getTotal(),
            'items' => $this->getItems(),
        ];
    }

    protected function addActionItems(array $items): array
    {
        $entityClass = $this->getEntity();
        foreach ($items as &$item) {
            // using a reference, no extra queries are performed until the object's properties are actually being read.
            $object = $this->em->getReference($entityClass, (int) $item['id']);

            $item['actions'] = $this->getActionsForItem($item, $object);
        }
        return $items;
    }

    /**
     * @param array $item the item-data fetched from the database as associative array
     * @param object $object a reference the the entity-object.
     * @return array
     */
    protected function getActionsForItem(array $item, $object): array
    {
        $actions = [];
        if (static::SHOW_ACTION && $this->isActionGranted('view', $object)) {
            $actions['show'] = [
                'href' => $this->getActionRoute('show', $object),
                'color' => 'primary',
                'icon' => 'mdi-details',
            ];
        }
        if (static::EDIT_ACTION && $this->isActionGranted('edit', $object)) {
            $actions['edit'] = [
                'href' => $this->getActionRoute('edit', $object),
                'color' => 'warning',
                'icon' => 'mdi-pencil',
            ];
        }
        if (static::DELETE_ACTION && $this->isActionGranted('delete', $object)) {
            $actions['delete'] = [
                'href' => $this->getActionRoute('delete', $object),
                'color' => 'error',
                'icon' => 'mdi-delete',
            ];
        }
        return $actions;
    }

    protected function getActionRoute($routeNameSuffix, $object)
    {
        return $this->router->generate($this->getRoute($routeNameSuffix), $object);
    }

    protected function isActionGranted($voterAttributeSuffix, $object): bool
    {
        if (!static::USE_VOTER_CHECK) {
            return true;
        }
        return $this->isGranted($this->getPrefix().'_'.$voterAttributeSuffix, $object);
    }

    protected function isGranted($attribute, $object): bool
    {
        return $this->authorizationChecker->isGranted(strtoupper($attribute), $object);
    }
}