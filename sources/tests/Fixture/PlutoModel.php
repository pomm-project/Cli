<?php

namespace Model\PommTest\PommTestSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\ReadQueries;

use PommProject\Foundation\Where;

use Model\PommTest\PommTestSchema\AutoStructure\Pluto as PlutoStructure;
use Model\PommTest\PommTestSchema\Pluto;

/**
 * PlutoModel
 *
 * Model class for materialized view pluto.
 *
 * @see Model
 */
class PlutoModel extends Model
{
    use ReadQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->structure = new PlutoStructure;
        $this->flexible_entity_class = '\Model\PommTest\PommTestSchema\Pluto';
    }
}
