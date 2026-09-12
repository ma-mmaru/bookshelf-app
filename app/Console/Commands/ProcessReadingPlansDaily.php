<?php

namespace App\Console\Commands;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanDueDateNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessReadingPlansDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reading-plans:process-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '期日経過した読書計画のステータス更新とリマインダー通知の発火を行います。';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $activePlans = ReadingPlan::with(['user', 'book'])
            ->whereNotNull('target_date')
            ->where('status', '!=', ReadingPlanStatus::Completed)
            ->get();

        foreach ($activePlans as $plan) {
            $rawDate = $plan->getRawOriginal('target_date');
            if (! $rawDate) {
                continue;
            }

            $dueDate = Carbon::parse($rawDate)->startOfDay();

            $diffDays = (int) $today->diffInDays($dueDate, false);
            $bookTitle = $plan->book->title ?? '書籍';

            if ($diffDays === 3) {
                $plan->user->notify(new ReadingPlanDueDateNotification(
                    title: '【読書期日】3日前のお知らせ',
                    body: "「{$bookTitle}」の読書期日まであと3日です（期日: {$dueDate->format('Y-m-d')})。",
                    timing: 'three_days_before'
                ));
            }

            if ($diffDays === 0) {
                $plan->user->notify(new ReadingPlanDueDateNotification(
                    title: '【読書期日】本日が期日です。',
                    body: "本日が「{$bookTitle}」の読書期日です。",
                    timing: 'on_due_date'
                ));
            }

            if ($diffDays === -3) {
                $plan->user->notify(new ReadingPlanDueDateNotification(
                    title: '【読書期日】期日を3日超過しています。。',
                    body: "「{$bookTitle}」の読書期日（{$dueDate->format('Y-m-d')})を3日経過しました。",
                    timing: 'three_days_after'
                ));
            }
        }

        ReadingPlan::where('target_date', '<', $today)->whereIn('status', [ReadingPlanStatus::Planned, ReadingPlanStatus::InProgress])
            ->update(['status' => ReadingPlanStatus::Overdue]);

        $this->info('日次バッチ処理が正常に完了しました。');

        return Command::SUCCESS;
    }
}
