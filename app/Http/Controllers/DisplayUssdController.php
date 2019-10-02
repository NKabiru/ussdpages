<?php

namespace App\Http\Controllers;

use App\Jobs\CreateUserInDatabase;
use App\User;
use App\UssdSession;
use App\UssdView;
use Illuminate\Http\Request;

class DisplayUssdController extends Controller
{
    public function index(Request $request)
    {
        // Store the session
        $session = UssdSession::firstOrCreate([
            'session_id' => $request->sessionId,
            'phone_number' => $request->phoneNumber,
        ]);

        // Attach the session to a user for easier reference.
        if ($user = User::where('phone_number', $request->phoneNumber)->first()) {
            $session->user()->associate($user);
            $session->save();
        }

        // If session has a user, it means that user is registered. Display the login menu.
        if ($session->user) {
            if (! $session->currentView) {
                $view = UssdView::where('name','login')->first();
            } else {
                $view = $session->currentView->nextViews->first();
            }

            $session->currentView()->associate($view);
            $session->save();
        }

        // Convert text string into an array
        $textArray = explode('*', $request->text);

        // Check if user is registered
        if (! $session->user) {
            // If session has no current view, it means that this is the first prompt. Display the register-name view.
            if (! $session->currentView) {
                $view = UssdView::where('name', 'register-name')->first();
                $session->currentView()->associate($view);
                $session->save();
            } else {
                // Get the next view and save it as the new current view. If there is no next view, end the session.
                $nextViews = $session->currentView->nextViews;

                if ($nextViews->isNotEmpty()) {
                    $view = $nextViews->first();

                    // If the current view is confirm-pin, it should compare the two entered PINs if they match.
                    // If they match, create user in database.
                    $confirmPinView = UssdView::where('name', 'register-confirm-pin')->first();

                    if($session->currentView->is($confirmPinView)) {
                        if ($textArray[1] != $textArray[2]) {
                            $view = $nextViews->firstWhere('name', 'register-failure');
                        } else {
                            CreateUserInDatabase::dispatchNow([
                                'name' => $textArray[0],
                                'pin' => $textArray[1],
                                'phone_number' => $request->phoneNumber
                            ]);
                        }

                    }

                    $session->currentView()->associate($view);
                    $session->save();
                } else {
                    $session->delete();
                }

            }


        }

        return $view->body ?? "END There was a problem displaying the view";
    }
}