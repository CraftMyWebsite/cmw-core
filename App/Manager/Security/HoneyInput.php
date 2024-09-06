<?php

namespace CMW\Manager\Security;

class HoneyInput
{
    /**
     * @return void
     * @desc This function echo a honey pot (fake input)
     */
    protected function generateHoneyInput(): void
    {
        echo "<input type='text' name='honeyInput' value='' style='display: none; z-index: -99' />";
    }

    /**
     * @return bool
     * @desc Return true if honey pot is empty
     */
    protected function checkHoneyInput(): bool
    {
        return filter_input(INPUT_POST, 'honeyInput') === '';
    }
}
