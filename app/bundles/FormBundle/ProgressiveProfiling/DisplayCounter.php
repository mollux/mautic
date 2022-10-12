<?php

namespace Mautic\FormBundle\ProgressiveProfiling;

use Mautic\FormBundle\Entity\Field;
use Mautic\FormBundle\Entity\Form;

class DisplayCounter
{
    private int $displayedFields = 0;

    private int $alreadyAlwaysDisplayed = 0;

    /**
     * DisplayCounter constructor.
     */
    public function __construct(private Form $form)
    {
    }

    public function increaseDisplayedFields()
    {
        ++$this->displayedFields;
    }

    /**
     * @return int
     */
    public function getDisplayFields()
    {
        return $this->displayedFields;
    }

    public function increaseAlreadyAlwaysDisplayed()
    {
        ++$this->alreadyAlwaysDisplayed;
    }

    /**
     * @return int
     */
    public function getAlreadyAlwaysDisplayed()
    {
        return $this->alreadyAlwaysDisplayed;
    }

    /**
     * @return int
     */
    public function getAlwaysDisplayFields()
    {
        $i= 0;
        /** @var Field $field */
        foreach ($this->form->getFields()->toArray() as $field) {
            if ($field->isAlwaysDisplay()) {
                ++$i;
            }
        }

        return $i;
    }
}
