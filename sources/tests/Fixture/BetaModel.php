<?php

namespace Model\PommTest\PommTestSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\ModelTrait\WriteTrait;

use PommProject\Foundation\Where;

use Model\PommTest\PommTestSchema\AutoStructure\Beta as BetaStructure;
use Model\PommTest\PommTestSchema\Beta;

/**
 * BetaModel
 *
 * Model class for table beta.
 *
 * @see Model
 */
class BetaModel extends Model
{
    use WriteTrait;

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
        $this->structure = new BetaStructure;
        $this->flexible_entity_class = "\Model\PommTest\PommTestSchema\Beta";
    }
}
