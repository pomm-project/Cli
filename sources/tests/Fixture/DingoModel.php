<?php

namespace Model\PommTest\PommTestSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\ModelTrait\ReadTrait;

use PommProject\Foundation\Where;

use Model\PommTest\PommTestSchema\AutoStructure\Dingo as DingoStructure;
use Model\PommTest\PommTestSchema\Dingo;

/**
 * DingoModel
 *
 * Model class for view dingo.
 *
 * @see Model
 */
class DingoModel extends Model
{
    use ReadTrait;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     * @return void
     */
    protected function __construct()
    {
        $this->structure = new DingoStructure;
        $this->flexible_entity_class = "\Model\PommTest\PommTestSchema\Dingo";
    }
}
