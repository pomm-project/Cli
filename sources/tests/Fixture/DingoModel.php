<?php

namespace Model\PommTest\PommTestSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\ReadQueries;

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
    use ReadQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new DingoStructure;
        $this->flexible_entity_class = '\Model\PommTest\PommTestSchema\Dingo';
    }
}
