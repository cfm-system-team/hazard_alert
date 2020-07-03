<?php

namespace App\Jobs;

use App\Mail\GroupRegistered;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GroupImportMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $groups;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($groups)
    {
        $this->groups = $groups;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->groups as $group) {
            Mail::to($group['email'])->send(new GroupRegistered(['group' => $group]));
        }
    }
}
