<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stories;

class storySendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stories:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send story to group at set time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stories = \DB::table('stories')->where('publish_date', date('Y-m-d H:i', time()))->get();
        if ($stories) {
            foreach ($stories as $story) {
                if ($story->to_publish && empty($story->vk_id)) {
                    Stories::send($story->project_id, $story->id);
                }
                sleep(50);
            }
        }
        return false;
    }
}
