<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 9:59
 */
namespace samsoncms;

use samsonframework\core\RenderInterface;
use samsonframework\pager\PagerInterface;
use samsonframework\orm\QueryInterface;

/**
 * Generic SamsonCMS application entities collection.
 * Class provide all basic UI interactions with database entities.
 *
 * @package samsoncms
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Collection extends \samsonos\cms\collection\Generic
{
    /** @var string Block view file */
    protected $indexView = 'www/collection/index';

    /** @var string Item view file */
    protected $itemView = 'www/collection/item/index';

    /** @var string Empty view file */
    protected $emptyView = 'www/collection/item/empty';

    /** @var  PagerInterface Pager object */
    protected $pager;

    /** @var array Collection for current collection entity identifiers */
    protected $entityIDs = array();

    /** @var array Collection of query handlers */
    protected $idHandlers = array();

    /** @var string Entity primary field name */
    protected $entityPrimaryField;

    /**
     * Generic collection constructor
     * @param RenderInterface $renderer View render object
     * @param QueryInterface $query Query object
     */
    public function __construct(RenderInterface $renderer, QueryInterface $query, PagerInterface $pager)
    {
        // Call parent initialization
        parent::__construct($renderer, $query);

        // Store pager object
        $this->pager = $pager;
    }

    /**
     * Pager id handler, will change input entity identifier collection to
     * match exact page.
     * @param array Array of material identifiers
     */
    protected function pagerIDInjection(& $entityIDs)
    {
        // Recount pager
        $this->pager->update(sizeof($entityIDs));

        // Apply Sorter before cutting array into  pages
        //$this->applySorter($entityIDs);

        // Cut only needed entity identifiers from array
        $entityIDs = array_slice($entityIDs, $this->pager->start, $this->pager->end);
    }

    /**
     * Add external identifier filter handler
     * @param callback $handler
     * @param array $params
     * @return self Chaining
     */
    public function handler($handler, array $params = array())
    {
        // Add callback with parameters to array
        $this->idHandlers[] = array($handler, $params);

        return $this;
    }

    /**
     * Call handlers stack
     * @param array $handlers Collection of callbacks with their parameters
     * @param array $params External parameters to pass to callback at first
     * @return bool True if all handlers succeeded
     */
    protected function callHandlers(& $handlers = array(), $params = array())
    {
        // Call external handlers
        foreach ($handlers as $handler) {
            // Call external handlers chain
            if (
                call_user_func_array(
                    $handler[0],
                    array_merge($params, $handler[1]) // Merge params and handler params
                ) === false
            ) {
                // Stop - if one of external handlers has failed
                return false;
            }
        }

        return true;
    }

    /**
     * Fill collection with data from database
     * @return $this Chaining
     */
    public function fill()
    {
        // Clear current entity identifiers
        $this->entityIDs = array();

        // If we have no external entity identifier handlers
        if (!sizeof($this->idHandlers)) {
            // Lets retrieve all possible entities identifiers from database
            $this->entityIDs = $this->query->fieldsNew($this->entityPrimaryField);
        } else {// First of all call all external entity identifier handlers
            $this->callHandlers($this->idHandlers, array(&$this->entityIDs));
        }

        // Secondly call sorting handlers

        // Call paging
        $this->pagerIDInjection($this->entityIDs);

        // Finally get all entity objects by their identifiers
        if (
            $this->query
            ->cond($this->entityPrimaryField, $this->entityIDs)
            ->fieldsNew($this->entityPrimaryField, $this->entityIDs)
        ) {
            // Retrieve all entities from database with passed identifiers
            $this->collection = $this->query->cond($this->entityPrimaryField, $this->entityIDs)->exec();
        }

        return $this;
    }
}
