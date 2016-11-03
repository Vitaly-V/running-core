<?php

namespace Running\Core;

/**
 * Full object-as-array access interface
 *
 * Interface IArrayAccess
 * @package Running\Core
 */
interface IObjectAsArray
    extends \ArrayAccess, \Countable, \Iterator, IArrayable
{

}