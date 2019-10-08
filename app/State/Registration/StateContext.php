<?php

namespace App\State\Registration;

use App\UssdSession;

class StateContext
{
    public $state;

    public function __construct(UssdSession $session)
    {
        $this->state = new Initial($this, $session);
    }

    public function input($input)
    {
        $this->state->input($input);
    }

    public function changeState(State $state)
    {
        $this->state = $state;
    }

    public function view()
    {
        return $this->state->view();
    }
}
