<?php

namespace Model\PommTestSession\SchemaPommTest;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Model\PommTestSession\SchemaPommTest\AutoStructure\Beta as BetaStructure;
use Model\PommTestSession\SchemaPommTest\Beta;

/**
 * BetaModel
 *
 * Model class for table beta.
 *
 * @see Model
 */
class BetaModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new BetaStructure;
        $this->flexible_entity_class = '\Model\PommTestSession\SchemaPommTest\Beta';
    }
}
