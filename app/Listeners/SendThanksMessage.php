<?php

namespace App\Listeners;

use App\Events\IntervalSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SendThanksMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(IntervalSubmitted $event): void
    {

        //
        $interval = $event->interval;
        $interval->load(['user','book']);

        $user_name = $interval->user->name;
        $mail = $interval->user->email;
        $book_name = $interval->book->title;

        $message = "
        Dear $user_name,
        Thanks you for submitting an reading interval
        for this $book_name
        Best,
        Koinz
        ";

        $data = [
            'mail'=>$mail,
            'user_name'=>$user_name,
            'book_name'=>$book_name,
            "message"=>$message
        ];

        Http::withOptions(
            [
                'connect_timeout' => env('CONNECTION_TIME_OUT',2),
                'read_timeout' => env('CONNECTION_TIME_OUT',2),
                'timeout' => env('CONNECTION_TIME_OUT',2)
            ])
            ->post(config('variables.message_provider') ,$data);
    }
}
