<?php

namespace Running\Core;

/**
 * Standard class
 *
 * Class Std
 * @package Running\Core
 */
class Std
    implements IObjectAsArray, IHasMagicGetSet, IHasValidation, IHasSanitize
{
    use
        TStdGetSetWValidateSanitize;

    /**
     * Std constructor.
     * @param iterable|null $data
     */
    public function __construct(/* iterable */ $data = null)
    {
        if (null !== $data) {
            $this->fromArray($data);
        }
    }

    /**
     * @param \Running\Core\IArrayable|iterable $obj
     * @return \Running\Core\Std $this
     */
    public function merge(/* iterable */ $obj)
    {
        if ($obj instanceof IArrayable) {
            $obj = $obj->toArray();
        }
        foreach ($obj as $key => $value)
            $this->$key = $value;
        return $this;
    }

}