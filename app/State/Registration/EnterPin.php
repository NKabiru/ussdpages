<?php

namespace App\State\Registration;

use App\UssdSession;
use App\UssdView;

class EnterPin implements State
{
    private $context;
    private $session;

    public function __construct(StateContext $context, UssdSession $session)
    {
        $this->context = $context;
        $this->session = $session;
        $this->session->update(['state' => static::class]);
    }

    public function input(string $input)
    {
        // TODO: Implement pin() method.

        $this->context->changeState(new ConfirmPin($this->context, $this->session));
    }

    public function view()
    {
        return "CON Enter your PIN";
    }

}
