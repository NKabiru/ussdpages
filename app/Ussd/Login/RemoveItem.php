<?php

namespace App\Ussd\Login;

use App\Jobs\RemoveItemFromDatabase;
use App\Ussd\State;
use App\Ussd\Traits\DeletesUssdSessions;
use App\Ussd\Traits\SavesInputHistory;
use App\UssdSession;

class RemoveItem implements State
{
    use DeletesUssdSessions, SavesInputHistory;

    protected $context;
    protected $session;

    public function __construct(MainContext $context, UssdSession $session)
    {
        $this->context = $context;
        $this->session = $session;

        $this->session->update(['state' => static::class]);
    }

    public function input(string $input)
    {
        return;
    }

    public function view()
    {
        $this->removeItem();
        $this->deleteSession();

        return "END You have successfully removed the item";
    }

    protected function removeItem()
    {
        $inputHistory = $this->session->input_history;

        RemoveItemFromDatabase::dispatchNow($this->session->user, [
            'name' => $inputHistory[RemoveItemName::class],
            'quantity' => $inputHistory[RemoveItemQuantity::class],
        ]);
    }
}
