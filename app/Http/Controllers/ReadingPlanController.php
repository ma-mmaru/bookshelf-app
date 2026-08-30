<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use Illuminate\Http\Request;

class ReadingPlanController extends Controller
{
    public function index(Request $request)
    {
        $currentStatus = $request->query('status');

        $query = auth()->user()->readingPlans()->with('book');

        if ($request->filled('status')) {
            $query->where('status', $currentStatus);
        }

        $readingPlans = $query->latest()->paginate(10);

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }

    public function create()
    {
        $books = Book::all();
        return view('reading-plans.create', compact('books'));
    }

    public function store(StoreReadingPlanRequest $request)
    {
        $validated = $request->validated();

        $request->user()->readingPlans()->create([
            'book_id' => $validated['book_id'],
            'target_date' => $validated['target_date'],
            'status' => ReadingPlanStatus::Planned,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を作成しました。');
    }

    public function edit(ReadingPlan $plan)
    {
        $this->authorize('update', $plan);
        $books = Book::all();

        return view('reading-plans.edit', [
            'readingPlan' => $plan,
            'books' => $books,
        ]);
    }

    public function update(UpdateReadingPlanRequest $request, ReadingPlan $plan)
    {
        $validated = $request->validated();

        $plan->update([
            'target_date' => $validated['target_date'],
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を更新しました。');
    }

    public function destroy(ReadingPlan $plan)
    {
        $this->authorize('delete', $plan);

        $plan->delete();

        return redirect()->route('reading-plans.index')->with('success', '読書計画を削除しました。');
    }

    public function complete(ReadingPlan $plan)
    {
        $this->authorize('complete', $plan);

        $plan->update([
            'status' => ReadingPlanStatus::Completed,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読了に設定しました！');
    }
}