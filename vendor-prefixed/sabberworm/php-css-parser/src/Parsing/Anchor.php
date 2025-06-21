<?php

namespace Onepix\FoodSpotVendor\Sabberworm\CSS\Parsing;

/**
 * @internal since 8.7.0
 */
class Anchor
{
    /**
     * @var int
     */
    private $iPosition;

    /**
     * @var \Onepix\FoodSpotVendor\Sabberworm\CSS\Parsing\ParserState
     */
    private $oParserState;

    /**
     * @param int $iPosition
     * @param \Onepix\FoodSpotVendor\Sabberworm\CSS\Parsing\ParserState $oParserState
     */
    public function __construct($iPosition, ParserState $oParserState)
    {
        $this->iPosition = $iPosition;
        $this->oParserState = $oParserState;
    }

    /**
     * @return void
     */
    public function backtrack()
    {
        $this->oParserState->setPosition($this->iPosition);
    }
}
